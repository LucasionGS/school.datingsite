<?php
require_once("./p/Models.php");
function createUser() {
  $username = $_POST["username"];
  $email = $_POST["email"];
  $password = $_POST["password"];
  $confirmPassword = $_POST["confirmPassword"];

  if (!$username || $username == "")
    return new Result(false, "Username isn't set");
  if (!$email || $email == "")
    return new Result(false, "Email isn't set");
  // if (!preg_match("(\w+\.*?)+@\w+\.\w+", $email))
  //   return new Result(false, "Email isn't valid");
  if (!$password || $password == "")
    return new Result(false, "Password isn't set");
  if ($password != $confirmPassword)
    return new Result(false, "Password doesn't match");
    if (User::findByEmail($email) != null)
      return new Result(false, "$email is already registered");
  if (User::findByUsername($username) != null)
    return new Result(false, "$username already exists");

  $res = User::newUser($username, $email, password_hash($password, PASSWORD_DEFAULT));
  if ($res->success) {
    return new Result(true, "Successfully registered $username", ["token" => $res->data]);
  }
}

echo json_encode(createUser());