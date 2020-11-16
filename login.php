<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/src/main.css">
  <!-- <script defer async src="/src/models.js" ></script> -->
  <script defer async src="/src/login.js" ></script>
  <title>Toxen</title>
</head>
<body>
  <?php include("./src/header.php") ?>
  <?php
  $registerFirst = false;

  if (isset($_GET["register"])) $registerFirst = true;
  ?>
  <main id="content">
    <p class="center centerText error" id="feedback"></p>
    <div id="loginform" <?php echo $registerFirst ? "hidden" : ""; ?>>
      <form>
        <h3 class="center centerText" for="userInput">Username</h3>
        <input class="fancy center centerText" type="text" name="username" id="userInput" required>
        <br>
        <h3 class="center centerText" for="passwordInput">Password</h3>
        <input class="fancy center centerText" type="password" name="password" id="passwordInput" required>
        <h4 class="fancy center centerText"><a href="#signup" onclick="switchTo(1);">Need an account? Sign up!</a></h4>
        <button type="submit" class="fancy center centerText" onclick="event.preventDefault(); login();">Log in</button>
      </form>
    </div>

    <div id="signupform" <?php echo !$registerFirst ? "hidden" : ""; ?>>
      <form>
        <h3 class="center centerText" for="userSignUp">Username</h3>
        <input class="fancy center centerText" type="text" name="user" id="userSignUpInput" required>
        <br>
        <h3 class="center centerText" for="emailSignUp">Email</h3>
        <input class="fancy center centerText" type="email" name="email" id="emailSignUpInput" required>
        <br>
        <h3 class="center centerText" for="passwordSignUp">Password</h3>
        <input class="fancy center centerText" type="password" name="password" id="passwordSignUpInput" required>
        <br>
        <h3 class="center centerText" for="confirmPasswordSignUp">Confirm Password</h3>
        <input class="fancy center centerText" type="password" name="confirmPassword" id="confirmPasswordSignUpInput" required>
        <h4 class="fancy center centerText"><a href="#login" onclick="switchTo(0);">Already have an account?</a></h4>
        <button type="submit" class="fancy center centerText" onclick="event.preventDefault(); register()">Sign up</button>
      </form>
    </div>
  </main>
</body>
</html>
