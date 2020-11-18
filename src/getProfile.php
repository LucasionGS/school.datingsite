<?php
require_once("./p/Models.php");
function getProfile() {
  $token = $_REQUEST["token"];
  if (isset($token)) return User::loginByToken($token);
  
  $id = $_REQUEST["id"];
  
  if (!isset($_REQUEST["id"])) {
    return new Result(false, "Specify a user id");
  }

  $user = User::findById($id);

  if ($user != null) return new Result(true, null, $user);
  else return new Result(false, "User not found");
}
echo json_encode(getProfile());