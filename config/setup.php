<head>
    <meta charset="utf-8">
    <title>Hypertube | Torrent Streaming APP</title>
    <link rel="icon" type="image/png" href="../view/assets/images/favico.png"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../view/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Lobster" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../view/assets/css/materialize.css">
    <script src="../view/assets/js/materialize.js"></script>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
          integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
</head>

<style>
    h1 {
        color: #333;
        font-size: 40px;
    }

    body {
        display: block;
        width: 100%;
        height: 100%;
        font-family: 'Nunito', sans-serif;
        font-size: 16px;
        line-height: 32px;
        color: #7d93b2;
        background-color: #fafcff;
        font-weight: 200;
        margin: 0;
    }

    .lg-title, .lg {
        float: left;
        padding: 10px;
        font-size: 60px;
        padding-bottom: 15px;
    }

    .lg-title {
        background-color: #ff5c72;
        color: white;
    }

    .error-sql {
        position: absolute;
        right: 0;
        left: 0;
        top: 500px;
        color: #820000;
    }

    .success-sql {
        position: absolute;
        right: 0;
        left: 0;
        top: 500px;
        color: #2da977;
    }

</style>

<body style="padding-top: 0px">

<div class="home_page row">
    <h1 class="logo_home fade-in one">
        Hypertube
    </h1>
</div>

