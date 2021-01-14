<?php
require_once("../src/p/Models.php");
function uploadProfilePicture() {
  $token = $_REQUEST["token"];
  if (isset($token)) {
    $result = User::loginByToken($token);
    if ($result->success) {
      /**
       * @var User
       */
      $user = $result->data;
      $profile = $user->getProfile();
      $dir = "../img/pfp/";
      $file = $_FILES["pfp"];
      if (!isset($file)) {
        return new Result(false, "No image attached");
      }

      $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
      $imageSize = getimagesize($file["tmp_name"]);

      if ($imageSize === false) {
        return new Result(false, "Uploaded file is not an image");
      }

      function randName() {
        $length = 7;
        $chars = "ABCDEFabcdef1234567890";
        $name = "";
        for ($i=0; $i < $length; $i++) { 
          $name .= $chars[rand(0, strlen($chars))];
        }

        return $name;
      }

      do {
        $name = "pfp_" . randName() . "." . $imageFileType;
      }
      while (is_file($dir.$name));

      if (move_uploaded_file($file["tmp_name"], $dir.$name)) {
        
        $profile->pfp = $name;
        $res = $profile->updateProfile();

        if ($res->success) {
          return new Result(true, null, $profile);
        }
        else {
          return new Result(false, "Failed to update profile.");
        }
      }
      else {
        return new Result(false, "Upload failed.");
      }
    }
    else {
      return new Result(false, "Token not valid.");
    }
  }
  else {
    return new Result(false, "No token supplied.");
  }
}
echo json_encode(uploadProfilePicture());