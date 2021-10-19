<?php
  session_start();

  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;

  if(!isset($_SESSION['username'])) {
    header("location: ../");
  }

  $title = "Ephraim Becker - Admin - Timeline - Edit Thought";
  $header = "Admin - Timeline - Edit Thought";
  $localStyleSheet = '<link rel="stylesheet" href="css/style.css" />';
  $localScript = '<script src="js/script.js"></script>';

  $body = '<form action="editThought.php" method="post">
        <div class="row">';

          $sql = $link->prepare("SELECT * FROM thoughts WHERE ThoughtId=?");
          $sql->bind_param("i", $id);

          $id = $_GET['id'];

          $sql->execute();

          $sqlResult = $sql->get_result();

          while($row = mysqli_fetch_array($sqlResult)){
            $hide = $row['hide'];
            $thoughtId = $row['ThoughtId'];
            $thought = $row['Thought'];
          }

    $body .= '<div>
          <label for="eventDescription">Thought:</label>
          <br />
          <textarea id="thought" name="thought" rows="6" cols="45" required>' . $thought . '</textarea>
        </div>
        <br />
        <div>
          <h3>Event memory type:</h3>
          <div class="row">
            <div class="hidden-memory remembered-memory">
              <input type="checkbox" id="hidden" name="hidden" value="1"';
              if($hide == 1) {
                $body .= "checked";
              }
              $body .= ' />
              <label for="hidden">Hidden thought</label>
            </div>
          </div>
        </div>
        <input type="hidden" name="id" value="' . $thoughtId . '" />
        <br />
        <input type="submit" id="submit" value="Edit thought" />
        <br />
      </form>';

  $sql->close();
  $link->close();

  require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
 ?>
