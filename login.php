<?php
require_once("include/main.php");

if(isLoggedIn()) {
  header("Location: /");
  die();
}

$errors = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if((!isset($_POST["username"]) || trim($_POST["username"]) === '') || (!isset($_POST["password"]) || trim($_POST["password"]) === '')) {
    array_push($errors, "No value given");
  }

  if(empty($errors)) {
    if(loginAccount($_POST['username'], $_POST['password'], $conn)) {
      array_push($errors, "Wrong username or password");
    } else {
      header("Location: /");
    }
  }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $CONFIG["name"];?> - Kosning</title>
  <link rel="stylesheet" type="text/css" href="/stylesheets/main.css" media="screen"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
</head>
<body>
  <div class="login">
    <h1>Innskráning</h1>
    <form method="POST" action="/login.php">
      <input type="text" name="username" placeholder="Notandanafn" <?php
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST["username"]) || trim($_POST["username"]) !== '')) {
        echo 'value="' . $_POST["username"] . '"';
      }
      ?>/>
      <input type="password" name="password" placeholder="Lykilorð"/>
      <input type="submit" name="submit" value="Skrá inn"/>
    </form>
    <?php
    foreach($errors as $message) {
      ?>
      <div class="error-box">
        <img src="/assets/icons/error-24px.svg"/>
        <p><?php echo $message ?></p>
      </div>
      <?php
    }
    ?>
  </div>
</body>
</html>
