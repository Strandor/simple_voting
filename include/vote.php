<?php
require_once ("db.php");

function getUser($id, $conn) {
  $sql = $conn->prepare("SELECT name FROM users WHERE id=?");
  $sql->bind_param('i', $id);
  $sql->execute();

  $result = $sql->get_result();
  if ($row = $result->fetch_assoc()) {
      return array("name" => $row["name"]);
  }
  return 0;
}

function addIdea($id, $target, $idea, $conn) {
  $sql = $conn->prepare('INSERT INTO `ideas`(`user`, `target`, `idea`) VALUES (?,?,?)');
  $sql->bind_param('iis', $id, $target, $idea);
  $sql->execute();
}

function getIdeas($id, $conn) {
  $sql = $conn->prepare("SELECT id, idea FROM ideas WHERE target=?");
  $sql->bind_param('i', $id);
  $sql->execute();

  $result = $sql->get_result();
  $ideas = array();
  while ($row = $result->fetch_assoc()) {
    array_push($ideas, array("idea" => $row["idea"], "id" => $row["id"]));
  }
  return $ideas;
}

function getIdNotVoted($conn) {
  $sql = $conn->prepare("SELECT id FROM `users` WHERE id != ? AND id NOT IN (SELECT target FROM votes WHERE user = ?)");
  $sql->bind_param('ii', $_SESSION["id"], $_SESSION["id"]);
  $sql->execute();


  $result = $sql->get_result();
  while ($row = $result->fetch_assoc()) {
    return $row["id"];
  }
  return null;
}

function getUsers($conn) {
  $sql = $conn->prepare("SELECT id, name FROM users WHERE id!=?");
  $sql->bind_param('i', $_SESSION["id"]);
  $sql->execute();

  $result = $sql->get_result();
  $users = array();
  while ($row = $result->fetch_assoc()) {
    array_push($users, array("name" => $row["name"], "id" => $row["id"]));
  }
  return $users;
}

function checkIfVoted($id, $conn) {
  $sql = $conn->prepare("SELECT id FROM votes WHERE target=? AND user=?;");
  $sql->bind_param('ii', $id, $_SESSION["id"]);
  $sql->execute();

  $result = $sql->get_result();
  if ($result->fetch_assoc() > 0) {
    return true;
  }
  return false;
}

// TODO FIX
function ideaExist($id, $voting) {
  return true;
}

function addScore($id, $voting, $conn) {
  foreach($voting as $key => $idea) {
    $sql = $conn->prepare('INSERT INTO `votes`(`target`, `user`, `idea`, `score`) VALUES (?, ?,?,?);');
    $score = 4 - $key;
    $sql->bind_param('iiii', $id, $_SESSION["id"], $idea, $score);
    $sql->execute();
  }
}
?>
