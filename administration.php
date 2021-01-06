<?php
require_once("login.class.php");
$connection = new DBconnection();
$i=0;

if (!empty($_SESSION)) {
  if ($_SESSION["ban"] == 1) {
      header("Location: ban.php");
  }
}

try{
  
    $admin = $connection->adminCheck($_SESSION["username"]);
        if($admin!=1){
            header("Location: index.php");
        }
  }catch(PDOException $exception){
  $msg = "Chyba s databází zkuste to později".$exception->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Administrace</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="Styles/bootstrap.min.css">
</head>
<body>

<div class="container">
  <br>

  <h2>Administrace</h2>   

  <br>

  <h3><a href="index.php">Zpět</a></h3>
  <table class="table">
  
    <thead>
      <tr>
        <th>Jméno</th>
        <th>Email</th>
        <th>Admin</th>
        <th>Ban</th>
        <th> </th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($connection->showUser() as $user): ?>
      <?php $i++; ?>
        <?php
        if($user["ban"] == 1){
            $ban="ano";
        }else{$ban="ne";}

        if($user["admin"] == 1){
            $admin="admin";
        }else{$admin="uživatel";}
        ?>
      <tr>
        <td><?php echo $user["username"] ?></td>
        <td><?php echo $user["mail"] ?></td>
        <?php if($i!=1): ?>
        <td><a href="changeStatus.php?id=<?php echo $user['username']?>"><?php echo $admin ?></a></td>
        <?php else: ?>
        <td><?php echo $admin ?></td> 
        <?php endif; ?>
        
        <?php if($i!=1): ?>
        <td><a href="changeBan.php?id=<?php echo $user['username']?>"><?php echo $ban ?></a></td>
        <?php else: ?>
          <td><?php echo $ban ?></td> 
        <?php endif; ?>

        <?php if($i!=1): ?>
        <td><a href="deleteUser.php?id=<?php echo $user['username']?>">Smazat</a></td>
        <?php else: ?>
        <td>Nelze smazat</td> 
        <?php endif; ?>
      </tr>
      <?php endforeach;?>
    </tbody>
  </table>
</div>

</body>
</html>