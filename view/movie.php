<?php
/**
 * Created by PhpStorm.
 * User: pimaglio
 * Date: 2019-02-12
 * Time: 11:59
 */

if (!isset($_SESSION))
    session_start();

if (!isset($_SESSION['loggued_on_user']))
    header("Location: ../index.php");

include('header_connect.php');

if (!isset($_GET['id']))
    header('Location: index.php');
else {
    $db_con = new film([]);
    if (empty($_GET['id'])) {
        $_SESSION['error'] = 11;
        header('Location: index.php');
    }
    if ($db_con->select_id($_GET['id']) == 0) {
        $_SESSION['error'] = 11;
        header('Location: index.php');
    }
}
?>

<body>
<?php
$db_con = new account([]);
$user = $db_con->array_user_id($_SESSION['id']);
$data = recup_single_film_arr($_GET['id']);
$key = $user['cle'];
if ($_SESSION['lang'] === 'en')
    $title = $data[0]['title'];
else
    $title = $data[0]['title_fr'];
if ($_SESSION['lang'] === 'en')
    $des = $data[0]['description'];
else
    $des = $data[0]['description_fr'];
$date = $data[0]['creation_date'];
$note = $data[0]['note'];
$id = $data[0]['id'];
$img = $data[0]['image'];
$dure = $data[0]['duree'];
$genres = $data[0]['genres'];
$casting = $data[0]['casting'];
$torrent = $data[0]['torrent'];
$comments = recup_com_film($id);
$usr = $_SESSION['id'];
$imdb = $data[0]['imdb_id'];
$tracks = '';

if (!file_exists("../video/subtitles/$imdb")){
    echo "<script src='http://localhost:8006?id=$imdb'></script>";
    header("Refresh: 0");
}
else{
    if (file_exists("../video/subtitles/$imdb/en.vtt"))
        $tracks .= "<track label=English kind=subtitles srclang=en src=../video/subtitles/$imdb/en.vtt >";
    if (file_exists("../video/subtitles/$imdb/fr.vtt"))
        $tracks .= "<track label=French kind=subtitles srclang=fr src=../video/subtitles/$imdb/fr.vtt >";
}



echo "
<div class=\"container fade-in two\">
    <div class=\"moviecontent row\">
        <div class=\"col s12 videocontent fade-in three\">
            <video class=\"videoplayer\" type=\"video/webm\" autoplay controls <video width=\"720\" height=\"405\" autoplay controls  poster=\"$img\"
                                                                                  src=\"http://localhost:8007?magnet=$torrent&key=$key&usr=$usr&movie=$id\">
                $tracks
            </video>
        </div>
        <div class=\"col s12 infocontent fade-in four\">
            <p class=\"playertitlemovie\">$title</p>
            <hr class=\"hrr\"/>

            <div class=\"col s12 fade-in five\">
                <p class=\"card_movie_year\"><i style=\"color: #D32F2F\" class=\"material-icons left\">movie</i>$date</p>
                <p class=\"rate_alt\"><i style=\"color: #ffab00\" class=\"material-icons left\">stars</i>$note / 10</p>
                <p class=\"rate_alt\"><i style=\"color: #2196f3\" class=\"material-icons left\">access_time</i>$dure</p>
                <p class=\"rate_alt\"><i style=\"color: #00c853\" class=\"material-icons left\">dashboard</i>$genres</p>
                <p class=\"rate_alt\"><i style=\"color: #c2185b\" class=\"material-icons left\">assignment_ind</i>$casting</p>
            </div>
            <div class=\"col s12 fade-in six\">
                <p class=\"playertitleinfo\"><i style=\"color: #ff6d00\" class=\"material-icons left\">assignment</i>$des</p>
            </div>
            <form name='comform' class=\"input-field col s12 fade-in two seven\">
                <input type='hidden' name='idurl' value='$id'>
                <input type='hidden' name='addcom' value='ok'>
                <i class=\"material-icons prefix\">mode_edit</i>
                <textarea name='comment' style='color: white' id=\"icon_prefix2\" class=\"materialize-textarea\" required></textarea>
                <label for=\"icon_prefix2\">$titlecom</label>
                <button id='commentButton' style='float: right;' class=\"btn-small waves-light\" name=\"action\">$titlebtncom
                    <i class=\"material-icons right\">send</i>
                </button>
            </form>
            <div id='comments' class=\"col s12 fade-in seven\">
            ";
            if (!empty($comments)){
                foreach ($comments as $k => $v){
                    $db_con = new account([]);
                    $user = $db_con->array_user_id($v['id_usr']);
                    $comms = $v['commentaire'];
                    $usercom = $user['login'];
                    $userdate = $v['date'];
                    echo "
                    <div class='comment'>
                        <p id='authorcom' class='playertitleinfo'><i style='color: #f4ff81' class='material-icons left'>account_box</i>$usercom ($userdate)</p>
                        <p id= 'comcontent' class='playertitleinfo'>$comms</p>
                    </div>
                    ";
                }
            }
                echo "
            </div>
        </div>
    </div>
</div>";
?>

<script>
    function addComment(e) {
        e.preventDefault();
        var fd = new FormData(document.forms["comform"]);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', "../controllers/FilmController.php");
        xhr.send(fd);
        document.getElementById("icon_prefix2").value= "";
    }

    document.getElementById("commentButton").addEventListener("click", (e) => addComment(e));
</script>

<script src="assets/js/materialize.js"></script>
</body>