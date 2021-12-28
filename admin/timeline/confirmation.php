<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;

  $id = $_GET['id'];
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Ephraim Becker - Admin - Timeline - Delete?</title>
    <link rel="stylesheet" href="../../css/style.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="icon" href="../../img/ephraim_becker.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="../../img/ephraim-becker.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  </head>
  <body>
    <nav>
      <ul>
        <li id="first"><img src="../../img/ephraim-becker.jpg" alt="Photo of Ephraim Becker" width="70px" height="70px" /></li>
        <li id="hamburger-icon"><a href="#" onclick="toggleNavMenu()">&#9776;</a></li>
        <div id="links">
          <li><a href="../">Admin</a></li>
          <li class="focus"><a href="index.php">Timeline</a></li>
        </div>
      </ul>
    </nav>
    <header>
      <h1 style="font-weight: bold;">Admin - Timeline - Delete?</h1>
    </header>
    <main>
      <?php
        $sql = $link->prepare("SELECT EventTitle FROM timeline WHERE TimelineId=?");
        $sql->bind_param("i", $id);

        $id = $_GET['id'];

        $sql->execute();

        $sqlResult = $sql->get_result();

        while($row = mysqli_fetch_array($sqlResult)){
          $eventTitle = $row['EventTitle'];
        }
       ?>
      <h2>Are you sure you want to delete the event named "<?php echo $eventTitle ?>"?</h2>

      <div class="row actionButtons">
        <a class="keep" href="index.php">No</a>
        <a class="delete" href="deleteEvent.php?id=<?php echo $id ?>">Yes</a>
      </div>
    </main>
  </body>
</html>
