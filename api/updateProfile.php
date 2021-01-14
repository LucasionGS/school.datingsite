<?php
require_once("../src/p/Models.php");
function updateProfile() {
  $token = $_REQUEST["token"];
  
  if (!isset($token)) {
    return new Result(false, "Specify a access token");
  }

  unset($_REQUEST["token"]);

  $res = User::loginByToken($token);
  if (!$res->success) {
    return new Result(false, "Access token is invalid");
  }

  /**
   * @var User
   */
  $thisUser = $res->data;
  if (!isset($thisUser)) {
    return new Result(false, "User not found");
  }
  $profile = $thisUser->getProfile();
  if (!isset($profile)) {
    return new Result(false, "Profile not found");
  }
  foreach ($_REQUEST as $key => $value) {
    $profile->$key = $value;
  }

  // return $profile;
  $res = $profile->updateProfile();
  return $res;
}
echo json_encode(updateProfile());