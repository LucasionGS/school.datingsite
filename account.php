<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/src/main.css">
  <script defer async src="/src/account.js"></script>
  <title>Toxinder | Account</title>
</head>
<body>
  <?php
  include("./src/header.php");
  $username = $_GET["username"];
  if (!isset($username))
    header("Location: /login");
  ?>
  <main id="content">
    <div id="profileContent">
      <div class="profileInfo" id="infocard"></div>
      <div class="profileInfo" id="bio"></div>
    </div>
  </main>
</body>
</html>