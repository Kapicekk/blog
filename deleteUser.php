<?php
require_once("database.php");
$connection = new DBconnection();

$admin = $connection->adminCheck($_SESSION["username"]);
if($admin==1){
    $connection->deleteUser($_GET["id"]);
    header("Location: administration.php");
}else{
    header("Location: index.php");
    }
