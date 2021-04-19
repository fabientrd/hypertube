<?php
/**
 * Created by PhpStorm.
 * User: pimaglio
 * Date: 2019-04-30
 * Time: 00:44
 */
/*require ('../controllers/FilmController.php');*/
include('../controllers/FilmController.php');

$idf = $_GET['last_id'];
$data = $_SESSION['search'];
include('DataSearch.php');
