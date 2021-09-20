<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Ephraim Becker - Timeline</title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="canonical" href="https://www.ephraimbecker.com/timeline/" />
    <link rel="icon" href="../img/ephraim_becker.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="../img/ephraim-becker.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Hi! My name is Ephraim Becker and here's a timeline about my life and how people can learn from it." />
    <meta name="keywords" content="Ephraim Becker, autism, aspergers, ADHD" />
  </head>
  <body>
    <nav>
      <ul>
        <li id="first"><img src="../img/ephraim-becker.jpg" alt="Photo of Ephraim Becker" width="70px" height="70px" /></li>
        <li id="hamburger-icon"><a href="#" onclick="toggleNavMenu()">&#9776;</a></li>
        <div id="links">
          <li><a href="../">Home</a></li>
          <li class="focus"><a href="#">Timeline</a></li>
          <div id="dropdown">
            <li><a href="#" onclick="toggleNavSubmenu()">Daily Life &emsp; &#x25BC;</a></li>
            <div id="dropdown-content">
              <li><a href="../everydayLife/">Everyday Life</a></li>
              <li><a href="../college/">College Life</a></li>
            </div>
          </div>
          <li><a href="../projects/">Projects</a></li>
          <li><a href="../resources/">Resources</a></li>
          <li><a href="../about/">About</a></li>
        </div>
      </ul>
    </nav>
    <header>
      <h1 style="font-weight: bold;">Timeline</h1>
    </header>
    <main>
      <table>
        <tr>
          <td rowspan="3">Legend</td>
          <td class="remembered-memory">Remembered memory</td>
        </tr>
        <tr>
          <td class="diary-memory">Diary memory</td>
        </tr>
      </table>
      <div id="grid-container">
        <?php
        $sql = "SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime', DATE_FORMAT(EventDate - INTERVAL EventTimeZoneOffset SECOND, '%Y') AS Year FROM timeline WHERE hide = 0 GROUP BY Year ORDER BY EventDate ASC";
        $sqlResult = mysqli_query($link, $sql);

        while($row = mysqli_fetch_array($sqlResult)){
          $year = $row['Year'];
          // $eventTimeZone = $row['EventTimeZone'];
          // $eventTimeZoneOffset = $row['EventTimeZoneOffset'];
          //
          // $eventDate = $row['EventDate'];
          // $eventTime = $row['EventTime'];
          //
          // $localDate = $row['LocalDate'];
          // $localTime = $row['LocalTime'];
          //
          // $endEventDate = $row['EndEventDate'];
          //
          // $endEventDateFormatted = date("F d, Y", strtotime($endEventDate));
          //
          // $eventDateFormatted = date("F d, Y", strtotime($localDate));
          // $eventTimeFormatted = date("h:i A", strtotime($localTime));
          //
          // $eventTitle = $row['EventTitle'];
          // $eventDescription = $row['EventDescription'];
          // $memoryType = $row['MemoryType'];
          //
          // $eventYouTubeLink = $row['EventYouTubeLink'];
          //
          // $eventMedia = $row['EventMedia'];
          // $eventMediaPortrait = $row['EventMediaPortrait'];
          // $eventMediaDescription = $row['EventMediaDescription'];
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
    <script src="../js/script.js"></script>
    <script src="js/ajax.js"></script>
  </body>
</html>
