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
  $sql = $conn->prepare("SELECT idea FROM ideas WHERE target=?");
  $sql->bind_param('i', $id);
  $sql->execute();

  $result = $sql->get_result();
  $ideas = array();
  while ($row = $result->fetch_assoc()) {
    array_push($ideas, array("idea" => $row["idea"]));
  }
  return $ideas;
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
?>
