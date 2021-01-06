<?php
require_once("database.php");
$connection = new DBconnection();

$admin = $connection->adminCheck($_SESSION["username"]);
if($admin==1){
    
    $ban = $connection->showBan($_GET["id"]);
    $connection->changeBan($ban, $_GET["id"]);
    header("Location: administration.php");
}else{
    header("Location: index.php");
    }
