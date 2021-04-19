<?php
/**
 * Created by PhpStorm.
 * User: ftreand
 * Date: 2019-04-04
 * Time: 12:23
 */

include('../config/database.php');
if (!isset($_SESSION))
    session_start();

class infos
{
    private $age;
    private $sexe;
    private $location;
    private $orientation;
    private $bio;
    private $popularite;
    private $login;
    private $db_con;
    private $id;

    public function __construct(array $user_data)
    {
        if (array_key_exists('age', $user_data))
            $this->age = $user_data['age'];
        if (array_key_exists('sexe', $user_data))
            $this->sexe = $user_data['sexe'];
        if (array_key_exists('location', $user_data))
            $this->location = $user_data['location'];
        if (array_key_exists('orientation', $user_data))
            $this->orientation = $user_data['orientation'];
        if (array_key_exists('bio', $user_data))
            $this->bio = $user_data['bio'];
        if (array_key_exists('popularite', $user_data))
            $this->popularite = $user_data['popularite'];
        if (isset($_SESSION['loggued_on_user']))
            $this->login = $_SESSION['loggued_on_user'];
        $this->db_con = database_connect();
        $this->id = $this->find_id();
    }

    public function find_id()
    {
        $array = [];
        $query = 'SELECT id FROM user_db WHERE login=:log';
        $stmt = $this->db_con->prepare($query);
        $stmt->execute(array(
            ":log" => $this->login
        ));
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC))
            array_push($array, $data);
        $array = $array[0];
        return $array['id'];
    }

    public function array_user()
    {
        $query = 'SELECT * FROM user_db WHERE id=:id';
        $stmt = $this->db_con->prepare($query);
        $stmt->execute(array(
            ":id" => $this->id
        ));
        return ($fetch = $stmt->fetch(PDO::FETCH_ASSOC));
    }

    public function del_user_db()
    {
        $query = 'DELETE FROM user_db WHERE id=:id';
        $stmt = $this->db_con->prepare($query);
        $stmt->execute(array(
            ":id" => $this->id,
        ));
    }

    public function drop($id)
    {
        $query = 'DELETE FROM `user_db` WHERE id=:id';
        $stmt = $this->db_con->prepare($query);
        $stmt->execute(array(
            ":id" => $id
        ));
        header('Location: index.php');
    }
}

class account
{
    private $login;
    private $nom;
    private $id;
    private $id_42;
    private $id_google;
    private $pic;
    private $password;
    private $email;
    private $date;
    private $notif;
    private $db_con;
    private $valid;
    public $error;

    public function __construct(array $user_account)
    {
        if (array_key_exists('id', $user_account))
            $this->id = $user_account['id'];
        if (array_key_exists('login', $user_account))
            $this->login = $user_account['login'];
        if (array_key_exists('password', $user_account))
            $this->password = $user_account['password'];
        if (array_key_exists('email', $user_account))
            $this->email = $user_account['email'];
        if (array_key_exists('notif', $user_account))
            $this->notif = $user_account['notif'];
        if (array_key_exists('nom', $user_account))
            $this->nom = $user_account['nom'];
        if (array_key_exists('valid', $user_account))
            $this->valid = $user_account['valid'];
        if (array_key_exists('pic', $user_account))
            $this->pic = $user_account['pic'];
        if (array_key_exists('id_42', $user_account))
            $this->id_42 = $user_account['id_42'];
        if (array_key_exists('id_google', $user_account))
            $this->id_google = $user_account['id_google'];
        $this->date = date('Y-m-d H:i:s');
        $this->db_con = database_connect();
    }

    /*   STATUS CONNECT IN*/

    public function connect_in()
    {
        $stmt = $this->db_con->prepare("UPDATE user_db SET status=1 WHERE id=:id");
        $stmt->execute(array(
            ":id" => $_SESSION['id']
        ));
    }

    /*   STATUS CONNECT OUT*/

    public function connect_out()
    {
        $stmt = $this->db_con->prepare("UPDATE user_db SET status=0 WHERE id=:id");
        $stmt->execute(array(
            ":id" => $_SESSION['id']
        ));
    }

    // RECUP USER ARRAY

