<?php
if (!isset($_SESSION)) {
    session_start();
}

if (isset($_SESSION['loggued_on_user']))
        header("Location: ./view");

if (!isset($_SESSION['lang']))
    $_SESSION['lang'] = 'en';
if (isset($_GET['lang'])){
    if ($_GET['lang'] === 'fr')
        $_SESSION['lang'] = 'fr';
    if ($_GET['lang'] === 'en')
        $_SESSION['lang'] = 'en';
}

if (isset($_SESSION['lang'])){
    if ($_SESSION['lang'] === 'en')
        include_once 'controllers/en.php';
    if ($_SESSION['lang'] === 'fr')
        include_once 'controllers/fr.php';
}

?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hypertube | Torrent Streaming APP</title>
    <link rel="icon" type="image/png" href="view/assets/images/favico.png"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="view/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Lobster" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="view/assets/css/materialize.css">
    <script src="view/assets/js/materialize.js"></script>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
          integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
</head>

<body style="padding-top: 0px">

<div class="home_page row">
    <ul style="display: inline-flex">
        <?php
        $url_fr = $_SERVER['PHP_SELF'] . "?lang=fr";
        $url_en = $_SERVER['PHP_SELF'] . "?lang=en";
        echo "
            <li style='margin-right: 20px'><a href=\"$url_fr\"><img class=\"flag\" src=\"view/assets/images/fr.png\"></a></li>
            <li><a href=\"$url_en\"><img class=\"flag\" src=\"view/assets/images/en.png\"></a></li>            
            ";
        ?>
    </ul>
    <h1 class="logo_home fade-in one">
        Hypertube
    </h1>
    <div style="margin-top: 400px">
        <a href="view/register.php" class="waves-effect waves-light btn-large blue"><i
                    class="material-icons left">create</i><?php echo $signinbtn ?></a>
        <a href="view/login.php" class="waves-effect waves-light btn-large"><i
                    class="material-icons left">person</i><?php echo $signupbtn ?></a>
    </div>
</div>



<?php
if (isset($_SESSION['success2'])) {
    switch ($_SESSION['success2']) {
        case 1:
            $icon = 'fas fa-check';
            $message = 'Mise à jour effectuée.';
            break;
        case 2:
            $icon = 'fas fa-envelope';
            $message = 'Un email de confirmation vous à été envoyé !';
            break;
        case 3:
            $icon = 'fas fa-key';
            $message = 'Un nouveau mot de passe vous à été envoyé !';
            break;
        case 4:
            $icon = 'fas fa-check';
            $message = 'Vous êtes connecté !';
            break;
    }
    echo "
    <div class=\"quotes alert_notif\"><a class=\"success\"><i class=\"$icon icon_spacing\"></i>$message</a></div>
    ";
    unset($_SESSION['success2']);
} else if (isset($_SESSION['error'])) {
    switch ($_SESSION['error']) {
        case 1:
            $icon = 'fas fa-exclamation-triangle';
            $message = 'Nom d\'utilisateur trop long.';
            break;
        case 2:
            $icon = 'fas fa-exclamation-triangle';
            $message = 'Les mots de passe ne sont pas identiques.';
            break;
        case 3:
            $icon = 'fas fa-exclamation-triangle';
            $message = 'Votre mot de passe doit contenir au moins 1 caractère spécial.';
            break;
        case 4:
            $icon = 'fas fa-exclamation-triangle';
            $message = 'Votre mot de passe est trop court (6 caractères minimum).';
            break;
        case 5:
            $icon = 'fas fa-bomb';
            $message = 'Script détecté. Petit malin...';
            break;
        case 6:
            $icon = 'fas fa-exclamation-triangle';
            $message = 'Ce nom d\'utilisateur existe déjà.';
            break;
        case 7:
            $icon = 'fas fa-exclamation-triangle';
            $message = 'Cette adresse email existe déjà.';
            break;
        case 8:
            $icon = 'fas fa-times';
            $message = 'Ce compte n\'existe pas.';
            break;
        case 9:
            $icon = 'fas fa-exclamation-triangle';
            $message = 'Vous devez activer votre compte.';
            break;
        case 9:
            $icon = 'fas fa-exclamation-triangle';
            $message = 'Votre compte est déjà validé.';
            break;
        case 9:
            $icon = 'fas fa-exclamation-triangle';
            $message = 'La clé d\'activation ne correspond pas.';
            break;
        case 10:
            $icon = 'fas fa-exclamation-triangle';
            $message = 'Token d\'authentification invalide.';
            break;
    }
    echo "
    <div class=\"quotes alert_notif\"><a class=\"error\"><i class=\"$icon icon_spacing\"></i>$message</a></div>
    ";
    unset($_SESSION['error']);
}
?>

<script src="view/assets/js/materialize.js"></script>

<script>
    (function () {

        var quotes = $(".quotes");
        var quoteIndex = -1;

        function showNextQuote() {
            ++quoteIndex;
            quotes.eq(quoteIndex % quotes.length)
                .fadeIn(1000)
                .delay(3000)
                .fadeOut(1000);
        }

        showNextQuote();

    })();
</script>
</body>
</html>