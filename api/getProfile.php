<?php
require_once("../src/p/Models.php");
function getProfile() {
  // $token = $_REQUEST["token"];
  // if (isset($token)) return User::loginByToken($token);
  
  $id = $_REQUEST["id"];
  $username = $_REQUEST["username"];
  
  if (!isset($id) && !isset($username)) {
    return new Result(false, "Specify a profile id or username");
  }

  if (isset($id)) {
    if (is_numeric($id)) {
      $profile = Profile::findById($id);
    }
    else {
      $ids = array_filter(explode(",", $id), function($id) {
        return is_numeric($id);
      });
      $profile = Profile::findByIds($ids);
    }
  }

  else if (isset($username)) {
    if (strpos($username, ",") === false) {
      $profile = Profile::findbyUsername($username);
    }
    else {
      $profile = Profile::findbyUsernames(explode(",", $username));
    }
  }

  if ($profile != null) return new Result(true, null, $profile);
  else return new Result(false, "Profile not found");
}
echo json_encode(getProfile());