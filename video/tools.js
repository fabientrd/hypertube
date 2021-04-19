const torrentStream = require('torrent-stream');
const mysql = require('mysql');
const srt2vtt = require('srt-to-vtt')
const http = require('http');
const fs = require('fs');
const tools = require('./tools')
const OS = require('opensubtitles-api');
const pump = require('pump')
const path = require('path')
const ffmpegPath = require('@ffmpeg-installer/ffmpeg').path;
const ffmpeg = require('fluent-ffmpeg');
ffmpeg.setFfmpegPath(ffmpegPath)



function history(db, usr_id, film_id, name) {
    db.query("SELECT etat FROM history WHERE id_film="+film_id+" AND id_usr="+usr_id, function (err, result, fields){
        if (err)
            console.log(err)
        else
        {
            if (!result[0]){
                db.query("INSERT INTO history (id_film, id_usr, etat) VALUES ("+film_id+', '+usr_id+', 1)')
            }

        }
        db.query("UPDATE film SET last_seen="+Date.now()/1000+', path="'+name+'" WHERE id='+film_id)
    })
}



module.exports = {

// database connection
    db_connect: function () {
        try{
            var con = mysql.createConnection({
                host: "localhost",
                user: "root",
                password: "root",
                database: "hypertube"
            })
            return con
        } catch (err){
            console.log(err)
        }

    },

// user verification
    verif_user: function (db, key, callback) {
        try {
            db.connect()
            db.query("SELECT status FROM user_db WHERE cle=" + key, function (err, result, fields) {
                if (err)
                    console.log(err)
                else{
                    if (result[0])
                        return callback(result[0].status);
                    else
                        return callback(0);
                }
            });
        } catch (err){
            console.log(err)
        }
    },

// connect to OS api and get subtitles
    get_subtitles: function (imdb_id, res) {
        const OpenSubtitles = new OS('TemporaryUserAgent');

        OpenSubtitles.search(
            {
                imdbid: imdb_id
            }
        ).then(subtitles => {
            //console.log(subtitles);
            if (subtitles) {
                var dir = './video/subtitles/';
                var subdir = imdb_id + '/';
                var ext = '.vtt';

                // Create subtitles directory
                if (!fs.existsSync(dir)) {
                    fs.mkdirSync(dir);
                }

                // Create subtitles subdirectory
                if (!fs.existsSync(dir + subdir)) {
                    fs.mkdirSync(dir + subdir);
                }
                var response = ''
                // Download english subs
                if (subtitles.en) {
                    if (en_link = subtitles.en.url) {
                        let file = fs.createWriteStream(dir + subdir + 'en' + ext);
                        http.get(en_link, function (response) {
                            response.pipe(srt2vtt()).pipe(file);
                        });
                        response = 'en'
                    }
                }

                // Download french subs
                if (subtitles.fr) {
                    if (fr_link = subtitles.fr.url) {
                        let file = fs.createWriteStream(dir + subdir + 'fr' + ext);
                        http.get(fr_link, function (response) {
                            response.pipe(srt2vtt()).pipe(file);
                        });
                        response += ', fr'
                    }
                }
            } else {
                error = 'no subtitles found';
            }
        }).catch(console.error);
    },


// Download and stream
     dl_n_stream: function (db, req, res, data) {
        var engine = torrentStream(data.magnet, {path: './video/dl'});

        engine.on('ready', function () {
            engine.files.sort((a, b) => b['length'] - a['length']);
            var file = engine.files[0];
            history(db, data.usr, data.movie, file['path'].split('/')[0])
            const fileSize = file['length']
            const range = req.headers.range
            setTimeout(function () {
                console.log(engine.swarm.downloaded)
                if (engine.swarm.downloaded === 0){
                    res.writeHead(206, {'Content-Length': 0,});
                    res.end();
                    return;
                }
            }, 60000);
            if (range) {
                const ext = path.extname(file['name'])
                const parts = range.replace(/bytes=/, "").split("-")
                const start = parseInt(parts[0], 10)
                const end = parts[1]
                    ? parseInt(parts[1], 10)
                    : fileSize - 1
                const chunksize = (end - start) + 1
                const stream = file.createReadStream({'start': start, 'end': end})
                if (ext === '.mp4' || req.headers['user-agent'].indexOf('Chrome') > -1) {
                    const head = {
                        'Content-Range': `bytes ${start}-${end}/${fileSize}`,
                        'Accept-Ranges': 'bytes',
                        'Content-Length': chunksize,
                        'Content-Type': 'video/mp4',
                    }
                    res.writeHead(206, head);
                    pump(stream, res)
                }
                else{
                    let converted = ffmpeg(stream)
                        .withVideoCodec("libvpx")
                        .withVideoBitrate("2000")
                        .withAudioCodec("libvorbis")
                        .withAudioBitrate("256k")
                        .audioChannels(2)
                        .outputOptions([
                            "-preset ultrafast",
                            "-deadline realtime",
                            "-error-resilient 1",
                            "-movflags +faststart",
                        ])
                        .format("mp4");
                    const head = {
                       // 'Content-Length': fileSize,
                        'Content-Type': 'video/mp4',
                    }
                    res.writeHead(200, head);
                    res.on("close", () => {
                        converted.kill("SIGTERM")
                    });

                    pump(converted, res)
                }

            } else {
                const head = {
                    'Content-Length': fileSize,
                    'Content-Type': 'video/mp4',
                }
                res.writeHead(200, head)
                file.createReadStream().pipe(res)
            }
        });
    }
};