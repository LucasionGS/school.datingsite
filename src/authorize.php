<?php
require_once("./p/Models.php");
function authorize() {
  $token = $_POST["token"];
  if (isset($token)) return User::loginByToken($token);
  
  $username = $_POST["username"];
  $password = $_POST["password"];
  
  if (!isset($_POST["username"]) || !isset($_POST["password"])) {
    return new Result(false, "Missing username or password");
  }

  return User::login($username, $password);
}
echo json_encode(authorize());