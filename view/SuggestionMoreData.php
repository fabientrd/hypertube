<?php
/**
 * Created by PhpStorm.
 * User: pimaglio
 * Date: 2019-04-30
 * Time: 00:44
 */
/*require ('../controllers/FilmController.php');*/
include('../Controllers/FilmController.php');

$data = recup_film_arr($_GET['last_id']);

include('DataSuggestion.php');
?>