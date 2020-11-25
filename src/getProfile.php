<?php
require_once("./p/Models.php");
function getProfile() {
  // $token = $_REQUEST["token"];
  // if (isset($token)) return User::loginByToken($token);
  
  $id = $_REQUEST["id"];
  
  if (!isset($_REQUEST["id"])) {
    return new Result(false, "Specify a profile id");
  }

  if (is_numeric($id)) {
    $profile = Profile::findById($id);
  }
  else {
    $ids = array_filter(explode(",", $id), function($id) {
      return is_numeric($id);
    });
    $profile = Profile::findByIds($ids);
  }

  if ($profile != null) return new Result(true, null, $profile);
  else return new Result(false, "Profile not found");
}
echo json_encode(getProfile());