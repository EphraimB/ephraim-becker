<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Ephraim Becker - Admin - Timeline</title>
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
          <li class="focus"><a href="#">Timeline</a></li>
        </div>
      </ul>
    </nav>
    <header>
      <h1 style="font-weight: bold;">Admin - Timeline</h1>
    </header>
    <main>
      <div id="grid-container">
        <?php
        $sql = "SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime', DATE_FORMAT(EventDate - INTERVAL EventTimeZoneOffset SECOND, '%Y') AS Year FROM timeline GROUP BY Year ORDER BY EventDate ASC";
        $sqlResult = mysqli_query($link, $sql);

        while($row = mysqli_fetch_array($sqlResult)){
          $year = $row['Year'];
        ?>

        <div class="card album-cover" id="album-cover-<?php echo $year ?>" onclick="filterTimeline('<?php echo $year ?>')">
          <h2><?php echo $year ?></h2>
          <p>All the events in <?php echo $year ?> when I was <?php echo $year-1996 ?> years old</p>
        </div>

      <?php } ?>
      </div>
    </main>
    <footer>
      <p>&copy; 2021 Ephraim Becker</p>
    </footer>
    <script src="../../js/script.js"></script>
    <script src="js/ajax.js"></script>
  </body>
</html>
