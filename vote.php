<?php
require_once("include/main.php");
requireLogin();
require_once("include/vote.php");

$state = getState();

$errors = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if($state === STATE::IDEAS) {
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
  } else if(getState() === STATE::VOTING) {
    $voting = array();
    foreach($_POST as $key => $value) {
      if(is_int($key)) {
        if($value > 0 && $value < 4) {
          if(array_key_exists($value, $voting)) {
            array_push($errors, "Repeat of value");
            break;
          }
          $voting[$value] = $key;
        } else if($value != 0) {
          array_push($errors, "Unknown value given");
          break;
        }
      }
    }

    if(empty($voting)) {
      array_push($errors, "No vote given.");
    }

    if(empty($errors)) {
      if(ideaExist($_GET["id"], $voting)) {
        addScore($_GET["id"], $voting, $conn);
        header("Location: /redirectvote.php");
        exit;
      } else {
        array_push($errors, "Idea id given does not exists");
      }
    }
  }
}

$users = getUsers($conn);
?>
<!DOCTYPE html>
<html>
<head>
  <?php
  include("include/meta.php");
  ?>
</head>
<body>
  <?php
    if(!isset($_GET["id"]) || trim($_GET["id"]) === '') {
      if($state === state::IDEAS) {?>
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
      <?php } else { ?>
        <div class="start_vote">
          <h1>Kosning</h1>
          <div>
            <form method="POST" action="/redirectvote.php">
              <input type="submit" name="submit" value="Hefja kosningu"/>
            </form>
          </div>
        </div>
      <?php }
    } else {
      if($_GET["id"] == $_SESSION["id"]) {
          header("Location: /");
          die();
      }

      $user = getUser($_GET["id"], $conn);
      if($user === 0) {
        die('hello');
        header("Location: /");
      }

      if($state === state::IDEAS) {
        $ideas = getIdeas($_GET["id"], $conn);
        ?>
        <div>
          <a href="/vote.php" class="back">Til baka</a>
          <h1 class="name"><?php echo $user["name"] ?></h1>
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
      } else if($state === state::VOTING) {
        if(checkIfVoted($_GET["id"], $conn)) {
          header("Location: /");
        }

        $ideas = getIdeas($_GET["id"], $conn);
        ?>
        <div>
          <a href="/vote.php" class="back">Til baka</a>
          <h1 class="name"><?php echo $user["name"] ?></h1>
          <div class="list_of_voters">
            <form id="score-form" method="POST" action="/vote.php?id=<?php echo $_GET["id"] ?>">
            <table>
              <tr>
                <th>Hugmyndir:</th>
              </tr>
              <?php
              $exists = false;
              $length = count($ideas);
              foreach($ideas as $idea) {
                $exists = true;
                ?>
                <tr>
                  <th>
                    <select class="score-option" name="<?php echo $idea["id"]; ?>">
                      <option value="0"></option>
                      <?php
                      for($i = 1; $i <= $length && $i <= 3; $i++) {
                        echo '<option value="' . $i . '">' . $i .'</option>';
                      }
                      ?>
                    </select>
                    <?php echo htmlspecialchars($idea["idea"], ENT_QUOTES, 'UTF-8'); ?>
                  </th>
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
          </form>
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
            <p>Senda inn atkvæði</p>
            <button form="score-form" type="submit" name="submit" placeholder="Senda inn">Submit</button>
          </div>
          <?php
      }
    }
?>
<script src="/js/main.js"></script>
</body>
</html>
<?php
$conn->close();
?>
