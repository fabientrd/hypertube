<?php

if (!isset($_SESSION))
    session_start();

include ('../controllers/ProfilsController.php');
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
        include_once '../controllers/en.php';
    if ($_SESSION['lang'] === 'fr')
        include_once '../controllers/fr.php';
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hypertube | Torrent Streaming App</title>
    <link rel="icon" type="image/png" href="assets/images/favico.png"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Lobster" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="assets/css/materialize.css">
    <script src="assets/js/materialize.js"></script>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
          integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <meta name="google-signin-scope" content="profile email">
    <meta name="google-signin-client_id" content="141074765115-n2mhte8kolbu2bm7d0lp19qcstdmpbff.apps.googleusercontent.com">
    <script src="https://apis.google.com/js/platform.js" async defer></script>
</head>
<body>

<nav class="fade-in one">
    <div class="nav-wrapper">
        <a href="../" class="brand-logo center logo_home"><i class="fas fa-film"></i>Hypertube</a>
        <ul class="right hide-on-med-and-down">
            <?php
            $url_fr = $_SERVER['PHP_SELF'] . "?lang=fr";
            $url_en = $_SERVER['PHP_SELF'] . "?lang=en";
            echo "
            <li><a href=\"$url_fr\"><img class=\"flag\" src=\"assets/images/fr.png\"></a></li>
            <li><a href=\"$url_en\"><img class=\"flag\" src=\"assets/images/en.png\"></a></li>            
            ";
            ?>
        </ul>
    </div>
</nav>
<div class="background">
    <svg viewbox="0 0 100 25">
        <path fill="#FFFFFF" d="M0 30 V12 Q30 17 55 12 T100 11 V30z" />
    </svg>
</div>

<?php
if (isset($_SESSION['success'])) {
    switch ($_SESSION['success']) {
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
        case 5:
            $icon = 'fas fa-check';
            $message = 'Votre compte est validé !';
            break;
    }
    echo "
    <div class=\"quotes alert_notif\"><a class=\"success\"><i class=\"$icon icon_spacing\"></i>$message</a></div>
    ";
    unset($_SESSION['success']);
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
        case 10:
            $icon = 'fas fa-times';
            $message = 'Mot de passe incorrect.';
            break;
        case 11:
            $icon = 'fas fa-exclamation-triangle';
            $message = 'Votre fichier doit être une image (JPG, JPEG, PNG & GIF)';
            break;
        case 12:
            $icon = 'fas fa-exclamation-triangle';
            $message = 'Votre image est trop lourde (>5mb)';
            break;
        case 13:
            $icon = 'fas fa-exclamation-triangle';
            $message = 'Une erreur est survenue durant l\'upload de votre image, veuillez réessayer plus tard';
            break;
    }
    echo "
    <div class=\"quotes alert_notif\"><a class=\"error\"><i class=\"$icon icon_spacing\"></i>$message</a></div>
    ";
    unset($_SESSION['error']);
}
?>

<script>
    (function () {

        var quotes = $(".quotes");
        var quoteIndex = -1;

        function showNextQuote() {
            ++quoteIndex;
            quotes.eq(quoteIndex % quotes.length)
                .fadeIn(1000)
                .delay(2000)
                .fadeOut(1000);
        }

        showNextQuote();

    })();
    $(document).ready(function () {
        $('.sidenav').sidenav();
    });
</script>
</body>
</html>