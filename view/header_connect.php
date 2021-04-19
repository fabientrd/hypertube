<?php
require_once "../controllers/ProfilsController.php";
require_once "../controllers/FilmController.php";

if (!isset($_SESSION))
    session_start();

if (!isset($_SESSION['loggued_on_user']))
    header('Location: ../index.php');

if (isset($_GET['lang'])) {
    if ($_GET['lang'] === 'fr')
        $_SESSION['lang'] = 'fr';
    if ($_GET['lang'] === 'en')
        $_SESSION['lang'] = 'en';
}

if (isset($_SESSION['lang'])) {
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
    <title>Hypertube | Torrent Streaming APP</title>
    <link rel="icon" type="image/png" href="assets/images/favico.png"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Lobster" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="assets/css/materialize.css">
    <script src="assets/js/materialize.js"></script>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
          integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
</head>
<body>

<nav class="fade-in one">
    <div class="nav-wrapper">
        <a style="color: #E50812" href="../" class="brand-logo center logo_home"><i
                    class="fas fa-film"></i>Hypertube</a>
        <ul class="right hide-on-med-and-down">
            <?php
            if (isset($_GET['id'])) {
                $iduri = $_GET['id'];
                $url_fr = $_SERVER['PHP_SELF'] . "?id=$iduri" . "&lang=fr";
                $url_en = $_SERVER['PHP_SELF'] . "?id=$iduri" . "&lang=en";
            } else {
                $url_fr = $_SERVER['PHP_SELF'] . "?lang=fr";
                $url_en = $_SERVER['PHP_SELF'] . "?lang=en";
            }

            echo "
            <li><a href=\"$url_fr\"><img class=\"flag\" src=\"assets/images/fr.png\"></a></li>
            <li><a href=\"$url_en\"><img class=\"flag\" src=\"assets/images/en.png\"></a></li>            
            ";
            ?>
            <li><a href="logout.php"><i class="material-icons">power_settings_new</i></a></li>
        </ul>
        <a href="#" data-target="slide-out" class="sidenav-trigger show-on-large"><i class="material-icons">menu</i></a>
    </div>
</nav>
<ul id="slide-out" class="sidenav">
    <li>
        <div class="user-view">
            <div class="background">
                <img src="assets/images/bg_profil.png">
            </div>
            <?php
            $data = recup_user();
            $pic = $data['pic'];
            $nom = $data['nom'];
            $login = $data['login'];
            $email = $data['email'];
            $id = $data['id'];
            echo "
            <a href=\"profile.php?id=$id\"><img class=\"circle\" src=\"$pic\"></a>
            <a href=\"profile.php?id=$id\"><span class=\"white-text name\">$nom ($login)</span></a>
            <a href=\"profile.php?id=$id\"><span class=\"white-text email\">$email</span></a>
            ";
            ?>
        </div>
    </li>
    <li><a class="subheader"><?php echo $menutitle ?></a></li>
    <li><a href="search.php"><i class="material-icons">search</i><?php echo $menuitemSearch ?></a></li>
    <li><a href="index.php"><i class="material-icons">star</i><?php echo $menuitemPopular ?></a></li>
    <li>
        <div class="divider"></div>
    </li>
    <li><a class="subheader"><?php echo $menutitleparam ?></a></li>
    <li><a href="account.php"><i class="material-icons">settings</i><?php echo $menutitleparamAccount ?></a></li>
    <li><a href="logout.php"><i class="material-icons">power_settings_new</i><?php echo $menutitleparamLogout ?></a>
    </li>
    <li><a href="delete.php"><i class="material-icons">delete</i><?php echo $menutitleparamDelete ?></a></li>
</ul>

<!--NOTIFICATIONS-->

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
            $message = 'Votre compte est désormais actif !';
            break;
        case 6:
            $icon = 'fas fa-check';
            $message = 'Vos informations ont bien été mises à jour !';
            break;
        case 7:
            $icon = 'fas fa-check';
            $message = 'Utilisateur signalé!';
            break;
        case 8:
            $icon = 'fas fa-check';
            $message = 'Utilisateur bloqué!';
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
            $icon = 'fas fa-exclamation-triangle';
            $message = 'Tu n\'as pas matcher avec cette personne.';
            break;
        case 11:
            $icon = 'fas fa-bomb';
            $message = 'Toi tu veux tout casser petit malin...';
            break;
        case 12:
            $icon = 'fas fa-exclamation-triangle';
            $message = 'Utilisateur deja signalé';
            break;
        case 13:
            $icon = 'fas fa-exclamation-triangle';
            $message = 'Votre fichier doit être une image (JPG, JPEG, PNG & GIF)';
            break;
        case 14:
            $icon = 'fas fa-exclamation-triangle';
            $message = 'Votre image est trop lourde (>5mb)';
            break;
        case 15:
            $icon = 'fas fa-exclamation-triangle';
            $message = 'Une erreur est survenue durant l\'upload de votre image, veuillez réessayer plus tard';
            break;

    }
    echo "
    <div class=\"quotes alert_notif\"><a class=\"error\"><i class=\"$icon icon_spacing\"></i>$message</a></div>
    ";
    unset($_SESSION['error']);
}
if (isset($_SESSION['match'])) {
    echo "<div id=\"popup_match\" class=\"notif_match fade-in two\">
    <div class=\"popup_match\">
        <span class=\"btn_close\" onclick=\"closepopup()\">
            <i class=\"close material-icons left\">close</i>
        </span>

        <svg class=\"heart\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 512 612\">
            <path class=\"heart__path\" d=\"M472.08 81.12c-26.96-27.572-63.022-42.757-101.546-42.757-37.93 0-73.593 14.773-100.421 41.602l-13.846 13.855-13.859-13.859c-26.826-26.826-62.482-41.599-100.401-41.599-37.931 0-73.593 14.773-100.417 41.599-26.82 26.819-41.59 62.478-41.59 100.409s14.77 73.59 41.59 100.409l177.482 177.481c10.253 10.251 23.717 15.377 37.184 15.377 13.464-.001 26.931-5.126 37.181-15.376l176.339-176.323c55.385-55.387 56.419-145.472 2.304-200.818zm-20.897 182.226l-176.338 176.325c-10.251 10.25-26.931 10.25-37.181-.001l-177.483-177.484c-21.854-21.853-33.889-50.909-33.889-81.817s12.035-59.964 33.889-81.818c21.858-21.859 50.918-33.897 81.826-33.897 30.896 0 59.95 12.038 81.809 33.897l13.866 13.866-21.233 21.249c-5.132 5.136-5.128 13.459.007 18.591 2.567 2.565 5.929 3.847 9.292 3.847 3.367 0 6.732-1.286 9.299-3.854l53.658-53.7c21.859-21.859 50.918-33.897 81.826-33.897 31.389 0 60.775 12.376 82.747 34.847 44.096 45.097 43.155 118.597-2.095 163.846zM466.29 174.403c-1.445-23.38-11.35-45.366-27.891-61.909-11.712-11.712-26.245-20.222-42.026-24.615-6.995-1.946-14.242 2.147-16.188 9.142-1.946 6.994 2.147 14.242 9.142 16.188 11.427 3.179 21.967 9.36 30.482 17.875 12.004 12.004 19.192 27.963 20.241 44.938.431 6.971 6.218 12.336 13.109 12.336.272 0 .547-.008.823-.026 7.246-.445 12.756-6.682 12.308-13.929z\"></path>
        </svg>

        <h1 class=\"matching_font\">It's a Matcha !</h1>
    </div>
    </div>";
    unset($_SESSION['match']);
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
                .delay(3000)
                .fadeOut(1000);
        }

        showNextQuote();

    })();
    $(document).ready(function () {
        $('.sidenav').sidenav();
    });

    function closepopup() {
        document.getElementById('popup_match').style.display = "none";
    }
</script>
</body>
</html>