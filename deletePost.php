<?php
require_once("database.php");
$connection = new DBconnection();

if(isset($_SESSION["username"])){
    $opravneni = $connection->adminCheck($_SESSION["username"]);
    if($opravneni == 1 || $_GET["id3"] == $connection->showUserID($_SESSION["username"])){
        $connection->deletePost($_GET["id"]);
        $userPostCheck = $_GET["ur1"];
        echo $userPostCheck;
    if($userPostCheck == 1){
        header("Location: userPosts.php"); 
    }else{
        $adress = $_GET["id2"];
        header("Location: kategory.php?id=$adress");
}
}else {
        header("Location: kategory.php?id=$adress");
}}


