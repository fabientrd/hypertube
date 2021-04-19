<?php
include('header_connect.php');

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['loggued_on_user']))
    header('Location: ../index.php');

if (!isset($_GET['id']))
    header('Location: index.php');
else {
    $db_con = new account([]);
    $infos = new infos([]);
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
<div class="container_profil">
    <div style="position: relative" class="user_profil">
        <?php

        $id_usr = $_SESSION['id'];
        $id_usr_l = $_GET['id'];
        $data = recup_user();
        $pic = $data['pic'];
        $nom = $data['nom'];
        $login = $data['login'];
        $email = $data['email'];
        $id = $data['id'];
        $date = $data['creation_date'];
        echo "
        
        <div class=\"user_profil_image\">
            <img class=\"circle\" width=\"180\" height=\"180\" src=\"$pic\">
        </div>
        <div class=\"row center pdrl\">
            <div class=\"col s12\">
                <b class=\"info_sub_profil\">$nom </b><span>($login)</span>
                <p class=\"fw100\">Inscrit depuis le $date</p>
            </div>
        </div>
        ";
        ?>
    </div>
</div>


<script src="assets/js/materialize.js"></script>

<script>
    $(document).ready(function () {
        $('.materialboxed').materialbox();
    });
</script>
</body>