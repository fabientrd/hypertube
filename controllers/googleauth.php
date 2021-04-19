<?php
/**
 * Created by PhpStorm.
 * User: pimaglio
 * Date: 2019-04-28
 * Time: 13:43
 */

session_start();
if (isset($_SESSION['loggued_on_user']))
    header("Location: index.php");
require ('ProfilsController.php');
/* Google App Client Id */
define('CLIENT_ID', '141074765115-n2mhte8kolbu2bm7d0lp19qcstdmpbff.apps.googleusercontent.com');

/* Google App Client Secret */
define('CLIENT_SECRET', 'W1ejP7KnqPPhiG3Rgzf0SuwG');

/* Google App Redirect Url */
define('CLIENT_REDIRECT_URL', 'http://localhost:8008/controllers/googleauth.php');
if (!isset($_GET['code']))
    header('Location: https://accounts.google.com/o/oauth2/v2/auth?scope=' . urlencode('https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email') . '&redirect_uri=' . urlencode(CLIENT_REDIRECT_URL) . '&response_type=code&client_id=' . CLIENT_ID . '&access_type=online');

function GetAccessToken($client_id, $redirect_uri, $client_secret, $code) {
    $url = 'https://www.googleapis.com/oauth2/v4/token';

    $curlPost = 'client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . '&client_secret=' . $client_secret . '&code='. $code . '&grant_type=authorization_code';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = json_decode(curl_exec($ch), true);
    $http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
    if($http_code != 200){
        $_SESSION['error'] = 10;
        header('Location: ../index.php');
    }

    return $data;
}

function GetUserProfileInfo($access_token) {
    $url = 'https://www.googleapis.com/oauth2/v2/userinfo?fields=name,email,gender,id,picture,verified_email';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $access_token));
    $data = json_decode(curl_exec($ch), true);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if($http_code != 200){
        $_SESSION['error'] = 10;
        header('Location: ../index.php');
    }

    return $data;
}

if(isset($_GET['code'])) {
    try {
        // Get the access token
        $data = GetAccessToken(CLIENT_ID, CLIENT_REDIRECT_URL, CLIENT_SECRET, $_GET['code']);

        // Access Tokem
        $access_token = $data['access_token'];

        // Get user information
        $user_info = GetUserProfileInfo($access_token);
        if (get_google_id($user_info['id']) == 1) {
            signin_google($user_info['id']);
            exit;
        }
        $nom = "name=" . $user_info['name'];
        $login = "login=";
        $email = "email=" . $user_info['email'];
        $pic = "pic=" . $user_info['picture'];
        $_SESSION['id_google'] = $user_info['id'];
        if (isset($_SESSION['error'])){
            if ($_SESSION['error'] == 7)
                $email = "email=";
        }
        header('Location: ../view/register.php?' . $nom . "&" . $login . "&" . $email . "&" . $pic);
        die('Redirect');
    }
    catch(Exception $e) {
        echo $e->getMessage();
        $_SESSION['error'] = 10;
        header('Location: ../index.php');
        exit();
    }
}