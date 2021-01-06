<?php
    require_once("database.php");
    $connection = new DBconnection();
    $user = $_SESSION["username"];
    $admin = $connection->adminCheck($user);

    if (!empty($_SESSION)) {
      if ($_SESSION["ban"] == 1) {
          header("Location: ban.php");
      }
  }

    try{
      if(empty($_SESSION["username"])){
        header("Location: index.php");
      }
}catch(PDOException $exception){
  $msg = "Chyba s databází zkuste to později";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title><?php echo strip_tags($user)?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="Styles/bootstrap.min.css">
</head>
<body>


  <div class="container">
    <h1 style="padding-top:5%;"><?php echo $user?></h1>
    <h2><a href="index.php">Zpět</a></h2>
    <div class="list-group" style="padding-top:2.5%;">
      <a href="edit.profile.php" class="list-group-item list-group-item-action">Změnit heslo</a>
      <a href="userPosts.php" class="list-group-item list-group-item-action">Mé příspěvky</a>
      <a href="userData.php" class="list-group-item list-group-item-action">Mé údaje</a>
        <?php
          if($admin==1):
        ?>
      <a href="addKategory.php" class="list-group-item list-group-item-action">Přidat kategorii</a>  
        <?php
          endif;
        ?>
    </div>
  </div>
</body>
</html>


