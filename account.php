<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/src/main.css">
  <title>Toxinder | Account</title>
</head>
<body>
  <?php
  include("./src/header.php");
  require_once("./src/p/Models.php");
  $username = $_GET["username"];
  if (!isset($username))
    header("Location: /login");
  ?>
  <main id="content">
    <div>
      <div class="info"></div>
      <div class="info"></div>
    </div>
  </main>
</body>
</html>