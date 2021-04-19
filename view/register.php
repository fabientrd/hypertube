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
            <img class="image_title_account" src="assets/images/createprofil.svg">
            <h2 style="margin-left: 100px;" class="title-form-alt">Inscription<br><span
                        class="title-form">Sign up</span></h2>
        </div>
    </div>
    <?php
    $nom = '';
    $login = '';
    $email = '';
    $pic = '';
    $id = '';
    if (isset($_GET['name']))
        $nom = $_GET['name'];
    if (isset($_GET['login']))
        $login = $_GET['login'];
    if (isset($_GET['email']))
        $email = $_GET['email'];
    if (isset($_GET['pic'])) {
        $pic = $_GET['pic'];
        $require = '';
    } else
        $require = 'required';
    if (isset($_SESSION['id_42']))
        $id_42 = $_SESSION['id_42'];
    else
        $id_42 = 0;
    if (isset($_SESSION['id_google']))
        $id_google = $_SESSION['id_google'];
    else
        $id_google = 0;
    echo "
    <form id=\"register-form\" method=\"POST\" action=\"../controllers/ProfilsController.php\" enctype=\"multipart/form-data\">
        <div class=\"row\">
            <p class='title_login1'>$title_spesign</p>
            <div style=\"margin-top: 15px; margin-bottom: 15px\" class=\"col s6 center\">
            <a href=\"../controllers/googleauth.php\"><img class=\"auth_logo\" src=\"assets/images/google.png\"></a>
        </div>
        <div style=\"margin-top: 15px; margin-bottom: 15px\" class=\"col s6 center\">
        <a href=\"../controllers/42auth.php\"><img class=\"auth_logo\" src=\"assets/images/42_Logo.svg\"></a>
</div>
<p class='title_login1'>$title_basicsign</p>
<div class=\"input-field col s6 fade-in three\">
<i class=\"material-icons prefix\">account_circle</i>
<input id=\"nom\" type=\"text\" class=\"validate\" pattern=\"[A-Za-z\séèâêëçû -]+\" name=\"nom\" maxlength=\"50\" value='$nom' required>
<span class=\"helper-text\" data-error=\"$formnameError\" data-success=\"$formnameSuccess\"></span>
<label for=\"nom\">$formname</label>
</div>
<div class=\"input-field col s6 fade-in four\">
<i class=\"material-icons prefix\">account_circle</i>
<input id=\"login\" type=\"text\" class=\"validate\" pattern=\"[A-Za-z-0-9\s -]+\" name=\"login\" maxlength=\"25\" value='$login' required>
<span class=\"helper-text\" data-error=\"$formloginError\" data-success=\"$formnameSuccess\"></span>
<label for=\"login\">$formlogin</label>
</div>
<div class=\"input-field col s12 fade-in five\">
<i class=\"material-icons prefix\">email</i>
<input id=\"email\" type=\"email\" class=\"validate\" name=\"email\" value='$email' required>
<label for=\"email\">$formemail</label>
</div>
<div class=\"input-field col s6 fade-in six\">
<i class=\"material-icons prefix\">vpn_key</i>
<input id=\"password\" type=\"password\" class=\"validate\" pattern=\"(?=.*[!@#$%^&*(),.?:{}|<>]).{6,}\" name=\"password\" maxlength=\"25\" required>
<span class=\"helper-text\" data-error=\"$formpasswordError\" data-success=\"$formnameSuccess\"></span>
<label for=\"password\">$formpassword1</label>
</div>
<div class=\"input-field col s6 fade-in seven\">
<i class=\"material-icons prefix\">vpn_key</i>
<input id=\"password2\" type=\"password\" class=\"validate\" pattern=\"(?=.*[!@#$%^&*(),.?:{}|<>]).{6,}\" name=\"password2\" maxlength=\"25\" required>
<span class=\"helper-text\" data-error=\"$formpasswordError2\" data-success=\"$formnameSuccess\"></span>
<label for=\"password2\">$formpassword2</label>
</div>
</div>
<div style=\"text-align: center\" class=\"input-field col s12\">
<img src=\"$pic\" class=\"preview img_up\"/>
<div class=\"file-field input-field\">
<div class=\"btn\">
    <span>$formpic</span>
    <input type=\"file\" accept=\"image/*\" name=\"fileToUpload\" id=\"fileToUpload\" data-preview=\".preview\" $require/>
    <input type='hidden' name='42pic' value='$pic'/>
</div>
<div class=\"file-path-wrapper\">
    <input class=\"file-path validate\" type=\"text\">
</div>
</div>
</div>
<input type=\"hidden\" name=\"register_42\" value=\"ok\">
<input type='hidden' name='42_id' value='$id_42'>
<input type='hidden' name='google_id' value='$id_google'>
<div style=\"text-align: center\">
<button class=\"btn-large waves-effect waves-light accent-3 fade-in eight\" type=\"submit\" name=\"submit\" value=\"Créer mon profil\">$formbtn
<i class=\"material-icons right\">send</i>
</button>
</div>
</form>
<div style=\"text-align: center; margin-top: 50px\">
<p class=\"connect fade-in seven\">$formconnect1 <a class=\"link\" href=\"login.php\">$formconnect2</a></p>
</div>
</div>";
    ?>

    <script src="assets/js/materialize.js"></script>
    <script>
        $(document).ready(function () {
            $('select').formSelect();
        });

        var password = document.getElementById("password")
            , confirm_password = document.getElementById("password2");

        function validatePassword() {
            if (password.value !== confirm_password.value) {
                confirm_password.setCustomValidity("Les mots de passe ne correspondent pas");
            } else {
                confirm_password.setCustomValidity('');
            }
        }

        password.onchange = validatePassword;
        confirm_password.onkeyup = validatePassword;

        $(function () {
            $("input[data-preview]").change(function () {
                var input = $(this);
                var oFReader = new FileReader();
                const file2 = this.files[0];
                const fileType = file2['type'];
                const validImageTypes = ['image/gif', 'image/jpeg', 'image/png'];
                if (!validImageTypes.includes(fileType)) {
                    window.location.reload();
                }
                oFReader.readAsDataURL(this.files[0]);
                oFReader.onload = function (oFREvent) {
                    $(input.data('preview')).attr('src', oFREvent.target.result);
                };
            });
        })
    </script>
</body>
