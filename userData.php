<?php
require_once("login.class.php");
$connection = new DBconnection();

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
  $msg = "Chyba s databází zkuste to později".$exception->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Údaje</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="Styles/bootstrap.min.css">
</head>
<body>

<br>

<div class="container">
  <h3><a href="user.php">Zpět</a></h3>
  <br>


  <div class="tab-content">
    <div id="home" class="container tab-pane active"><br>
      <h3>Mé údaje:</h3>
        <div><br>

            <?php foreach($connection->showUserData($_SESSION["username"]) as $user): ?>
                
                <?php
                
                        if($user["admin"] == 1){
                            $admin="Admin";
                        }else{$admin="Uživatel";}
                ?>

                <h4>Uživatelské jméno: <b><?php echo $user['username']?></b></h4><br>
                <h4>E-mail: <b><?php echo $user['mail']?></b></h4><br>
                <h4>Status: <b><?php echo $admin?></b></h4><br>
            <?php endforeach; ?>
        </div>
    </div>
  </div>
</div>

</body>
</html>