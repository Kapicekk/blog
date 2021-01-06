<?php
require_once("database.php");
$connection = new DBconnection();

if(isset($_SESSION["username"])){
    if($_GET["id2"] == $connection->showUserID($_SESSION["username"])){
        $id = $_GET["id"];
        if(!empty($connection->showRate($_GET["id"], $_GET["id2"]))){
            $connection->destroyRate($_GET["id"], $_GET["id2"]);
        }else{
            $connection->insertRate($_GET["id"], $_GET["id2"]);
        }
        header("Location: post.php?postId=$id");
    }else{
        header("Location: index.php");
    }

}else{
    header("Location: index.php");
}