    public function array_user()
    {
        $query = 'SELECT * FROM user_db WHERE login=:log';
        $stmt = $this->db_con->prepare($query);
        $stmt->execute(array(
            ":log" => $this->login
        ));
        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fetch;
    }

    public function array_user_id($id)
    {
        $query = 'SELECT * FROM user_db WHERE id=:id';
        $stmt = $this->db_con->prepare($query);
        $stmt->execute(array(
            ":id" => $id
        ));
        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fetch;
    }

    // CONTROL INSCRIPTION LOGIN / EMAIL

    public function user_passwd()
    {
        $query = 'SELECT * FROM user_db WHERE login=:log';
        $stmt = $this->db_con->prepare($query);
        $stmt->execute(array(
            ":log" => $this->login
        ));
        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fetch['password'];
    }

    public function ifLoginTaken()
    {
        $stmt = $this->db_con->prepare("SELECT * FROM user_db WHERE login=:login");
        $stmt->execute(array(
            ":login" => $this->login
        ));
        $count = $stmt->rowCount();
        if ($count != 0) {
            $_SESSION['error'] = 6;
            return 1;
        }
        return 0;
    }

    public function ifEmailTaken()
    {
        $stmt = $this->db_con->prepare("SELECT * FROM user_db WHERE email=:email");
        $stmt->execute(array(
            ":email" => $this->email
        ));
        $count = $stmt->rowCount();
        if ($count != 0) {
            if (!isset($_SESSION['modif'])) {
                $_SESSION['error'] = 7;
                return 1;
            }
        }
        return 0;
    }

    public function ifEmailTaken2($id)
    {
        $stmt = $this->db_con->prepare("SELECT * FROM user_db WHERE email=:email AND id!=:id");
        $stmt->execute(array(
            ":email" => $this->email,
            ":id" => $id
        ));
        $count = $stmt->rowCount();
        if ($count != 0) {
            $_SESSION['error'] = 7;
            return 1;
        }
        return 0;
    }

//              AJOUT USER

