<?php
/**
 * Created by PhpStorm.
 * User: pimaglio
 * Date: 2019-02-12
 * Time: 10:19
 */

include('./UsersModel.php');
if (!isset($_SESSION))
	session_start();
$new = New account(array("empty" => "empty"));
$res = $new->Activation($_GET['cle'],$_GET['login']);
if ($res === 1)
	$_SESSION['error'] = 8;
if ($res === 2)
	$_SESSION['error'] = 11;
if ($res === 3)
	$_SESSION['error'] = 12;
if ($res === 0)
{
	$_SESSION['success'] = 5;
	header("Location: ../view/index.php");
	exit() ;
}
header("Location: ../index.php");
?>

