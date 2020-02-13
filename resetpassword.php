<?php
require_once("include/main.php");

if(!isLoggedIn()) {
  header("Location: /");
  die();
}

$errors = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if((!isset($_POST["old_password"]) || trim($_POST["old_password"]) === '') || (!isset($_POST["new_password"]) || trim($_POST["new_password"]) === '')) {
    array_push($errors, "No value given");
  }

  if (strlen($_POST["new_password"]) < 8) {
    array_push($errors, "Password too short!");
  }

  if (!preg_match("#[0-9]+#", $_POST["new_password"])) {
    array_push($errors, "Password must include at least one number!");
  }

  if (!preg_match("#[a-zA-Z]+#", $_POST["new_password"])) {
    array_push($errors, "Password must include at least one letter!");
  }

  if(empty($errors)) {
    if(resetPassword($_SESSION["id"], $_POST['old_password'], $_POST['new_password'], $conn)) {
      array_push($errors, "Password incorrect");
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
  <title>5.X - Kosning</title>
  <link rel="stylesheet" type="text/css" href="/stylesheets/main.css" media="screen"/>
</head>
<body>
  <div class="login">
    <h1>Breyta aðgangsorði</h1>
    <form method="POST" action="/resetpassword.php">
      <input type="password" name="old_password" placeholder="Núverandi lykilorð"/>
      <input type="password" name="new_password" placeholder="Nýtt lykilorð"/>
      <input type="submit" name="submit" value="Breyta lykiorði"/>
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
