<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/src/main.css">
  <title>Toxinder | Account</title>
</head>
<body>
  <?php include("./src/header.php") ?>
  <main id="content">
    <?php
    require_once("./src/p/Models.php");
    $username = $_GET["username"];
    if (!isset($username))
      header("Location: /login");
    ?>
    <h1 class="centerText">Account page!</h1>
    <p class="centerText">This is a placeholder page for when accounts become manageable.</p>
    <p class="centerText">
      <?php
      ?>
    </p>
  </main>
</body>
</html>