    public function add()
    {
        try {
            if ($this->ifLoginTaken() || $this->ifEmailTaken())
                return 1;
            $stmt = $this->db_con->prepare("INSERT INTO user_db(login, nom, email, password, creation_date, pic) VALUES (:login, :nom, :email, :password, :creation_date, :pic)");
            $val = $stmt->execute(array(
                ":login" => $this->login,
                ":nom" => $this->nom,
                ":email" => $this->email,
                ":password" => $this->password,
                ":creation_date" => $this->date,
                ":pic" => $this->pic
            ));
            if ($val) {
                $_SESSION['loggued_but_not_valid'] = $this->login;
                if (!isset($_SESSION['loggued_on_user'])) {
                    $query = 'SELECT id FROM user_db WHERE login=:login';
                    $stmt = $this->db_con->prepare($query);
                    $stmt->execute(array(
                        ":login" => $this->login
                    ));
                    $_SESSION['id'] = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
                }
                return 0;
            } else
                echo "ERROR EXECUTE ADD";
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function add_42()
    {
        try {
            if ($this->ifLoginTaken() || $this->ifEmailTaken())
                return 1;
            $stmt = $this->db_con->prepare("INSERT INTO user_db(login, nom, email, password, creation_date, pic, id_42) VALUES (:login, :nom, :email, :password, :creation_date, :pic, :id_42)");
            $val = $stmt->execute(array(
                ":login" => $this->login,
                ":nom" => $this->nom,
                ":email" => $this->email,
                ":password" => $this->password,
                ":creation_date" => $this->date,
                ":pic" => $this->pic,
                ":id_42" => $this->id_42
            ));
            if ($val) {
                $_SESSION['loggued_but_not_valid'] = $this->login;
                if (!isset($_SESSION['loggued_on_user'])) {
                    $query = 'SELECT id FROM user_db WHERE login=:login';
                    $stmt = $this->db_con->prepare($query);
                    $stmt->execute(array(
                        ":login" => $this->login
                    ));
                    $_SESSION['id'] = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
                }
                return 0;
            } else
                echo "ERROR EXECUTE ADD";
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function add_google()
    {
        try {
            if ($this->ifLoginTaken() || $this->ifEmailTaken())
                return 1;
            $stmt = $this->db_con->prepare("INSERT INTO user_db(login, nom, email, password, creation_date, pic, id_google) VALUES (:login, :nom, :email, :password, :creation_date, :pic, :id_google)");
            $val = $stmt->execute(array(
                ":login" => $this->login,
                ":nom" => $this->nom,
                ":email" => $this->email,
                ":password" => $this->password,
                ":creation_date" => $this->date,
                ":pic" => $this->pic,
                ":id_google" => $this->id_google
            ));
            if ($val) {
                $_SESSION['loggued_but_not_valid'] = $this->login;
                if (!isset($_SESSION['loggued_on_user'])) {
                    $query = 'SELECT id FROM user_db WHERE login=:login';
                    $stmt = $this->db_con->prepare($query);
                    $stmt->execute(array(
                        ":login" => $this->login
                    ));
                    $_SESSION['id'] = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
                }
                return 0;
            } else
                echo "ERROR EXECUTE ADD";
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function edit_profil($id)
    {
        try {
            $stmt = $this->db_con->prepare("UPDATE user_db SET login=:login, email=:email, password=:password, nom=:nom, pic=:pic WHERE id='$id'");
            $stmt->execute(array(
                ":login" => $this->login,
                ":email" => $this->email,
                ":password" => $this->password,
                ":nom" => $this->nom,
                ":pic" => $this->pic
            ));
            unset($_SESSION['loggued_on_user']);
            $_SESSION['loggued_on_user'] = $this->login;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

//              EMAIL D'ACTIVATION

    public function sendMail()
    {
        $cle = md5(microtime(TRUE) * 100000);
        $stmt = $this->db_con->prepare("UPDATE user_db SET cle=:cle WHERE login=:login");
        $stmt->execute(array(
            ":cle" => $cle,
            ":login" => $this->login
        ));
        $sujet = "Hypertube | Activer votre compte";
        $entete = "From: no_reply@hypertube.com";
        $message = 'Bienvenue sur Hypertube ' . $this->login . '!

		Pour activer votre compte, veuillez cliquer sur le lien ci dessous
		ou copier/coller dans votre navigateur internet.

		http://localhost:8008/models/activation.php?login=' . urlencode($this->login) . '&cle=' . urlencode($cle) . '
		---------------
		Ceci est un mail automatique, Merci de ne pas y répondre.';
        mail($this->email, $sujet, $message, $entete);
    }

    //              EMAIL RECOVERY PASS

    public function passMail($newpass)
    {

        $stmt = $this->db_con->prepare("UPDATE user_db SET password=:password WHERE login=:login");
        $stmt->execute(array(
            ":login" => $this->login,
            ":password" => $this->password
        ));
        $stmt = $this->db_con->prepare("SELECT email FROM user_db WHERE login=:login");
        $stmt->execute(array(
            ":login" => $this->login
        ));
        $fetched = $stmt->fetch(PDO::FETCH_ASSOC);
        $sujet = "Matcha | Réinitialisation de votre mot de passe";
        $entete = "From: no_reply@hypertube.com";
        $message = 'Salut ' . $this->login . '!
        
        Voici ton nouveau mot de passe:    ' . $newpass . '
		---------------
		Ceci est un mail automatique, Merci de ne pas y répondre.';
        mail($fetched['email'], $sujet, $message, $entete);
    }


//              ACTIVATION

    public function Activation($cle, $login)
    {
        $stmt = $this->db_con->prepare("SELECT cle,valid,id,id_42,id_google FROM user_db WHERE login=:login");
        $stmt->execute(array(
            ":login" => $login
        ));
        $count = $stmt->rowcount();
        if ($count == 0)
            return 1;
        $fetched = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fetched['valid'])
            return 2;
        if ($fetched['cle'] == $cle) {
            $stmt = $this->db_con->prepare("UPDATE user_db SET valid=:valid WHERE login=:login");
            $stmt->execute(array(
                    ":valid" => true,
                    ":login" => $login)
            );
            $_SESSION['loggued_on_user'] = $login;
            $_SESSION['id'] = $fetched['id'];
            $_SESSION['lang'] = 'en';
            $this->connect_in();
            return 0;
        }
        return 3;
    }

//              CONNEXION

    public function Connect()
    {
        $stmt = $this->db_con->prepare("SELECT email, valid, password, login, id FROM user_db WHERE login=:login");
        $stmt->execute(array(
            ":login" => $this->login
        ));
        $count = $stmt->rowCount();
        if ($count == 0)
            return 1;
        $fetched = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$fetched['valid'])
            return 2;
        if ($fetched['password'] !== $this->password)
            return 3;
        $_SESSION['loggued_on_user'] = $fetched['login'];
        $_SESSION['id'] = $fetched['id'];
        $_SESSION['lang'] = 'en';
        $this->connect_in();
        return 0;
    }

    public function Connect_42()
    {
        $stmt = $this->db_con->prepare("SELECT valid, login, id FROM user_db WHERE id_42=:id_42");
        $stmt->execute(array(
            ":id_42" => $this->id_42
        ));
        $fetched = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$fetched['valid'])
            return 2;
        $_SESSION['loggued_on_user'] = $fetched['login'];
        $_SESSION['id'] = $fetched['id'];
        $_SESSION['lang'] = 'en';
        $this->connect_in();
        return 0;
    }

    public function Connect_Google()
    {
        $stmt = $this->db_con->prepare("SELECT valid, login, id FROM user_db WHERE id_google=:id_google");
        $stmt->execute(array(
            ":id_google" => $this->id_google
        ));
        $fetched = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$fetched['valid'])
            return 2;
        $_SESSION['loggued_on_user'] = $fetched['login'];
        $_SESSION['id'] = $fetched['id'];
        $_SESSION['lang'] = 'en';
        $this->connect_in();
        return 0;
    }

    public function UpNotif()
    {
        $stmt = $this->db_con->prepare("UPDATE user_db SET notif=:notif WHERE login=:login");
        $val = $stmt->execute(array(
            ":login" => $this->login,
            ":notif" => $this->notif
        ));
        if ($val)
            return 1;
        else
            return 0;
    }

    public function __destruct()
    {
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setValid()
    {
        $query = 'UPDATE user_db SET valid=1 WHERE login=:login';
        $stmt = $this->db_con->prepare($query);
        $stmt->execute(array(
            ":login" => $this->login
        ));
    }

    public function setProfile()
    {
        $query = 'UPDATE user_db SET profile=1 WHERE login=:login';
        $stmt = $this->db_con->prepare($query);
        $stmt->execute(array(
            ":login" => $this->login
        ));
    }

    public function select_id($id)
    {
        $query = 'SELECT id FROM user_db WHERE id=:id';
        $stmt = $this->db_con->prepare($query);
        $stmt->execute(array(
            ":id" => $id
        ));
        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
        if (isset($fetch['id']))
            return 1;
        return 0;
    }

    public function select_42_id()
    {
        $query = 'SELECT id_42 FROM user_db WHERE id_42=:id_42';
        $stmt = $this->db_con->prepare($query);
        $stmt->execute(array(
            ":id_42" => $this->id_42
        ));
        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
        if (isset($fetch['id_42']))
            return 1;
        return 0;
    }

    public function select_google_id()
    {
        $query = 'SELECT id_google FROM user_db WHERE id_google=:id_google';
        $stmt = $this->db_con->prepare($query);
        $stmt->execute(array(
            ":id_google" => $this->id_google
        ));
        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
        if (isset($fetch['id_google']))
            return 1;
        return 0;
    }

    public function select_login($id)
    {
        $error_user = 'Compte supprimé';
        $query = 'SELECT login FROM user_db WHERE id=:id';
        $stmt = $this->db_con->prepare($query);
        $stmt->execute(array(
            ":id" => $id
        ));
        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
        if (isset($fetch['login']))
            return $fetch['login'];
        else
            return $error_user;
    }

    public function select_nom($id)
    {
        $error_user = 'Compte supprimé';
        $query = 'SELECT nom FROM user_db WHERE id=:id';
        $stmt = $this->db_con->prepare($query);
        $stmt->execute(array(
            ":id" => $id
        ));
        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
        if (isset($fetch['nom']))
            return $fetch['nom'];
        else
            return $error_user;
    }
}