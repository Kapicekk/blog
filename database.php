<?php
session_start();

class DBconnection extends PDO
{
    private $connection;
    
    private $dsn = "mysql:dbname=blog;host=localhost;charset=utf8";
    private $user = "root";
    private $pass = "";
    
    private $params = [
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => false
    ];

    public function __construct()
    {
        try {
            parent::__construct($this->dsn, $this->user, $this->pass, $this->params);
        } catch (PDOException $exception) {
            echo "Nepodařilo se připojit k databázi: " . $exception->getMessage();
            exit;
        }
    }

    public function addUser($info)
    {

        $this->connection = new DBconnection();
        $username = $_POST['username'];
        $mail = $_POST['mail'];
        $password = $_POST['password'];
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $passwordAgain = $_POST['password_check'];
        $msg = "";

        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            return header("Location: registration.php?id=Zadaná E-mailová adresa již existuje");
        }
        if (password_verify($passwordAgain, $hash)) {

            $sql = "SELECT users.username FROM users WHERE username = '$username'";
            foreach (parent::query($sql) as $row) {
                $check = $row["username"];
            }

            if (empty($check)) {

                $sql = "SELECT users.mail FROM users WHERE mail = '$mail'";
                foreach (parent::query($sql) as $row) {
                    $check2 = $row["mail"];
                }
                if (empty($check2)) {

                    $sql = "INSERT INTO users (username, mail, password) VALUES (:username, :mail, :password)";
                    $stm = parent::prepare($sql);
                    $stm->bindParam(":username", $info->username);
                    $stm->bindParam(":mail", $info->mail);
                    $stm->bindParam(":password", $info->password);

                    if ($stm->execute()) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return header("Location: registration.php?id=Zadaný mail již existuje");
                }
            } else {
                return header("Location: registration.php?id=Zadané jméno již existuje");
            }
        } else {
            return header("Location: registration.php?id=Zadaná hesla se neshodují");
        }
    }


    public function changePassword($password, $username)
    {
        $sql = "UPDATE `users` SET `password` = :password WHERE `users`.`username` = :username";
        $stm = parent::prepare($sql);
        $stm->bindParam(":password", $password);
        $stm->bindParam(":username", $username);
        if ($stm->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function passwordCheck($username)
    {
        $sql = "SELECT users.password FROM users WHERE username = '$username'";
        foreach (parent::query($sql) as $row) {
            $pass = $row[0];
        }
        return $pass;
    }

    public function adminCheck($username)
    {
        $sql = "SELECT users.admin FROM users WHERE username = '$username'";
        foreach (parent::query($sql) as $row) {
            $admin = $row[0];
        }
        return $admin;
    }

    public function banCheck($username)
    {
        $sql = "SELECT users.ban FROM users WHERE username = '$username'";
        foreach (parent::query($sql) as $row) {
            $ban = $row[0];
        }
        return $ban;
    }

    public function showKategory()
    {
        $sql = "SELECT * FROM kategory";
        $array = [];
        foreach (parent::query($sql) as $row) {
            $array[] = $row;
        }
        return $array;
    }

    public function showSingleKategory($id)
    {
        $sql = "SELECT kategory_name FROM kategory WHERE id_kategory = '$id'";
        $name = [];
        foreach (parent::query($sql) as $row) {
            $name = $row[0];
        }
        return $name;
    }

    public function showUserID($username)
    {
        $sql = "SELECT users.id_user FROM users WHERE username='$username'";
        foreach (parent::query($sql) as $row) {
            $id = $row[0];
        }
        return $id;
    }

    public function addPost($info)
    {

        $sql = "INSERT INTO posts (id_creator, id_kategory, title, content) VALUES (:id_creator, :id_kategory, :title, :content)";
        $stm = parent::prepare($sql);
        $stm->bindParam(":id_creator", $info->id_creator);
        $stm->bindParam(":id_kategory", $info->id_kategory);
        $stm->bindParam(":title", $info->title);
        $stm->bindParam(":content", $info->content);

        if ($stm->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function addComment($info)
    {

        $sql = "INSERT INTO comments (id_user, id_post, comment) VALUES (:id_user, :id_post, :comment)";
        $stm = parent::prepare($sql);
        $stm->bindParam(":id_user", $info->id_user);
        $stm->bindParam(":id_post", $info->id_post);
        $stm->bindParam(":comment", $info->comment);

        if ($stm->execute()) {
            return true;
        } else {
            return false;
        }
    }


    public function showComment($id)
    {
        $sql = "SELECT * FROM comments WHERE comments.id_post = '$id' ORDER BY comments.id_comment DESC";
        $array = [];
        foreach (parent::query($sql) as $row) {
            $array[] = $row;
        }
        return $array;
    }

    public function showPost($id)
    {
        $sql = "SELECT * FROM posts WHERE posts.id_kategory = '$id' ORDER BY posts.id_post DESC";
        $array = [];
        foreach (parent::query($sql) as $row) {
            $array[] = $row;
        }
        return $array;
    }

    public function showOnePost($id)
    {
        $sql = "SELECT * FROM posts WHERE posts.id_post = '$id'";
        $array = [];
        foreach (parent::query($sql) as $row) {
            $array[] = $row;
        }
        return $array;
    }

    public function showUserPost($id_user)
    {
        $sql = "SELECT * FROM posts WHERE posts.id_creator = '$id_user'";
        $array = [];
        foreach (parent::query($sql) as $row) {
            $array[] = $row;
        }
        return $array;
    }

    public function showUserKategory($id_kategory)
    {
        $sql = "SELECT `kategory`.`kategory_name` FROM `kategory`, `posts` WHERE `kategory`.`id_kategory` = '$id_kategory'";
        $array = [];
        foreach (parent::query($sql) as $row) {
            $array[] = $row;
        }
        return $array[0];
    }

    public function deletePost($id)
    {
        $sql = "DELETE FROM posts WHERE posts.id_post = (?)";
        $stm = parent::prepare($sql);
        $stm->execute([$id]);
    }

    public function showKategoryID($kategory)
    {
        $sql = "SELECT id_kategory FROM kategory WHERE kategory_name='$kategory'";
        $id = [];
        foreach (parent::query($sql) as $row) {
            $id = $row[0];
        }
        return $id;
    }

    public function deleteKategory($kategory)
    {
        $sql = "DELETE FROM kategory WHERE kategory.id_kategory = (?)";
        $stm = parent::prepare($sql);
        $stm->execute([$kategory]);
    }

    public function convertId($id_user)
    {
        $sql = "SELECT users.username FROM users WHERE id_user='$id_user'";
        $username = [];
        foreach (parent::query($sql) as $row) {
            $username = $row[0];
        }
        return $username;
    }

    public function showUser()
    {
        $sql = "SELECT id_user, username, admin, mail, ban FROM users WHERE 1";
        $array = [];
        foreach (parent::query($sql) as $row) {
            $array[] = $row;
        }
        return $array;
    }

    public function showBan($username)
    {
        $sql = "SELECT users.ban FROM users WHERE username='$username'";
        $ban = [];
        foreach (parent::query($sql) as $row) {
            $ban = $row[0];
        }
        return $ban;
    }

    public function showAdmin($username)
    {
        $sql = "SELECT users.admin FROM users WHERE username='$username'";
        $admin = [];
        foreach (parent::query($sql) as $row) {
            $admin = $row[0];
        }
        return $admin;
    }

    public function changeBan($id, $username)
    {
        if ($id == 1) {
            $sql = "UPDATE `users` SET `ban` = 0 WHERE `users`.`username` = (?)";
            $stm = parent::prepare($sql);
            $stm->execute([$username]);
        } else {
            $sql = "UPDATE `users` SET `ban` = 1 WHERE `users`.`username` = (?)";
            $stm = parent::prepare($sql);
            $stm->execute([$username]);
        }
    }

    public function changeAdmin($id, $username)
    {
        if ($id == 1) {
            $sql = "UPDATE `users` SET `admin` = 0 WHERE `users`.`username` = (?)";
            $stm = parent::prepare($sql);
            $stm->execute([$username]);
        } else {
            $sql = "UPDATE `users` SET `admin` = 1 WHERE `users`.`username` = (?)";
            $stm = parent::prepare($sql);
            $stm->execute([$username]);
        }
    }

    public function deleteUser($username)
    {
        $sql = "DELETE FROM users WHERE username = (?)";
        $stm = parent::prepare($sql);
        $stm->execute([$username]);
    }

    public function addKategory($kategory)
    {
        $sql = "INSERT INTO kategory (kategory_name) VALUES (?)";
        $stm = parent::prepare($sql);
        $stm->execute([$kategory]);
    }

    public function insertImages($image_name, $post_id)
    {
        $sql = "INSERT INTO images(image_name) VALUE (?)";
        $stm = parent::prepare($sql);
        $stm->execute([$image_name]);
        $sql = "SELECT id_image FROM images WHERE image_name = (?)";
        $stm = parent::prepare($sql);
        $stm->execute([$image_name]);
        $id_img = $stm->fetch();
        $sql = "INSERT INTO posts_images (id_post, id_image) VALUES (?, ?)";
        $stm = parent::prepare($sql);
        $stm->execute([$post_id, $id_img["id_image"]]);
    }

    public function showImages($id_post)
    {
        $sql = "SELECT images.image_name FROM images, posts, posts_images WHERE $id_post = posts_images.id_post AND images.id_image = posts_images.id_image GROUP BY images.image_name";
        $array = [];
        foreach (parent::query($sql) as $image) {
            $array[] = $image;
        }
        return $array;
    }

    public function editPost($id)
    {
        $sql = "SELECT * FROM posts WHERE posts.id_post = '$id'";
        $array = [];
        foreach (parent::query($sql) as $row) {
            $array[] = $row;
        }
        return $array;
    }

    public function updatePost($id, $post)
    {
        $title = $post->title;
        $content = $post->content;
        $sql = "UPDATE `posts` SET `title` = '$title', `content` = '$content' WHERE `posts`.`id_post` = (?)";
        $stm = parent::prepare($sql);
        $stm->execute([$id]);
    }

    public function showUserData($username)
    {
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $array = [];
        foreach (parent::query($sql) as $row) {
            $array[] = $row;
        }
        return $array;
    }

    public function showRate($id_post, $id_user)
    {
        $sql = "SELECT `rate` FROM `rating` WHERE `id_post` = '$id_post' AND `id_user` = '$id_user'";
        $array = [];
        foreach (parent::query($sql) as $row) {
            $array[] = $row;
        }
        return $array;
    }

    public function destroyRate($id_post, $id_user)
    {
            $sql = "DELETE FROM rating WHERE `id_post` = (?) AND `id_user` = (?)";
            $stm = parent::prepare($sql);
            $stm->execute([$id_post, $id_user]);
    }

    public function insertRate($id_post, $id_user)
    {
        $sql = "INSERT INTO rating(id_post, id_user, rate) VALUE (?, ?, 1)";
        $stm = parent::prepare($sql);
        $stm->execute([$id_post, $id_user]);
    }

    public function showLikes($id_post)
    {
        $sql = "SELECT COUNT(rate) FROM rating WHERE `id_post` = '$id_post'";
        $array = [];
        foreach (parent::query($sql) as $row) {
            $array[] = $row[0];
        }
        return $array;
    }
}
