<?php
  session_start();
  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;

  if(!isset($_SESSION['username'])) {
    header("location: ../");
  }
  
  $id = $_GET['id'];
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Ephraim Becker - Admin - Timeline - Delete thought?</title>
    <link rel="stylesheet" href="../../../css/style.css" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="icon" href="../../../img/ephraim_becker.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="../../../img/ephraim-becker.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  </head>
  <body>
    <nav>
      <ul>
        <li id="first"><img src="../../../img/ephraim-becker.jpg" alt="Photo of Ephraim Becker" width="70px" height="70px" /></li>
        <li id="hamburger-icon"><a href="#" onclick="toggleNavMenu()">&#9776;</a></li>
        <div id="links">
          <li><a href="../../">Admin</a></li>
          <li class="focus"><a href="../">Timeline</a></li>
        </div>
      </ul>
    </nav>
    <header>
      <h1 style="font-weight: bold;">Admin - Timeline - Delete thought?</h1>
    </header>
    <main>
      <h2>Are you sure you want to delete this thought"?</h2>

      <?php
      $sqlTwo = $link->prepare("SELECT TimelineId FROM thoughts WHERE ThoughtId=? LIMIT 1");

      $sqlTwo->bind_param("i", $id);

      $sqlTwo->execute();

      $sqlTwoResult = $sqlTwo->get_result();

      while($row = mysqli_fetch_array($sqlTwoResult)) {
        $timelineId = $row['TimelineId'];
      }

      $sqlTwo->close();
      $link->close();
      ?>

      <div class="row actionButtons">
        <a class="keep" href="index.php?id=<?php echo $timelineId ?>">No</a>
        <a class="delete" href="deleteThought.php?id=<?php echo $id ?>">Yes</a>
      </div>
    </main>
  </body>
</html>
