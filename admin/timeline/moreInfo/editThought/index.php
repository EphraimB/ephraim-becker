<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Ephraim Becker - Admin - Timeline - Edit Thought</title>
    <link rel="stylesheet" href="../../../../css/style.css" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="icon" href="../../../../img/ephraim_becker.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="../../../../img/ephraim-becker.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  </head>
  <body>
    <nav>
      <ul>
        <li id="first"><img src="../../../../img/ephraim-becker.jpg" alt="Photo of Ephraim Becker" width="70px" height="70px" /></li>
        <li id="hamburger-icon"><a href="#" onclick="toggleNavMenu()">&#9776;</a></li>
        <div id="links">
          <li><a href="../../../">Admin</a></li>
          <li class="focus"><a href="../../">Timeline</a></li>
        </div>
      </ul>
    </nav>
    <header>
      <h1 style="font-weight: bold;">Admin - Timeline - Edit Thought</h1>
    </header>
    <main>
      <form action="editThought.php" method="post">
        <div class="row">
          <?php
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
          ?>

        <div>
          <label for="eventDescription">Thought:</label>
          <br />
          <textarea id="thought" name="thought" rows="6" cols="50" required><?php echo $thought ?></textarea>
        </div>
        <br />
        <div>
          <h3>Event memory type:</h3>
          <div class="row">
            <div class="hidden-memory remembered-memory">
              <input type="checkbox" id="hidden" name="hidden" value="1" <?php if($hide == 1) { echo "checked"; } ?> />
              <label for="hidden">Hidden thought</label>
            </div>
          </div>
        </div>
        <input type="hidden" name="id" value="<?php echo $thoughtId ?>" />
        <br />
        <input type="submit" id="submit" value="Edit thought" />
        <br />
      </form>
    </main>
    <script src="../../../../js/script.js"></script>
    <script src="js/script.js"></script>
  </body>
</html>

<?php
  $sql->close();
  $link->close();
 ?>