<div style="text-align: center !important;">
    <?php
    /**
     * Created by PhpStorm.
     * User: pimaglio
     * Date: 2019-02-06
     * Time: 14:28
     */

    require_once("database.php");
    //    require_once("../controllers/TorrentController.php");
    require_once '../vendor/autoload.php';
    include_once '../models/FilmModel.php';

    function find_torrent_validation()
    {
        $mov = new Film([]);
        $arr = $mov->recup_all_film();
        for ($i = 0; $i <= 199; $i++) {
            $json = file_get_contents('https://api-fetch.website/tv/movie/' . $arr[$i]['imdb_id']);
            if (!$s = stristr($json, 'magnet')) {
                $mov->delete_movie($arr[$i]['title']);
                continue;
            }
            $link = explode('"', $s)[0];
            $mov->add_torrent($link, $arr[$i]['title']);
        }
    }

    try {
        $db = database_connect();

        $sql_create_user_db_tbl = <<<EOSQL
CREATE TABLE if not exists user_db (
  id int(11) NOT NULL AUTO_INCREMENT,
  login varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  nom varchar(50) NOT NULL,
  password binary(64) NOT NULL COMMENT 'sha-256',
  email varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  creation_date date DEFAULT NULL,
  cle varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  pic VARCHAR(255) NOT NULL default '../upload/no-image.png',
  notif tinyint(1) DEFAULT NULL,
  status tinyint(1) DEFAULT 0,
  id_42 int(11) DEFAULT NULL,
  id_google varchar(25) DEFAULT NULL,
  valid tinyint(1) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE=utf8_unicode_ci
EOSQL;

        $sql_create_film_tbl = <<<EOSQL
CREATE TABLE IF NOT EXISTS film (
  id int (11) NOT NULL AUTO_INCREMENT,
  title varchar(255) NOT NULL,
  imdb_id varchar(255) NOT NULL,
  title_fr varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  creation_date int(11) NOT NULL,
  casting varchar(255),
  duree varchar(255) NOT NULL,
  note float NOT NULL,
  image varchar(255) NOT NULL,
  description varchar(5000) NOT NULL,
  description_fr varchar(5000) COLLATE utf8_unicode_ci NOT NULL,
  genres varchar(255),
  torrent varchar(1000),
  last_seen bigint DEFAULT NULL,
  path varchar(255) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE=utf8_unicode_ci
EOSQL;

        $sql_create_comment_tbl = <<<EOSQL
CREATE TABLE IF NOT EXISTS comment (
  id int (11) NOT NULL AUTO_INCREMENT,
  id_film int (11) NOT NULL,
  id_usr int (11) NOT NULL,
  commentaire varchar(255) not null,
  date varchar(25) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE=utf8_unicode_ci
EOSQL;

        $sql_create_history_tbl = <<<EOSQL
CREATE TABLE IF NOT EXISTS history (
  id int (11) NOT NULL AUTO_INCREMENT,
  id_film int (11) NOT NULL,
  id_usr int (11) NOT NULL,
  etat tinyint(1) DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE=utf8_unicode_ci
EOSQL;

        $sql_create_request_tbl = <<<EOSQL
CREATE TABLE IF NOT EXISTS request (
  id int (11) NOT NULL AUTO_INCREMENT,
  request varchar(255) NOT NULL,
  PRIMARY KEY (id)
)ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE=utf8_unicode_ci
EOSQL;


        $sql_create_user = <<<EOSQL
INSERT INTO user_db (login, nom, password, email, valid, cle)
VALUES 
  ('root', 'root', '4813494d137e1631bba301d5acab6e7bb7aa74ce1185d456565ef51d737677b2', 'root@root.com', '1', '123456789');
EOSQL;

        $msg = '';
        $msg_err = '';
        $r = $db->exec($sql_create_request_tbl);
        $film = new Film([]);
        if (!($arr = $film->recup_request(strtolower($title)))) {
            if ($r !== false) {
                $r = $db->exec($sql_create_user_db_tbl);
                $r = $db->exec($sql_create_user);
                $r = $db->exec($sql_create_film_tbl);
                $r = $db->exec($sql_create_comment_tbl);
                $r = $db->exec($sql_create_history_tbl);
                $r = $db->exec($sql_create_request_tbl);
                $token = new \Tmdb\ApiToken('eec9199c7c3efc546a7b7ea6d86ffcee');
                $client = new \Tmdb\Client($token);
                for ($i = 1; $i <= 10; $i++) {
                    $movie = $client->getMoviesApi()->getTopRated(array(
                        'page' => $i
                    ));
                    $movie_fr = $client->getMoviesApi()->getTopRated(array(
                        'page' => $i,
                        'language' => 'fr'
                    ));
                    foreach ($movie as $k => $v) {
                        if ($k == 'results') {
                            foreach ($v as $k1 => $v1) {
                                $id = $v1['id'];
                                $detail = $client->getMoviesApi()->getMovie($id);
                                $credits = $client->getMoviesApi()->getCredits($id);
                                $infos = [];
                                $infos['title'] = $detail['title'];
                                $infos['imdb'] = $detail['imdb_id'];
                                $infos['title_fr'] = $movie_fr[$k][$k1]['title'];
                                $infos['overview'] = $detail['overview'];
                                $infos['overview_fr'] = $movie_fr[$k][$k1]['overview'];
                                if (!empty($detail['release_date']))
                                    $infos['date'] = explode('-', $detail['release_date'])[0];
                                $infos['note'] = $detail['vote_average'];
                                $infos['time'] = $detail['runtime'] . 'min';
                                $infos['cast'] = NULL;
                                for ($j = 0; $j <= 4; $j++) {
                                    if (!isset($credits['cast'][$j]))
                                        break;
                                    if ($j != 4)
                                        $infos['cast'] .= $credits['cast'][$j]['name'] . ' / ';
                                    else
                                        $infos['cast'] .= $credits['cast'][$j]['name'];
                                }
                                $configRepository = new \Tmdb\Repository\ConfigurationRepository($client);
                                $config = $configRepository->load();
                                $imageHelper = new \Tmdb\Helper\ImageHelper($config);
                                $infos['img'] = 'http:' . $imageHelper->getUrl($detail['poster_path'], 'w500', 500, 80);
                                $genre = new \Tmdb\Repository\GenreRepository($client);
                                $gender_id = [];
                                $gender = [];
                                foreach ($movie[$k][$k1]['genre_ids'] as $kk => $vv)
                                    $gender_id[] .= $vv;
                                $infos['genres'] = NULL;
                                foreach ($gender_id as $kk => $vv) {
                                    $gender = $genre->load(intval($vv));
                                    $infos['genres'] .= $gender->getName() . ' ';
                                }
                                $seed = new Film($infos);
                                $seed->insert_film();
                            }
                        }
                    }
                }
                if ($r !== false) {
                    $msg = "Tables are created successfully!." . "<br>";
                } else {
                    $msg_err = "Error creating table." . "<br>";
                }

            } else {
                $msg_err = "Error creating table." . "<br>";
            }
            $film->add_request(strtolower($title));
            find_torrent_validation();
        }
        // display the message
        if ($msg != '') {
            echo "<h2 class='success-sql'>$msg<br><i class=\"far fa-smile-beam fa-9x\"></i></h2>" . "\n";
            $delai = 2;
            $url = '../index.php';
            header("Refresh: $delai;url=$url");
        } else if ($msg_err != '')
            echo "<h2 class='error-sql'>$msg_err<br><i class=\"far fa-sad-cry fa-9x\"></i></h2>" . "\n";

    } catch (PDOException $e) {
        $msg2 = $e->getMessage();
        echo "<br>" . "<h2 class='error-sql' >$msg2<br><i class=\"far fa-sad-cry fa-7x\"></i></h2>";
    }
    ?>
</div>


</body>