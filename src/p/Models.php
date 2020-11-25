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
    $o = $sql->query("SELECT * FROM `users` WHERE `id` = $id LIMIT 0, 1");
    $usersFound = $o->fetch_all(MYSQLI_ASSOC);
    return count($usersFound) == 1 ? User::mapToUser($usersFound[0]) : null;
  }

  public static function newUser($username, $email, $password)
  {
    global $sql;
    $token = User::generateToken();

    $o = $sql->multi_query("INSERT INTO `users` VALUES 
    (
      NULL,
      \"" . $sql->escape_string($username) . "\",
      \"" . $sql->escape_string($email) . "\",
      \"" . $sql->escape_string($password) . "\",
      \"$token\"
    );
    ");

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

  public function getProfile() {
    return Profile::findByid($this->id);
  }
}

class Profile
{
  public $id = -1;
  public $fullName = null;
  public $likedBy = null;
  public $pfp = null;
  public $bio = null;
  public $birthdate = null;
  public $height = null;
  public $weight = null;
  public function __construct($data) {
    $this->id = (int)$data["id"];
    $this->fullName = (string)$data["fullname"];
    $this->likedBy = (string)$data["likedBy"];
    $this->pfp = (string)$data["pfp"];
    $this->bio = (string)$data["bio"];
    $this->birthdate = new DateTime($data["birthdate"]);
    $this->height = (int)$data["height"];
    $this->weight = (int)$data["weight"];
  }

  public static function mapToProfile(array $data)
  {
    return new Profile($data);
  }

  /**
   * @return Profile
   */
  public static function findById(int $id)
  {
    global $sql;
    $o = $sql->query("SELECT * FROM `profiles` WHERE `id` = $id LIMIT 0, 1");
    $profilesFound = $o->fetch_all(MYSQLI_ASSOC);
    return count($profilesFound) == 1 ? Profile::mapToProfile($profilesFound[0]) : null;
  }

  /**
   * Find multiple profiles by their ids.
   * @param int[] $id
   * @return Profile[]
   */
  public static function findByIds(array $ids)
  {
    global $sql;
    $conditions = implode(" OR ", array_map(function($id) {
      return "`id` = $id";
    }, $ids));
    $o = $sql->query("SELECT * FROM `profiles` WHERE $conditions");
    $profilesFound = $o->fetch_all(MYSQLI_ASSOC);
    return array_map(function($data) {
      return Profile::mapToProfile($data);
    }, $profilesFound);
  }

  /**
   * @return Profile[]
   */
  public static function getAllProfiles()
  {
    global $sql;
    $o = $sql->query("SELECT * FROM `profiles`");
    $profiles = $o->fetch_all(MYSQLI_ASSOC);
    return array_map(function ($profile) {
      return Profile::mapToProfile($profile);
    }, $profiles);
  }
  
  /**
   * @return Profile[]
   */
  public static function searchProfiles(ProfileSearch $search, int $page = 1)
  {
    // global $sql;
    // $o = $sql->query("SELECT * FROM `profiles` WHERE `fullname` LIKE \"%Lucas%\"");
    // $profiles = $o->fetch_all(MYSQLI_ASSOC);
    // return array_map(function ($profile) {
    //   return Profile::mapToProfile($profile);
    // }, $profiles);
  }
}

class ProfileSearch {
  public $minAge;
  public $maxAge;
  public $minHeight;
  public $maxHeight;
  public $minWeight;
  public $maxWeight;
  public function __construct($data) {
    // Age is in years
    $this->minAge = (int)$data["minAge"];
    $this->maxAge = (int)$data["maxAge"];

    // Height is in CM measures
    $this->minHeight = (int)$data["minHeight"];
    $this->maxHeight = (int)$data["maxHeight"];

    // Weight is in KG measures
    $this->minWeight = (int)$data["minWeight"];
    $this->maxWeight = (int)$data["maxWeight"];
  }

  function generateQuery() {
    $queryItems = [];

    // Weight
    if (isset($this->minWeight)) {
      $minWeight = $this->minWeight;
      array_push($queryItems, "`weight` >= $minWeight");
    }
    if (isset($this->maxWeight)) {
      $maxWeight = $this->maxWeight;
      array_push($queryItems, "`weight` <= $maxWeight");
    }

    // Height
    if (isset($this->minHeight)) {
      $minHeight = $this->minHeight;
      array_push($queryItems, "`height` >= $minHeight");
    }
    if (isset($this->maxHeight)) {
      $maxHeight = $this->maxHeight;
      array_push($queryItems, "`height` <= $maxHeight");
    }

    // Age
    if (isset($this->minAge)) {
      $age = $this->minAge;
      $minAge = date_sub(new DateTime(), date_interval_create_from_date_string("$age years"));
      array_push($queryItems, "`age` >= $minAge");
    }
    if (isset($this->maxAge)) {
      $age = $this->maxAge;
      $maxAge = date_sub(new DateTime(), date_interval_create_from_date_string("$age years"));
      array_push($queryItems, "`age` <= $maxAge");
    }

    return implode(" AND ", $queryItems);
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
