<?php
/**
 * Created by PhpStorm.
 * User: pimaglio
 * Date: 2019-02-12
 * Time: 11:59
 */
if (!isset($_SESSION))
    session_start();

if (isset($_SESSION['loggued_on_user']))
    header("Location: index.php");
include('header_alt.php');
?>

<body>

<div id="background">
</div>

<div class="form_create_profil" style="padding-bottom: 50px">
    <div class="row fade-in two">
        <div class="col s6">
            <div class="form_pic2"></div>
        </div>
        <div class="col s6">
            <h2 class="title-form">Réinitialiser <br><span class="title-form-alt">le mot de passe</span></h2>
        </div>
    </div>
    <form id="register-form" method="POST" action="../controllers/ProfilsController.php">
        <div class="row fade-in three">
            <div class="input-field col s12">
                <i class="material-icons prefix">account_circle</i>
                <input id="login" type="text" class="validate" name="login" required>
                <label for="login">Nom d'utilisateur</label>
            </div>
        </div>
        <input type="hidden" name="forgot" value="ok"><br/>
        <div style="text-align: center">
            <button class="waves-teal btn-large fade-in four" type="submit" name="submit"
                    value="Créer mon profil">Recevoir un email
                <i class="material-icons right">send</i>
            </button>
        </div>
    </form>
</div>

<script src="assets/js/materialize.js"></script>
<script>
    $(document).ready(function () {
        $('select').formSelect();
    });
</script>

</body>