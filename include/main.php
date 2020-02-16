<?php
session_start();
require_once ("db.php");

$CONFIG = array(
    'name' => '5.X',
    'is_open' => true,
    'date_of_vote' => mktime(12, 0, 0, 2, 17, 2020)
);

abstract class state
{
    const CLOSED = 0;
    const IDEAS = 1;
    const VOTING = 2;
}

function getState() {
  global $CONFIG;
  if($CONFIG["is_open"]) {
    if(time() - $CONFIG["date_of_vote"] > 0) {
      return state::VOTING;
    }
    return state::IDEAS;
  }
  return state::CLOSED;
}

function requireChangePassword() {
  if(isset($_SESSION["change_password"])) {
    return $_SESSION["change_password"];
  } else {
    return false;
  }
}

function changePassword($password) {
  $options = [
  'cost' => 11
  ];
  return password_hash($password, PASSWORD_BCRYPT, $options);
}

function logout() {
  $_SESSION["logged_in"] = false;
  $_SESSION["username"] = null;
  $_SESSION["id"] = null;
  $_SESSION["name"] = null;
  $_SESSION["change_password"] = null;
}

function isLoggedIn() {
  if(isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    return true;
  } else {
    return false;
  }
}

function requireOpen() {
  if(getState() === state::CLOSED) {
    header("Location: /closed.php");
    die();
  }
}

function requireLogin($redirect = true) {
  if(!isLoggedIn()) {
    header("Location: /login.php");
    die();
  }
  if($redirect === true && requireChangePassword()) {
    header("Location: /resetpassword.php");
    die();
  }
}

function resetpassword($id, $old_password, $new_password, $conn) {
  $sql = $conn->prepare("SELECT hashed_password FROM users WHERE id=?");
  $sql->bind_param('i', $id);
  $sql->execute();

  $result = $sql->get_result();
  if ($row = $result->fetch_assoc()) {
    if (password_verify($old_password, $row["hashed_password"])) {
      $sql = $conn->prepare("UPDATE users SET hashed_password=?, change_password = 0 WHERE id=?");
      $hashed = changePassword($new_password);
      $sql->bind_param('si', $hashed, $id);
      $sql->execute();
      $_SESSION["change_password"] = false;
      return 0;
    } else {
      return 1;
    }
  }
  return 1;
}

function loginAccount(string $username, string $password, $conn) {
  $sql = $conn->prepare("SELECT id, username, name, hashed_password, change_password FROM users WHERE username=?");
  $sql->bind_param('s', $username);
  $sql->execute();

  $result = $sql->get_result();
  if ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row["hashed_password"])) {
      $_SESSION["logged_in"] = true;
      $_SESSION["username"] = $row["username"];
      $_SESSION["name"] = $row["name"];
      $_SESSION["id"] = $row["id"];
      $_SESSION["change_password"] = $row["change_password"];

      if($row["change_password"] === 1) {
        header("Location: /resetpassword.php");
        exit;
      }
      return 0;
    }
  }
  return 1;
}
?>
