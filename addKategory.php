<?php
    require_once("user.class.php");
    require_once("database.php");

    if (!empty($_SESSION)) {
      if ($_SESSION["ban"] == 1) {
          header("Location: ban.php");
      }
  }
  
    $msg = "";
    $connection = new DBconnection();
    try{
      if(!empty($_SESSION)){
        if($connection->banCheck($_SESSION["username"]) == 1){
            header("Location: user.php");
        }}

        $admin = $connection->adminCheck($_SESSION["username"]);
        if($admin!=1 || empty($_SESSION["username"])){
            header("Location: index.php");
        }
        if(isset($_POST["send"])){
            $post = strip_tags($_POST["kategory"]);
            $connection->addKategory($post);
        }
}catch(PDOException $exception){
    $msg = "Chyba s databází zkuste to později";
}

    
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Přidávání kategorie</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="Styles/bootstrap.min.css">
</head>
<body>

<div class="container mt-3">
    <form method="post">
        <h1>Přidání kategorie</h1>
        <h2><a href="user.php">Zpět</a></h2>
    <div class="form-group">
        <div class="form-group">
            <label for="email">Název kategorie:</label>
            <input class="form-control" type="text" name="kategory" placeholder="Zadejte název kategorie" required>
        </div>
        <input class="btn btn-primary" type="submit" name="send" value="Přidat">
        <p><?php if($msg!=""){echo $msg;} ?></p>
    </div>

    <table class="table">
  
  <thead>

<h3 style="text-align:center;">Kategorie</h3>

  </thead>
  <tbody style="text-align:center;">
  <?php foreach($connection->showKategory() as $kategory): ?>
  <?php $kategoryID = $connection->showKategoryID($kategory['kategory_name']); ?>
    <tr>
      <td><?php echo $kategory["kategory_name"] ?></td>
      <td><a href="deleteKategory.php?id=<?php echo $kategoryID?>">Smazat</a></td>
    </tr>
    <?php endforeach;?>
  </tbody>
</table>
</div>

  </form>
</div>
</body>
</html>


