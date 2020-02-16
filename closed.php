<?php
require_once("include/main.php");

if(getState() !== state::CLOSED) {
  header("Location: /");
  exit;
}

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
    <h1>Síða lokuð</h1>
  </div>
</body>
</html>
<?php
$conn->close();
?>
