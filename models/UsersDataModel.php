<?php
/**
 * Created by PhpStorm.
 * User: pimaglio
 * Date: 2019-02-13
 * Time: 13:37
 */

if (!isset($_SESSION)) {
    session_start();
}

include("../config/database.php");

class user
{
    private $login;
    private $comments;
    private $picture;
    private $likes;
    private $liker;
    public $error;


    public function __construct(array $kwargs)
    {
        if (array_key_exists('login', $kwargs))
            $this->login = $kwargs['login'];
        if (array_key_exists('picture', $kwargs))
            $this->picture = $kwargs['picture'];
        if (array_key_exists('comments', $kwargs))
            $this->comments = $kwargs['comments'];
        if (array_key_exists('author', $kwargs))
            $this->author = $kwargs['author'];
        if (array_key_exists('likes', $kwargs))
            $this->likes = $kwargs['likes'];
        if (array_key_exists('liker', $kwargs))
            $this->liker = $kwargs['liker'];
        $this->date = date('Y-m-d H:i:s');
        $this->db_con = database_connect();
    }

    public function addCollage()
    {
        $stmt = $this->db_con->prepare("INSERT INTO data(login,picture,likes) VALUES (:login, :picture, :likes)");
        $val = $stmt->execute(array(
            "login" => $this->login,
            "picture" => $this->picture,
            "likes" => $this->likes,
        ));
    }

    public function getCollage($page)
    {
        $stmt = $this->db_con->prepare("SELECT login, picture FROM data WHERE login IS NOT NULL ORDER BY id DESC LIMIT 6 OFFSET $page");
        $val = $stmt->execute();
        while ($data = $stmt->fetch()) {
            $login2[] = $data['login'];
            $imgs[] = $data['picture'];
        }
        $stmt = $this->db_con->prepare("SELECT login, picture FROM data WHERE login IS NOT NULL");
        $val = $stmt->execute();
        if (isset($imgs) && isset($login2)) {
            $tab = array_combine($imgs, $login2);
            $tab['count'] = $stmt->rowCount();
            return $tab;
        }
        else
            return 0;
    }

    public function getCollageUser()
    {
        $stmt = $this->db_con->prepare("SELECT picture FROM data WHERE login=:login");
        $val = $stmt->execute(array(
            "login" => $this->login
        ));
        $imgs = [];
        while ($data = $stmt->fetch())
            $imgs[] = $data['picture'];
        return $imgs;
    }

    public function verifLoginPic()
    {
        $stmt = $this->db_con->prepare("SELECT picture FROM data WHERE login=:login and picture=:picture");
        $stmt->execute(array(
            "login" => $this->login,
            "picture" => $this->picture
        ));
        $count = $stmt->rowCount();
        if ($count === 0) {
            return 1;
        }
        return 0;
    }

    public function deletePicture()
    {
        if ($this->verifLoginPic() === 1) {
            return 3;
        }
        else {
            $stmt = $this->db_con->prepare("DELETE FROM data WHERE picture=:picture");
            $val = $stmt->execute(array(
                "picture" => $this->picture,
            ));
        }
        return 0;
    }

    public function destroyPictures()
    {
        $stmt = $this->db_con->prepare("DELETE FROM data WHERE login=:login");
        $val = $stmt->execute(array(
            "login" => $this->login
        ));
    }

    public function getcomm($cmt)
    {
        $stmt = $this->db_con->prepare("SELECT comments, author FROM data WHERE picture='$cmt' AND comments IS NOT NULL");
        $val = $stmt->execute();
        while ($data = $stmt->fetch()) {
            $comm[] = $data['comments'];
            $user[] = $data['author'];
        }
        if (isset($comm) && isset($user)) {
            $tab = array_combine($comm, $user);
            return $tab;
        }
        else
            return 0;
    }

    public function addCom()
    {
        $stmt = $this->db_con->prepare("INSERT INTO data(picture,comments,author) VALUES (:picture, :comments, :author)");
        $val = $stmt->execute(array(
            ":picture" => $this->picture,
            ":comments" => $this->comments,
            ":author" => $_SESSION['loggued_on_user'],
        ));
    }

    public function delLike(){
        $stmt = $this->db_con->prepare("DELETE FROM data WHERE picture=:picture AND liker=:liker AND likes='1'");
        $val = $stmt->execute(array(
            "picture" => $this->picture,
            "liker" => $this->liker
        ));
    }

    public function ifLike()
    {
        $stmt = $this->db_con->prepare("SELECT liker, picture FROM data WHERE liker=:liker AND picture=:picture AND likes='1'");
        $stmt->execute(array(
            ":liker" => $_SESSION['loggued_on_user'],
            ":picture" => $this->picture,
        ));
        $count = $stmt->rowCount();
        if ($count != 0) {
            return 1;
        }
        return 0;
    }

    public function getLikes()
    {
        $stmt = $this->db_con->prepare("SELECT picture FROM data WHERE picture=:picture AND likes='1'");
        $stmt->execute(array(
            ":picture" => $this->picture
        ));
        $count = $stmt->rowCount();
        return $count;
    }

    public function addLike()
    {
        try {
            if ($this->ifLike() == 1) {
                $this->delLike();
                return 1;
            }
            $stmt = $this->db_con->prepare("INSERT INTO data(picture,likes,liker) VALUES (:picture, :likes, :liker)");
            $val = $stmt->execute(array(
                "picture" => $this->picture,
                "likes" => $this->likes,
                "liker" => $_SESSION['loggued_on_user'],
            ));
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function deleteCom()
    {
        $stmt = $this->db_con->prepare("DELETE FROM data WHERE picture=:picture AND comments=:comments AND author=:author");
        $val = $stmt->execute(array(
            "picture" => $this->picture,
            "comments" => $this->comments,
            "author" => $this->author,
        ));
    }

    public function comNotif()
    {
        $stmt = $this->db_con->prepare("SELECT login FROM data WHERE picture=:picture AND login IS NOT NULL");
        $stmt->execute(array(
            ":picture" => $this->picture
        ));
        $fetched = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_pic = $fetched['login'];
        $stmt = $this->db_con->prepare("SELECT email, login, notif FROM user_db WHERE login='$user_pic'");
        $stmt->execute(array());
        $fetched = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user_pic === $this->author)
            return 1;
        if ($fetched['notif'] === '1')
            return 18;
        $sujet = "Camagru | Nouveau commentaire";
        $entete = "From: no_reply@camagru.com";
        $message = 'Salut ' . $fetched['login'] . '!
        
        Vous avez reçu un nouveau commentaire de ' . $this->author . ':   ' . $this->comments . '
		---------------
		Ceci est un mail automatique, Merci de ne pas y répondre.';
        mail($fetched['email'], $sujet, $message, $entete);
    }

    public function getNotif()
    {
        $stmt = $this->db_con->prepare("SELECT notif FROM user_db WHERE login=:login");
        $val = $stmt->execute(array(
            ":login" => $this->login
        ));
        $fetched = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($val)
            return $fetched['notif'];
        else
            return 0;
    }

    public function getPic()
    {
        $stmt = $this->db_con->prepare("SELECT picture FROM data WHERE login IS NOT NULL and picture=:picture");
        $stmt->execute(array(
            ":picture" => $this->picture
        ));
        $count = $stmt->rowCount();
        return $count;
    }


    public function __destruct()
    {
    }
}

?>