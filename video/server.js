var http = require('http');
const fs = require('fs-extra');
const url = require('url');
const tools = require('./tools')
const isNumber = require('is-number');





var server = http.createServer(function(req, res) {

    //parse the url to get the magnet and the user key
    var data = url.parse(req.url, true).query
    if (data.magnet && data.key && data.usr && data.movie) {
        //check if the user is log/valid , if good the stream start
        const db = tools.db_connect()
        try {
            tools.dl_n_stream(db, req, res, data);
        } catch (error) {
            console.error(error);
        }
    }
    else
        res.end()

});



var sub_server = http.createServer(function(req, res) {

    var data = url.parse(req.url, true).query

    if (data.id) {
        try {
           tools.get_subtitles(data.id)
        } catch (e) {
            console.log(e)
        }
        var interval = setInterval(function () {
            if (fs.existsSync('./video/subtitles/'+data.id+'/en.vtt')){
                console.log(fs.existsSync('./video/subtitles/'+data.id+'/en.vtt'))
                clearInterval(interval)
                res.end()
            }

        }, 100)
    }
    else
        res.end()
});



setInterval(function () {
    const db = tools.db_connect()
    $time = Math.round((Date.now() - 2592000000)/1000);
    //console.log("time = " + $time);
    //console.log("now = " + Math.round(Date.now()/1000))

    db.connect();
    db.query("SELECT path FROM film WHERE last_seen <" + $time, function (err, result, fields) {
        if (err)
            console.log(err)
        else {
            for (var i in result){
                var path =__dirname + "/dl/" + result[i]['path'];
                fs.remove(path.replace(/\s/g, ''))
            }

        }
    });
}, 86400); //86400 => tout les 24h


server.listen(8007);
sub_server.listen(8006);