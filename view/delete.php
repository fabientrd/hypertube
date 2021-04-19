<?php
/**
 * Created by PhpStorm.
 * User: ftreand
 * Date: 2019-04-08
 * Time: 14:28
 */

include ('../controllers/ProfilsController.php');
if (!isset($_SESSION))
    session_start();
echo $_SESSION['loggued_on_user'];
delete_account();
unset($_SESSION);
session_destroy();
header('Location: ../index.php');