<?php
/**
 * Created by PhpStorm.
 * User: pimaglio
 * Date: 2019-02-06
 * Time: 14:26
 */

function database_connect()
{
    $DB_DSN = "172.18.0.2";
    $DB_USER = "root";
    $DB_PASSWORD = "root";
    $DB_NAME = "hypertube";

    $conn = new PDO("mysql:host=$DB_DSN", $DB_USER, $DB_PASSWORD);
    $conn->exec('CREATE DATABASE IF NOT EXISTS `hypertube` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;');
    try {
        $db_conn = new PDO("mysql:host=$DB_DSN;dbname=$DB_NAME", $DB_USER, $DB_PASSWORD);
        // set the PDO error mode to exception
        $db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        {
            return ($db_conn);
        }
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        exit();
    }
}
