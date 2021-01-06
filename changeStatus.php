<?php
require_once("database.php");
$connection = new DBconnection();

$adminn = $connection->adminCheck($_SESSION["username"]);
if($adminn==1){
    $admin = $connection->showAdmin($_GET["id"]);
    $connection->changeAdmin($admin, $_GET["id"]);
    header("Location: administration.php");
}else{
    header("Location: index.php");
    }
