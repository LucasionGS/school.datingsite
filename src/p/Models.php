<?php
$sql = new mysqli("localhost", "root", "", "school_datesite");

class User
{
  public $id = -1;
  public $username = "";
  public $email = "";
  public $accessToken = "";
  public function __construct(
    int $id,
    string $username,
    string $email,
    string $accessToken
  ) {
    $this->id = $id;
    $this->username = $username;
    $this->email = $email;
    $this->accessToken = $accessToken;
  }

  /**
   * @return Result
   */
  public static function login($username, $password)
  {
    $user = User::findByUsername($username);
    if ($user == null)
      return new Result(false, "User doesn't exist");
    if (!password_verify($password, $user->requestPassword()))
      return new Result(false, "Password is incorrect");

    return new Result(true, "Successfully signed in to " . $user->username, $user, "user");
  }

  /**
   * @return Result
   */
  public static function loginByToken($token)
  {
    $user = User::findByToken($token);
    if ($user == null)
      return new Result(false, "The token used isn't valid");

    return new Result(true, "Successfully signed in to " . $user->username, $user);
  }

  public function requestPassword()
  {
    global $sql;
    $o = $sql->query("SELECT `password` FROM `users` WHERE `id` = " . $this->id);
    $passwords = $o->fetch_all(MYSQLI_ASSOC);
    return isset($passwords[0]) ? $passwords[0]["password"] : null;
  }

  /**
   * @return User[]
   */
  public static function searchByUsername(string $username)
  {
    global $sql;
    $o = $sql->query("SELECT * FROM `users` WHERE `username` LIKE \"%" . $sql->escape_string($username) . "%\"");
    $users = $o->fetch_all(MYSQLI_ASSOC);
    return array_map(function ($user) {
      return User::mapToUser($user);
    }, $users);
  }

  /**
   * @return User
   */
  public static function findByUsername(string $username)
  {
    global $sql;
    $o = $sql->query("SELECT * FROM `users` WHERE `username` = \"" . $sql->escape_string($username) . "\" LIMIT 0, 1");
    $usersFound = $o->fetch_all(MYSQLI_ASSOC);
    return count($usersFound) == 1 ? User::mapToUser($usersFound[0]) : null;
  }
  /**
   * @return User
   */
  public static function findByToken(string $accessToken)
  {
    global $sql;
    $o = $sql->query("SELECT * FROM `users` WHERE `accessToken` = \"" . $sql->escape_string($accessToken) . "\" LIMIT 0, 1");
    $usersFound = $o->fetch_all(MYSQLI_ASSOC);
    return count($usersFound) == 1 ? User::mapToUser($usersFound[0]) : null;
  }

  /**
   * @return User
   */
  public static function findByEmail(string $email)
  {
    global $sql;
    $o = $sql->query("SELECT * FROM `users` WHERE `email` = \"" . $sql->escape_string($email) . "\" LIMIT 0, 1");
    $usersFound = $o->fetch_all(MYSQLI_ASSOC);
    return count($usersFound) == 1 ? User::mapToUser($usersFound[0]) : null;
  }

  /**
   * @return User
   */
  public static function findById(int $id)
  {
    global $sql;
    // return new Result(false, "brh", null, "user");
    $o = $sql->query("SELECT * FROM `users` WHERE `id` = $id LIMIT 0, 1");
    $usersFound = $o->fetch_all(MYSQLI_ASSOC);
    return count($usersFound) == 1 ? User::mapToUser($usersFound[0]) : null;
  }

  public static function newUser($username, $email, $password)
  {
    global $sql;
    $token = User::generateToken();

    $o = $sql->query("INSERT INTO `users` VALUES 
    (
      NULL,
      \"" . $sql->escape_string($username) . "\",
      \"" . $sql->escape_string($email) . "\",
      \"" . $sql->escape_string($password) . "\",
      \"$token\"
    )");

    return $o;
  }

  private static function generateToken()
  {
    global $sql;
    $token = "";
    do {
      $token = "";
      $tokenSegments = "ABCDEF1234567890";
      $tokenSegmentsLength = strlen($tokenSegments);
      for ($i = 0; $i < 16; $i++) {
        $token .= $tokenSegments[rand(0, $tokenSegmentsLength)];
      }
    } while (($sql->query("SELECT `accessToken` FROM `users` WHERE `accessToken` = \"$token\""))->num_rows > 0);
    return $token;
  }

  public function updateUser()
  {
    global $sql;
    $sqlUpdateCode = "";
    foreach ($this as $key => $value) {
      $sqlUpdateCode .= " `$key`=\"" . $sql->escape_string($value) . "\"";
    }
    $q = "UPDATE `users` SET $sqlUpdateCode WHERE `id`=" . $this->id;
    $o = $sql->query($q);

    return $o;
    // return $q;
  }

  public static function mapToUser(array $data)
  {
    return new User(
      $data["id"],
      $data["username"],
      $data["email"],
      $data["accessToken"],
    );
  }

  /**
   * @return User[]
   */
  public static function getAllUsers()
  {
    global $sql;
    $o = $sql->query("SELECT * FROM `users`");
    $users = $o->fetch_all(MYSQLI_ASSOC);
    return array_map(function ($user) {
      return User::mapToUser($user);
    }, $users);
  }
}

class Result
{
  public $success;
  public $reason;
  public $data;
  public function __construct(bool $success, $reason = null, $data = null)
  {
    $this->success = $success;
    if ($reason != null) $this->reason = $reason;
    else unset($this->reason);
    
    if ($data != null) $this->data = $data;
    else unset($this->data);
  }
}
