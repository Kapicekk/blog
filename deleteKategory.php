<?php
require_once("database.php");
$connection = new DBconnection();

if(isset($_SESSION["username"])){
    $opravneni = $connection->adminCheck($_SESSION["username"]);
    if($opravneni==1){
        $connection = new DBconnection();
        $connection->deleteKategory($_GET["id"]);
        header("Location: addKategory.php");
    }else{
        header("Location: addKategory.php");
    }
}else{
    header("Location: addKategory.php");
}
