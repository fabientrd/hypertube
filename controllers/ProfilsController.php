<?php
/**
 * Created by PhpStorm.
 * User: ftreand
 * Date: 2019-04-04
 * Time: 15:05
 */
include('../models/UsersModel.php');
include('../models/LocationModel.php');

if (!isset($_SESSION)) {
    session_start();
}

function htmldump($variable, $height = "300px")
{
    echo "<pre style=\"border: 1px solid #000; height: {$height}; background-color: white; overflow: auto; margin: 0.5em;\">";
    var_dump($variable);
    echo "</pre>\n";
}

function lowpassword()
{
    $cl = strlen($_POST['password']);
    $spechar = 5;
    if ($cl < 6)
        return 1;
    else if (!preg_match('/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/', $_POST['password']))
        return $spechar;
    else
        return 0;
}

function recup_data()
{
    $db_con = new infos($_POST);
    return $db_con->array_data();
}

function recup_inter()
{
    $db_con = new infos($_POST);
    return $db_con->array_inter();
}

function recup_user()
{
    $db_con = new infos([]);
    return $db_con->array_user();
}

function recup_user_id($id)
{
    $db_con = new account(array(
        'id' => $id
    ));
    return $db_con->array_user_id($id);
}

function recup_data_id($id)
{
    $db_con = new infos(array(
        'id' => $id
    ));
    return $db_con->array_data_id($id);
}

function recup_inter_id($id)
{
    $db_con = new infos(array(
        'id' => $id
    ));
    return $db_con->array_inter_id($id);
}

/*42 AUTH ACTION*/

function get_42_id($id)
{
    $db_con = new account(array(
        'id_42' => $id
    ));
    return $db_con->select_42_id();
}

/*GOOGLE AUTH ACTION*/

function get_google_id($id)
{
    $db_con = new account(array(
        'id_google' => $id
    ));
    return $db_con->select_google_id();
}

// MODIF USER

if (isset($_POST['user_modif']) && $_POST['user_modif'] === 'ok' && isset($_POST['login'])
    && isset($_POST['password']) && isset($_POST['password2']) && isset($_POST['nom'])
    && isset($_POST['email'])) {
    $db_con = new account(array(
        'login' => $_SESSION['loggued_on_user']
    ));
    $user = $db_con->array_user();
    echo $_SESSION['loggued_on_user'];
    if (htmlspecialchars($_POST['nom']) !== $_POST['nom'] || htmlspecialchars($_POST['login'])
        !== $_POST['login'] || htmlspecialchars($_POST['email']) !== $_POST['email']) {
        $_SESSION['error'] = 5;
        header('Location: ../view/account.php');
        exit();
    }
    if (!empty($_POST['42pic'])){
        $target_file = $_POST['42pic'];
    }
    if (!empty($_FILES["fileToUpload"]["size"])) {
        /*    UPLOAD IMAGE*/
        $target_dir = "../upload/";
        $target_file = $target_dir . date('Y-m-d_g:i:s') . ".png";
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check === false) {
            $_SESSION['error'] = 13;
            header('Location: ../view/account.php');
            exit();
        }
        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 5000000) {
            $_SESSION['error'] = 14;
            header('Location: ../view/account.php');
            exit();
        }
        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif") {
            $_SESSION['error'] = 15;
            header('Location: ../view/account.php');
            exit();
        }
        if (!move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $_SESSION['error'] = 15;
            header('Location: ../view/account.php');
            exit();
        }
    }

    if (!empty($_POST['password']) && !empty($_POST['password2'])) {
        if (strlen($_POST['login']) > 25) {
            $_SESSION['error'] = 1;
            header('Location: ../view/account.php');
            exit();
        }
        if ($_POST['password'] !== $_POST['password2']) {
            $_SESSION['error'] = 2;
            header('Location: ../view/account.php');
            exit();
        }
        $spechar = lowpassword();
        if ($spechar == 5) {
            $_SESSION['error'] = 3;
            header('Location: ../view/account.php');
            exit();
        }
        if (lowpassword() == 1) {
            $_SESSION['error'] = 4;
            header('Location: ../view/account.php');
            exit();
        }
        $password = hash('sha256', $_POST['password']);
    } else
        $password = $db_con->user_passwd();
    $info = new infos([]);
    $id = $info->find_id();
    $db_con = new account($_POST);
    $_SESSION['modif'] = 1;
    if ($var = $db_con->ifLoginTaken() === 1 && isset($_SESSION['error']) && $_SESSION['error'] === 6 && $_POST['login'] !== $user['login']) {
        header('Location: ../view/account.php');
        unset ($_SESSION['modif']);
        exit();
    }
    if ($var = $db_con->ifEmailTaken2($_SESSION['id']) === 1) {
        $_SESSION['error'] = 7;
        unset ($_SESSION['modif']);
        header('Location: ../view/account.php');
        exit();
    } else {
        $db = new account(array(
            'login' => $_POST['login'],
            'password' => $password,
            'email' => $_POST['email'],
            'nom' => $_POST['nom'],
            'pic' => $target_file
        ));
        $db->edit_profil($id);
        unset($_SESSION['error']);
        unset ($_SESSION['modif']);
        $_SESSION['success'] = 1;
        $_SESSION['loggued_on_user'] = $_POST['login'];
        header('Location: ../view/account.php');
    }
}

