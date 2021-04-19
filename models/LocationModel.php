<?php
/**
 * Created by PhpStorm.
 * User: ftreand
 * Date: 2019-04-09
 * Time: 16:45
 */

class location
{
    private $ville;
    private $zipcode;
    private $lat;
    private $longi;
    private $arrond;
    private $db_con;

    public function __construct($arr)
    {
        if (array_key_exists('0', $arr))
            $this->ville = $arr['0'];
        if (array_key_exists('1', $arr))
            $this->zipcode = $arr['1'];
        if (array_key_exists('2', $arr))
            $this->lat = $arr['2'];
        if (array_key_exists('3', $arr))
            $this->longi = $arr['3'];
        if (array_key_exists('4', $arr))
            $this->arrond = $arr['4'];
        $this->db_con = database_connect();
    }

    public function add_loc($id){
        $query = 'INSERT INTO location (id_usr, ville, zipcode, lat, `long`, arrondissement) VALUES (:id, :ville, :zip, :lat, :longi, :arron )';
        $stmt = $this->db_con->prepare($query);
        $stmt->execute(array(
            ":id" => $id,
            ":ville" => $this->ville,
            ":zip" => $this->zipcode,
            ":lat" => $this->lat,
            ":longi" => $this->longi,
            ":arron" => $this->arrond
        ));
    }

    public function recup_lat_long(){
        $query = 'SELECT lat, `long` FROM `location` WHERE id_usr=:id';
        $stmt = $this->db_con->prepare($query);
        $stmt->execute(array(
            ":id" => $_SESSION['id']
        ));
        return $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function recup_lat_long_id($id){
        $query = 'SELECT lat, `long` FROM `location` WHERE id_usr=:id';
        $stmt = $this->db_con->prepare($query);
        $stmt->execute(array(
            ":id" => $id
        ));
        return $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function all_loc(){
        $arr = [];
        $query = 'SELECT id_usr, lat, `long` FROM `location` WHERE NOT id_usr=:id';
        $stmt = $this->db_con->prepare($query);
        $stmt->execute(array(
            ":id" => $_SESSION['id']
        ));
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC))
            array_push($arr, $data);
        return $arr;
    }

    /**
     * Retourne la distance en kilometre entre deux latitude et longitude fournit
     */
    public static function distance($lat1, $lng1, $lat2, $lng2) {
        $earth_radius = 6378137;   // Terre = sphère de 6378km de rayon
        $rlo1 = deg2rad($lng1);
        $rla1 = deg2rad($lat1);
        $rlo2 = deg2rad($lng2);
        $rla2 = deg2rad($lat2);
        $dlo = ($rlo2 - $rlo1) / 2;
        $dla = ($rla2 - $rla1) / 2;
        $a = (sin($dla) * sin($dla)) + cos($rla1) * cos($rla2) * (sin($dlo) * sin($dlo));
        $d = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $meter = ($earth_radius * $d);
        return $meter / 1000;
    }
}