<?php
//if (isset($_GET['film']) && !empty($_GET['film'])) {
//    try {
require_once '../models/FilmModel.php';

function find_torrent($imdb)
{
    if (!($json = file_get_contents('https://api-fetch.website/tv/movie/' . $imdb)))
        return NULL;
    $s = stristr($json, 'magnet');
    $link = explode('"', $s)[0];
    return $link;
}