// REGISTER
if (isset($_POST['register_42']) && $_POST['register_42'] === 'ok' && isset($_POST['login'])
    && isset($_POST['nom']) && isset($_POST['email']) && isset($_POST['password'])
    && isset($_POST['password2']) && $_POST['42_id'] == 0 && $_POST['google_id'] == 0) {

    if (htmlspecialchars($_POST['nom']) !== $_POST['nom'] || htmlspecialchars($_POST['login'])
        !== $_POST['login'] || htmlspecialchars($_POST['email']) !== $_POST['email']) {
        $_SESSION['error'] = 5;
        header('Location: ../view/register.php');
        exit();
    }
    if (strlen($_POST['login']) > 25) {
        $_SESSION['error'] = 1;
        header('Location: ../view/register.php');
        exit();
    }
    if ($_POST['password'] !== $_POST['password2']) {
        $_SESSION['error'] = 2;
        header('Location: ../view/register.php');
        exit();
    }
    $spechar = lowpassword();
    if ($spechar == 5) {
        $_SESSION['error'] = 3;
        header('Location: ../view/register.php');
        exit();
    }
    if (lowpassword() == 1) {
        $_SESSION['error'] = 4;
        header('Location: ../view/register.php');
        exit();
    }
    $password = hash('sha256', $_POST['password']);
    /*    UPLOAD IMAGE*/
    $target_dir = "../upload/";
    $target_file = $target_dir . date('Y-m-d_g:i:s') . ".png";
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif") {
        $_SESSION['error'] = 11;
        header('Location: ../view/register.php');
        exit();
    }
// Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check === false) {
        $_SESSION['error'] = 11;
        header('Location: ../view/register.php');
        exit();
    }
// Check file size
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        $_SESSION['error'] = 12;
        header('Location: ../view/register.php');
        exit();
    }
// Allow certain file formats

    if (!move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $_SESSION['error'] = 13;
        header('Location: ../view/register.php');
        exit();
    }
    $new_user = new account(array(
        'login' => $_POST['login'],
        'nom' => $_POST['nom'],
        'password' => $password,
        'email' => $_POST['email'],
        'pic' => $target_file
    ));
    $var = $new_user->add();
    if ($var === 1) {
        header('Location: ../view/register.php');
        exit();
    } else {
        $new_user->sendMail();
        $_SESSION['success2'] = 2;
        header('Location: ../index.php');
    }
}

