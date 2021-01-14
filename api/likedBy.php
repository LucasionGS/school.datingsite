<?php
require_once("../src/p/Models.php");
function likedBy() {
  $id = $_REQUEST["id"];
  $username = $_REQUEST["username"];
  
  if (!isset($id) && !isset($username)) {
    return new Result(false, "Specify a profile id or username");
  }

  if (isset($id)) {
    if (is_numeric($id)) {
      $profile = Profile::findById($id);
      if ($profile !== null) $likedBy = $profile->likedBy();
    }
  }

  else if (isset($username)) {
    $profile = Profile::findbyUsername($username);
    if ($profile !== null) $likedBy = $profile->likedBy();
  }

  if ($profile === null) return new Result(false, "Profile not found");
  else return new Result(true, null, $likedBy);
}
echo json_encode(likedBy());