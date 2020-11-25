<?php
require_once("./p/Models.php");
function getProfile() {
  // $token = $_REQUEST["token"];
  // if (isset($token)) return User::loginByToken($token);
  
  $id = $_REQUEST["id"];
  
  if (!isset($_REQUEST["id"])) {
    return new Result(false, "Specify a profile id");
  }

  try {
    $profile = Profile::findById($id);
  } catch (\Throwable $th) {
    print_r($th);
  }

  if ($profile != null) return new Result(true, null, $profile);
  else return new Result(false, "Profile not found");
}
echo json_encode(getProfile());