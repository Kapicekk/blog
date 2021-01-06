<?php
require_once("database.php");
$connection = new DBconnection();

try{
    $ban = $connection->banCheck($_SESSION["username"]);
        if($ban==0 || empty($_SESSION["username"])){
            header("Location: index.php");
        }
  }catch(PDOException $exception){
  $msg = "Chyba s databází zkuste to později".$exception->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="Styles/bootstrap.min.css">
    <title>Ban</title>
</head>
<body>
    <div class="container">
        <h2>Váš účet byl zabanován!</h2>
        <h4><a href="logout.php">Odhlásit se</a></h4>
    </div>
</body>
</html>