// REGISTER 42
if (isset($_POST['register_42']) && $_POST['register_42'] === 'ok' && isset($_POST['login'])
    && isset($_POST['nom']) && isset($_POST['email']) && isset($_POST['password'])
    && isset($_POST['password2']) && $_POST['42_id'] != 0) {
    if (htmlspecialchars($_POST['nom']) !== $_POST['nom'] || htmlspecialchars($_POST['login'])
        !== $_POST['login'] || htmlspecialchars($_POST['email']) !== $_POST['email']) {
        $_SESSION['error'] = 5;
        header('Location: ../controllers/42auth.php');
        exit();
    }
    if (strlen($_POST['login']) > 25) {
        $_SESSION['error'] = 1;
        header('Location: ../controllers/42auth.php');
        exit();
    }
    if ($_POST['password'] !== $_POST['password2']) {
        $_SESSION['error'] = 2;
        header('Location: ../controllers/42auth.php');
        exit();
    }
    $spechar = lowpassword();
    if ($spechar == 5) {
        $_SESSION['error'] = 3;
        header('Location: ../controllers/42auth.php');
        exit();
    }
    if (lowpassword() == 1) {
        $_SESSION['error'] = 4;
        header('Location: ../controllers/42auth.php');
        exit();
    }
    $password = hash('sha256', $_POST['password']);
    if (isset($_POST['42pic'])) {
        if (!empty($_POST['42pic']))
            $target_file = $_POST['42pic'];
    }
    if (!empty($_FILES["fileToUpload"]["size"])) {
        /*    UPLOAD IMAGE*/
        $target_dir = "../upload/";
        $target_file = $target_dir . date('Y-m-d_g:i:s') . ".png";
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check === false) {
            $_SESSION['error'] = 11;
            header('Location: ../controllers/42auth.php');
            exit();
        }
        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 500000) {
            $_SESSION['error'] = 12;
            header('Location: ../controllers/42auth.php');
            exit();
        }
        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif") {
            $_SESSION['error'] = 11;
            header('Location: ../controllers/42auth.php');
            exit();
        }
        if (!move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $_SESSION['error'] = 13;
            header('Location: ../controllers/42auth.php');
            exit();
        }
    }
    $new_user = new account(array(
        'login' => $_POST['login'],
        'nom' => $_POST['nom'],
        'password' => $password,
        'email' => $_POST['email'],
        'pic' => $target_file,
        'id_42' => $_POST['42_id']
    ));
    $var = $new_user->add_42();
    if ($var === 1) {
        header('Location: ../controllers/42auth.php');
        exit();
    } else {
        $new_user->sendMail();
        $_SESSION['success2'] = 2;
        unset($_SESSION['id_42']);
        header('Location: ../index.php');
    }
}

// REGISTER GOOGLE
if (isset($_POST['register_42']) && $_POST['register_42'] === 'ok' && isset($_POST['login'])
    && isset($_POST['nom']) && isset($_POST['email']) && isset($_POST['password'])
    && isset($_POST['password2']) && $_POST['google_id'] != 0) {
    if (htmlspecialchars($_POST['nom']) !== $_POST['nom'] || htmlspecialchars($_POST['login'])
        !== $_POST['login'] || htmlspecialchars($_POST['email']) !== $_POST['email'] || htmlspecialchars($_POST['google_id']) !== $_POST['google_id']) {
        $_SESSION['error'] = 5;
        header('Location: ../controllers/googleauth.php');
        exit();
    }
    if (strlen($_POST['login']) > 25) {
        $_SESSION['error'] = 1;
        header('Location: .../controllers/googleauth.php');
        exit();
    }
    if ($_POST['password'] !== $_POST['password2']) {
        $_SESSION['error'] = 2;
        header('Location: ../controllers/googleauth.php');
        exit();
    }
    $spechar = lowpassword();
    if ($spechar == 5) {
        $_SESSION['error'] = 3;
        header('Location: ../controllers/googleauth.php');
        exit();
    }
    if (lowpassword() == 1) {
        $_SESSION['error'] = 4;
        header('Location: ../controllers/googleauth.php');
        exit();
    }
    $password = hash('sha256', $_POST['password']);
    if (isset($_POST['42pic'])) {
        if (!empty($_POST['42pic']))
            $target_file = $_POST['42pic'];
    }
    if (!empty($_FILES["fileToUpload"]["size"])) {
        /*    UPLOAD IMAGE*/
        $target_dir = "../upload/";
        $target_file = $target_dir . date('Y-m-d_g:i:s') . ".png";
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check === false) {
            $_SESSION['error'] = 11;
            header('Location: ../controllers/googleauth.php');
            exit();
        }
        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 500000) {
            $_SESSION['error'] = 12;
            header('Location: ../controllers/googleauth.php');
            exit();
        }
        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif") {
            $_SESSION['error'] = 11;
            header('Location: ../controllers/googleauth.php');
            exit();
        }
        if (!move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $_SESSION['error'] = 13;
            header('Location: ../controllers/googleauth.php');
            exit();
        }
    }
    $new_user = new account(array(
        'login' => $_POST['login'],
        'nom' => $_POST['nom'],
        'password' => $password,
        'email' => $_POST['email'],
        'pic' => $target_file,
        'id_google' => $_POST['google_id']
    ));
    $var = $new_user->add_google();
    if ($var === 1) {
        header('Location: ../controllers/googleauth.php');
        exit();
    } else {
        $new_user->sendMail();
        $_SESSION['success2'] = 2;
        unset($_SESSION['id_google']);
        header('Location: ../index.php');
    }
}

