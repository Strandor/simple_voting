<?php
require_once("include/main.php");
requireLogin();

require_once("include/vote.php");

$errors = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if((!isset($_POST["idea"]) || trim($_POST["idea"]) === '') || (!isset($_GET["id"]) || trim($_GET["id"]) === '')) {
    array_push($errors, "No value given");
  } else if(strlen($_POST["idea"]) > 50) {
    array_push($errors, "String too long");
  }

  if(empty($errors)) {
      if(!getUser($_GET["id"], $conn) == 0) {
        addIdea($_SESSION["id"], $_GET["id"], $_POST["idea"], $conn);
      }
  }
}

$users = getUsers($conn);
?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $CONFIG["name"];?> - Kosning</title>
  <link rel="stylesheet" type="text/css" href="/stylesheets/main.css" media="screen"/>
</head>
<body>
  <?php
    if(!isset($_GET["id"]) || trim($_GET["id"]) === '') {
      if(getState() === state::IDEAS) {?>
        <div>
          <h1>Uppástungur</h1>
          <div class="list_of_voters">
            <table>
              <tr>
                <th>Nafn</th>
              </tr>
              <?php
              foreach($users as $user) {?>
                <tr>
                  <th><a href="/vote.php?id=<?php echo $user["id"];?>"><?php echo $user["name"]; ?></th>
                </tr>
              <?php } ?>
            </table>
            <p style="color: white;">ATH: Þetta er aðeins uppástungur. Kosning verður tilkynnt bráðlega.</p>
          </div>
        </div>
      <?php } else {
        die('hello');
      }
    } else {
      if($_GET["id"] == $_SESSION["id"]) {
          header("Location: /");
          die();
      }

      $user = getUser($_GET["id"], $conn);

      if($user === 0) {
        header("Location: /");
      }

      $ideas = getIdeas($_GET["id"], $conn);
      ?>
      <div>
        <a href="/vote.php" class="back">Til baka</a>
        <h1><?php echo $user["name"] ?></h1>
        <div class="list_of_voters">
          <table>
            <tr>
              <th>Hugmyndir:</th>
            </tr>
            <?php
            $exists = false;
            foreach($ideas as $idea) {
              $exists = true;
              ?>
              <tr>
                <th><?php echo htmlspecialchars($idea["idea"], ENT_QUOTES, 'UTF-8'); ?></th>
              </tr>
              <?php
            }
            if(!$exists) {
              ?>
              <tr>
                <th>Engar hugmyndir enn :(</th>
              </tr>
              <?php
            }
            ?>
          </table>
        </div>
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
        <div class="idea">
          <p>Sentu inn hugmynd</p>
          <form method="POST" action="/vote.php?id=<?php echo $_GET["id"] ?>">
            <input type="text" name="idea" placeholder="Hugmynd">
            <input type="submit" name="submit" placeholder="Senda inn">
          </form>
        </div>
      <?php
    }
  ?>
</body>
</html>
<?php
$conn->close();
?>
