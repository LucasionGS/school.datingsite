<?php
require_once("../src/p/Models.php");
function setLike() {
  $token = $_REQUEST["token"];
  $id = $_REQUEST["id"];
  $state = $_REQUEST["state"];
  
  if (!isset($token)) {
    return new Result(false, "Specify a access token");
  }
  if (!isset($id) || !is_numeric($id)) {
    return new Result(false, "Specify a valid profile id");
  }
  if (!isset($state) || !($state == 0 || $state == 1)) {
    return new Result(false, "Specify a valid state id");
  }

  $res = User::loginByToken($token);
  if (!$res->success) {
    return new Result(false, "Access token is invalid");
  }

  /**
   * @var User
   */
  $thisUser = $res->data;
  $profile = Profile::findById($id);
  
  // Convert state from int to boolean.
  $state = $state == 1 ? true : false;

  if ($profile !== null) $profile->setLike($thisUser->id, $state);
  return new Result(true, "Like state updated", $state ? $profile->isMatch($thisUser->id) : false);
}
echo json_encode(setLike());