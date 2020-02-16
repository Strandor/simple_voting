<?php
require_once("include/main.php");
requireOpen();
requireLogin();
require_once("include/vote.php");
$id = getIdNotVoted($conn);
if($id !== null) {
  header("Location: /vote.php?id=" . $id);
} else {
  $_SESSION["finished_voting"] = true;
  header("Location: /finished.php");
}
exit;
?>