// CONNEXION

if (isset ($_POST['connec']) && $_POST['connec'] === 'ok' && isset($_POST['password'])
    && isset($_POST['login'])) {
    if (htmlspecialchars($_POST['login']) !== $_POST['login']) {
        $_SESSION['error'] = 5;
        header('Location: ../view/login.php');
        exit();
    }
    $password = hash('sha256', $_POST['password']);
    $user = New account(array(
        'password' => $password,
        'login' => $_POST['login']
    ));
    $var = $user->Connect();
    if ($var === 1) {
        $_SESSION['error'] = 8;
        header("Location: ../view/login.php");
        exit();
    }
    if ($var === 2) {
        $_SESSION['error'] = 9;
        header("Location: ../view/login.php");
        exit();
    }
    if ($var === 3) {
        $_SESSION['error'] = 10;
        header("Location: ../view/login.php");
        exit();
    }
    $_SESSION['success'] = 4;
    header("Location: ../view/");
}

/*42 CONNEXION*/

function signin_42($id)
{
    $db_con = new account(array(
        'id_42' => $id
    ));
    $res = $db_con->Connect_42();
    if ($res == 2) {
        $_SESSION['error'] = 9;
        header("Location: ../view/login.php");
        exit();
    }
    $_SESSION['success'] = 4;
    header("Location: ../view/");

}

/*GOOGLE CONNEXION*/

function signin_google($id)
{
    $db_con = new account(array(
        'id_google' => $id
    ));
    $res = $db_con->Connect_Google();
    if ($res == 2) {
        $_SESSION['error'] = 9;
        header("Location: ../view/login.php");
        exit();
    }
    $_SESSION['success'] = 4;
    header("Location: ../view/");

}


// MDP OUBLIE

if (isset($_POST['forgot']) && $_POST['forgot'] === 'ok' && isset($_POST['login'])) {
    if (htmlspecialchars($_POST['login']) !== $_POST['login']) {
        $_SESSION['error'] = 5;
        header('Location: ../view/reset.php');
        exit();
    }
    $spe = "!#$%+?@";
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

    $newpass = substr(str_shuffle($chars), 0, 6);
    $newpass .= substr(str_shuffle($spe), 0, 1);
    $password = hash('sha256', $newpass);
    $new_user = new account(array(
        'login' => $_POST['login'],
        'password' => $password
    ));
    $var = $new_user->ifLoginTaken();
    if ($var !== 1) {
        $_SESSION['error'] = 8;
        header('Location: ../view/reset.php');
        exit();
    } else {
        $new_user->passMail($newpass);
        unset($_SESSION['error']);
        $_SESSION['success'] = 3;
        header('Location: ../view/login.php');
    }
}

// DELETE ACCOUNT

function delete_account()
{
    $db_con = new infos([]);
    $db_con->drop($_SESSION['id']);
}

//FAKE ACCOUNT

function manage_fake_account($arr)
{
    $db_con = new account($arr);
    $db_con->add();
    $db_con->setValid();
    $db_con->setProfile();
    $db = new infos($arr);
    $id = $db->find_id();
    $db->add_data2($arr['location']);
    $db->addPP($arr['picture']);
    $db->addpop($arr['popularite']);
    $loc = new location($arr['location']);
    $loc->add_loc($id);
    $array['sport'] = rand(0, 1);
    $array['voyage'] = rand(0, 1);
    $array['vegan'] = rand(0, 1);
    $array['geek'] = rand(0, 1);
    $array['soiree'] = rand(0, 1);
    $array['tattoo'] = rand(0, 1);
    $array['musique'] = rand(0, 1);
    $array['lecture'] = rand(0, 1);
    $array['theatre'] = rand(0, 1);
    $array['religion'] = rand(0, 1);
    $db->add_interest($array);
}

/*  SET STATUS CONNECT IN*/
function set_connect_in(){
    $db_con = new account([]);
    $db_con->connect_in();
    return 0;
}

/*  SET STATUS CONNECT OUT*/

function set_connect_out(){
    $db_con = new account([]);
    $db_con->connect_out();
    return 0;
}

