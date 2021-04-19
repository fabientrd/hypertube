<?php
/**
 * Created by PhpStorm.
 * User: pimaglio
 * Date: 2019-02-12
 * Time: 11:59
 */

session_start();

if (isset($_SESSION['loggued_on_user']))
    header("Location: index.php");

include('header_alt.php');

if (isset($_SESSION['lang'])) {
    if ($_SESSION['lang'] === 'en')
        include_once '../controllers/en.php';
    if ($_SESSION['lang'] === 'fr')
        include_once '../controllers/fr.php';
}
?>

<body>

<div id="background">
</div>
<div class="form_create_profil fade-in two">
    <div class="row">
        <div class="title_account2">
            <img class="image_title_account" src="assets/images/login.svg">
            <h2 style="margin-left: 100px;" class="title-form-alt">Connexion<br><span
                        class="title-form">Sign in</span></h2>
        </div>
    </div>
    <form class="fade-in three" id="register-form" method="POST" action="../controllers/ProfilsController.php">
        <div class="row">
            <p class='title_login1'><?php echo $title_spelog ?></p>
            <div style="margin-top: 15px; margin-bottom: 15px" class="col s6 center">
                <a href="../controllers/googleauth.php"><img class="auth_logo" src="assets/images/google.png"></a>
            </div>
            <div style="margin-top: 15px; margin-bottom: 15px" class="col s6 center">
                <a href="../controllers/42auth.php"><img class="auth_logo" src="assets/images/42_Logo.svg"></a>
            </div>
            <p class='title_login1'><?php echo $title_basiclog ?></p>
            <div class="input-field col s12">
                <i class="material-icons prefix">account_circle</i>
                <input id="login" type="text" class="validate" name="login" autofocus required>
                <label for="login"><?php echo $formlogin ?></label>
            </div>
            <div class="input-field col s12">
                <i class="material-icons prefix">vpn_key</i>
                <input id="password" type="password" class="validate" name="password" required>
                <label for="password"><?php echo $formpassword1 ?></label>
            </div>
        </div>
        <input type="hidden" name="connec" value="ok">
        <div style="text-align: center">
            <button class="waves-teal btn-large fade-in five" type="submit" name="submit"
                    value="Créer mon profil"><?php echo $loginbtn ?>
                <i class="material-icons right">send</i>
            </button>
        </div>
    </form>
    <div style="text-align: center; margin-top: 50px">
        <p class="connect fade-in four"><?php echo $loginforgot ?> <a class="link"
                                                                      href="reset.php"><?php echo $loginreset ?></a></p>
    </div>
</div>

<script src="assets/js/materialize.js"></script>
<script>
    $(document).ready(function () {
        $('select').formSelect();
    });
</script>

</body>