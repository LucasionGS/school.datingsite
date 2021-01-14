<?php
header("Access-Control-Allow-Origin: *");
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
   * @return Result<User>
   */
  public static function loginByToken($token)
  {
    /**
     * @var User
     */
    $user = User::findByToken($token);
    if ($user == null)
      return new Result(false, "The token used isn't valid");

    return new Result(true, "Successfully signed in to " . $user->username, $user);
  }

  public function requestPassword()
  {
    global $sql;
    $o = $sql->query("SELECT `password` FROM `users` WHERE `accessToken` = " . "\"$this->accessToken\"");
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
      \"$token\",
      0
    );
    ");

    return new Result($o, "", $token);
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
    } while (($sql->query("SELECT `accessToken` FROM `users` WHERE `accessToken` = \"$token\" LIMIT 0,1"))->num_rows > 0);
    return $token;
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
  public $likes = null;
  public $pfp = null;
  public $bio = null;
  public $birthdate = null;
  public $country = null;
  public $height = null;
  public $weight = null;
  public $username = null;
  public function __construct($data) {
    $this->id = (int)$data["id"];
    $this->fullName = (string)$data["fullName"];
    $this->likes = (int)$data["likes"];
    $this->pfp = (string)$data["pfp"];
    $this->bio = (string)$data["bio"];
    $this->birthdate = new DateTime($data["birthdate"]);
    $this->country = (string)$data["country"];
    $this->height = (int)$data["height"];
    $this->weight = (int)$data["weight"];
    $this->username = (string)$data["username"];
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
    $profiles = Profile::findByIds([$id]);
    return isset($profiles[0]) ? $profiles[0] : null;
  }

  /**
   * @return Profile
   */
  public static function findByUsername(string $username)
  {
    $profiles = Profile::findByUsernames([$username]);
    return isset($profiles[0]) ? $profiles[0] : null;
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
      return "`id` = '$id'";
    }, $ids));
    if (count($conditions) == 0) return [];
    $o = $sql->query("SELECT * FROM `fullprofiles` WHERE $conditions");
    $profilesFound = $o->fetch_all(MYSQLI_ASSOC);
    return array_map(function($data) {
      return Profile::mapToProfile($data);
    }, $profilesFound);
  }
  
  /**
   * Find multiple profiles by their usernames.
   * @param int[] $id
   * @return Profile[]
   */
  public static function findByUsernames(array $usernames)
  {
    global $sql;
    $conditions = implode(" OR ", array_map(function($username) use ($sql) {
      return "`username` LIKE '" . $sql->escape_string($username) ."'";
    }, $usernames));
    if (count($conditions) == 0) return [];
    $o = $sql->query("SELECT * FROM `fullprofiles` WHERE $conditions");
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
    $o = $sql->query("SELECT * FROM `fullprofiles`");
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
    global $sql;
    $o = $sql->query($search->generateQuery($page));
    $profiles = $o->fetch_all(MYSQLI_ASSOC);
    return array_map(function ($profile) {
      return Profile::mapToProfile($profile);
    }, $profiles);
  }

  public function isLikedBy(int $id) {
    global $sql;
    $o = $sql->query("SELECT `id` FROM `likes` WHERE `liker` = $id AND `liked` = ". $this->id ." LIMIT 0, 1");
    $profilesFound = $o->fetch_all(MYSQLI_ASSOC);
    return isset($profilesFound[0]);
  }

  public function likedBy() {
    global $sql;
    $o = $sql->query("SELECT * FROM `fullprofiles` WHERE `id` IN (SELECT `liker` FROM `likes` WHERE `liked` = ". $this->id .")");
    $profilesFound = $o->fetch_all(MYSQLI_ASSOC);
    return array_map(function ($profile) {
      return Profile::mapToProfile($profile);
    }, $profilesFound);
  }

  /**
   * @param int $likerId ID of the user the like request came from.
   * @param bool $state State of the like. `true` means setting a like, `false` removes it.
   */
  public function setLike(int $likerId, bool $state) {
    global $sql;
    $liker = Profile::findById($likerId);
    if (!isset($liker)) return new Result(false, "Profile not found.");
    
    $likedAlready = $this->isLikedBy($likerId);
    if ($state) {
      if (!$likedAlready) {
        $success = $sql->query("INSERT INTO `likes` (`liker`, `liked`) VALUES ($liker->id,$this->id)");
        if ($success) {
          return new Result(true);
        }
        else {
          return new Result(false, "Unable to INSERT like state");
        }
      }
    }
    else {
      if ($likedAlready) {
        $success = $sql->query("DELETE FROM `likes` WHERE `liker` = $liker->id AND `liked` = ". $this->id ."");
        if ($success) {
          return new Result(true);
        }
        else {
          return new Result(false, "Unable to DELETE like state");
        }
      }
    }
  }
  
  public function isMatch(int $profileId) {
    global $sql;
    $id1 = $this->id;
    $id2 = $profileId;

    $o1 = $sql->query("SELECT * FROM `likes` WHERE `liker` = $id1 AND `liked` = $id2 LIMIT 0,1");
    $o2 = $sql->query("SELECT * FROM `likes` WHERE `liker` = $id2 AND `liked` = $id1 LIMIT 0,1");

    $res1 = $o1->fetch_all(MYSQLI_ASSOC);
    $res2 = $o2->fetch_all(MYSQLI_ASSOC);
    
    if (isset($res1[0]) && isset($res2[0])) return true;
    else return false;
  }

  public function updateProfile()
  {
    global $sql;
    $sqlUpdateCode = "";
    foreach ($this as $key => $value) {
      switch ($key) {
        // Strings
        case "fullName":
        case "pfp":
        case "bio":
        case "country":
          $sqlUpdateCode .= " `$key`=\"" . $sql->escape_string($value) . "\",";
          break;
        // Numbers
        case "height":
        case "likes":
        case "weight":
          $sqlUpdateCode .= " `$key`=" . $sql->escape_string($value) . ",";
          break;
              
        case "birthdate":
          if (is_string($value))
            $sqlUpdateCode .= " `$key`=\"" . $value . "\",";
          else
            $sqlUpdateCode .= " `$key`=\"" . $sql->escape_string($value->format("Y-m-d")) . "\",";
          break;
      }
    }
    $sqlUpdateCode = trim($sqlUpdateCode, ",");
    $q = "UPDATE `profiles` SET $sqlUpdateCode WHERE `id`=" . $this->id;
    $o = $sql->query($q);
    if ($o) {
      return new Result(true);
    }
    else {
      return new Result(false, "Unable to update profile.");
    }
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
    $this->minAge = isset($data["minAge"]) ? (int)$data["minAge"] : null;
    $this->maxAge = isset($data["maxAge"]) ? (int)$data["maxAge"] : null;

    // Height is in CM measures
    $this->minHeight = isset($data["minHeight"]) ? (int)$data["minHeight"] : null;
    $this->maxHeight = isset($data["maxHeight"]) ? (int)$data["maxHeight"] : null;

    // Weight is in KG measures
    $this->minWeight = isset($data["minWeight"]) ? (int)$data["minWeight"] : null;
    $this->maxWeight = isset($data["maxWeight"]) ? (int)$data["maxWeight"] : null;
  }

  function generateQuery($page = -1) {
    $queryItems = [];

    if ($page < -1) $page = -1;

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
      $minAge = date_sub(new DateTime(), DateInterval::createFromDateString("$age years"))->format("Y-m-d H:i:s");
      array_push($queryItems, "`birthdate` <= '$minAge'");
    }
    if (isset($this->maxAge)) {
      $age = $this->maxAge + 1;
      $maxAge = date_sub(new DateTime(), DateInterval::createFromDateString("$age years"))->format("Y-m-d H:i:s");
      array_push($queryItems, "`birthdate` >= '$maxAge'");
    }

    return "SELECT * FROM `fullprofiles`"
    . (count($queryItems) > 0 ? " WHERE " . implode(" AND ", $queryItems) : "")
    . ($page > -1 ? " LIMIT " . ($page * 30) . ",30" : "");
  }
}

class Result
{
  public $success;
  public $reason;
  public $data;
  public function __construct(bool $success, string $reason = null, $data = null)
  {
    $this->success = $success;
    if ($reason !== null) $this->reason = $reason;
    else unset($this->reason);

    if ($data !== null || is_array($data)) $this->data = $data;
    else unset($this->data);
  }
}
