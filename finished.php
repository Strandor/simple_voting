<?php
require_once("include/main.php");
requireOpen();
requireLogin();
if(!isset($_SESSION["finished_voting"]) || $_SESSION["finished_voting"] == false) {
  header("Location: /vote.php");
}
$_SESSION["finished_voting"] = false;
?>
<!DOCTYPE html>
<html>
<head>
  <?php
  include("include/meta.php");
  ?>
</head>
<body>
  <div class="finished">
    <h1>Atkvæðisgreiðslu lokið</h1>
    <p>Vel gert! Þú hefur klárað að greiða atkvæði við allt</p>
  </div>
</body>
</html>
<?php
$conn->close();
?>
