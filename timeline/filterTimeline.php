<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;


  function displayAllEvents($sqlResult, $link) {
    $year = $_GET['year'];
    $month = $_GET['month'];
    $day = $_GET['day'];

    ?>

    <header>
      <h1 style="font-weight: bold;">Timeline - <?php echo $year ?> album</h1>
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
      <br />
    <?php
    while($row = mysqli_fetch_array($sqlResult)) {
      $id = $row['TimelineId'];

      $eventTimeZone = $row['EventTimeZone'];
      $eventTimeZoneOffset = $row['EventTimeZoneOffset'];

      $eventDate = $row['EventDate'];
      $eventTime = $row['EventTime'];

      $localDate = $row['LocalDate'];
      $localTime = $row['LocalTime'];

      $endEventDate = $row['EndEventDate'];

      $endEventDateFormatted = date("F d, Y", strtotime($endEventDate));

      $eventDateFormatted = date("F d, Y", strtotime($localDate));
      $eventTimeFormatted = date("h:i A", strtotime($localTime));

      $eventTitle = $row['EventTitle'];
      $eventDescription = $row['EventDescription'];
      $memoryType = $row['MemoryType'];

      $eventYouTubeLink = $row['EventYouTubeLink'];

      $eventMedia = $row['EventMedia'];
      $eventMediaPortrait = $row['EventMediaPortrait'];
      $eventMediaDescription = $row['EventMediaDescription'];

      $sqlThoughts = $link->prepare("SELECT COUNT(*) AS NumberOfThoughts FROM thoughts WHERE TimelineId=?");
      $sqlThoughts->bind_param("i", $id);

      $sqlThoughts->execute();

      $sqlThoughtsResult = $sqlThoughts->get_result();

      while($rowTwo = mysqli_fetch_array($sqlThoughtsResult)) {
        $numberOfThoughts = $rowTwo['NumberOfThoughts'];
      }
      ?>
      <div style="margin-bottom: 10px;" class="event <?php if($memoryType == 0) { echo 'remembered-memory'; } else if($memoryType == 1) { echo 'diary-memory'; } ?> ">
        <a class="more-info-link" href="moreInfo/index.php?id=<?php echo $id ?>">
          <div class="row">
            <h2><time datetime="<?php echo $localDate ?>"><?php if(!is_null($localTime)) { echo $eventDateFormatted . " " . $eventTimeFormatted . " " . $eventTimeZone; } else { echo $eventDateFormatted; } ?></time><?php if(!is_null($endEventDate)) { echo " - <time datetime='" . $endEventDate . "'>" . $endEventDateFormatted . "</time>"; } ?></h2>
            <?php
            if($numberOfThoughts > 0) {
            ?>
            <div class="number-of-thoughts">
              <span><?php echo $numberOfThoughts ?></span>
            </div>
            <?php
            }
             ?>
          </div>
          <h3><?php echo $eventTitle ?></h3>
          <p><?php echo $eventDescription ?></p>

          <?php
          if(!is_null($eventYouTubeLink)) {
          ?>
          <iframe width="560" height="315" src="<?php echo $eventYouTubeLink ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          <?php
          }

          if(!is_null($eventMedia)) {
            if($eventMediaPortrait == 0) {
          ?>
            <img src="<?php echo '../../timeline/img/' . $eventMedia ?>" alt="<?php echo $eventMediaDescription ?>" width="200px" height="113px" />
          <?php } else { ?>
            <img src="<?php echo '../../timeline/img/' . $eventMedia ?>" alt="<?php echo $eventMediaDescription ?>" width="113px" height="200px" />
          <?php
          }
        }
      ?>
    </a>
  </div>
  <?php
    }

    $sqlDates = $link->prepare("SELECT EventDate, YEAR(EventDate) AS Year, MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE EventDate>? GROUP BY EventDate LIMIT 1");
    $sqlDates->bind_param("s", $navvedEventDate);

    $navvedEventDate = $year . "-" . $month . "-" . $day;

    $sqlDates->execute();

    $sqlDatesResult = $sqlDates->get_result();

    if($sqlDatesResult->num_rows > 0) {
      while($row = mysqli_fetch_array($sqlDatesResult)) {
        $nextYear = $row['Year'];
        $nextMonth = $row['Month'];
        $nextDay = $row['Day'];
      }

      if($month > 0 && $day > 0) {
        $year = $nextYear;
        $month = $nextMonth;
        $day = $nextDay;
      } else if($month == 0 && $day == 0) {
        $sqlDatesTwo = $link->prepare("SELECT EventDate, YEAR(EventDate) AS Year, MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE EventDate>? GROUP BY Year LIMIT 1");
        $sqlDatesTwo->bind_param("s", $navvedEventDate);

        $navvedEventDate = $nextYear . "-12-31";

        $sqlDatesTwo->execute();

        $sqlDatesTwoResult = $sqlDatesTwo->get_result();

        while($rowTwo = mysqli_fetch_array($sqlDatesTwoResult)) {
          $year = $rowTwo['Year'];
        }
      }

      ?>
      <div class="row navButton" onclick="filterTimeline('<?php echo $year ?>', '<?php echo $month ?>', '<?php echo $day ?>')">
         >
      </div>
      <?php
    }
  }

  function displaySorter($sqlResult) {
    $year = $_GET['year'];
    $month = $_GET['month'];
    ?>
    <header>
      <h1 style="font-weight: bold;">Timeline - <?php echo $year ?> album</h1>
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
      while($row = mysqli_fetch_array($sqlResult)) {
        $month = $row['Month'];

        $dateObj = DateTime::createFromFormat('!m', $month);
        $monthName = $dateObj->format('F');

        if(isset($row['Day'])) {
          $day = $row['Day'];
        } else {
          $day = NULL;
        }
        ?>
        <div class="card album-cover" id="album-cover-<?php if(!is_null($day)) { echo $year . '-' . $month . '-' . $day; } else { echo $year . '-' . $month; } ?>" onclick="filterTimeline('<?php echo $year ?>', '<?php echo $month ?>', '<?php echo $day ?>')">
          <h2><?php echo $monthName . " " . $day ?></h2>
          <p>All the events on <?php echo $monthName . " " . $day ?></p>
        </div>
      <?php } ?>
      </div>
      <?php
      }
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
          <li class="focus"><a href="../timeline/">Timeline</a></li>
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

<?php
if($_GET['day'] == 0) {
  if($_GET['month'] == 0) {
    $sql = $link->prepare("SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime', MONTH(EventDate) AS Month FROM timeline WHERE hide = 0 AND YEAR(EventDate) = ? ORDER BY EventDate ASC");
    $sql->bind_param("i", $year);

    $year = $_GET['year'];

    $sql->execute();

    $sqlResult = $sql->get_result();

    if($sqlResult->num_rows > 12) {
      $sqlTwo = $link->prepare("SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime', MONTH(EventDate) AS Month FROM timeline WHERE hide = 0 AND YEAR(EventDate) = ? GROUP BY Month ORDER BY EventDate ASC");
      $sqlTwo->bind_param("i", $year);
      $yearTwo = $_GET['year'];

      $sqlTwo->execute();

      $sqlTwoResult = $sqlTwo->get_result();

      displaySorter($sqlTwoResult);
    } else {
        displayAllEvents($sqlResult, $link);
    }

    $sql->close();
  } else {
    $year = $_GET['year'];
    $month = $_GET['month'];

    $dateObj = DateTime::createFromFormat('!m', $month);
    $monthName = $dateObj->format('F');

    $sqlThree = $link->prepare("SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime', DAY(EventDate) AS Day FROM timeline WHERE hide = 0 AND YEAR(EventDate) = ? AND MONTH(EventDate) = ? ORDER BY EventDate ASC");
    $sqlThree->bind_param("ii", $year, $month);

    $sqlThree->execute();

    $sqlThreeResult = $sqlThree->get_result();

    if($sqlThreeResult->num_rows < 12) {
      displayAllEvents($sqlThreeResult, $link);

      $sqlThree->close();
    } else {
        $sqlFour = $link->prepare("SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime', MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE hide = 0 AND YEAR(EventDate) = ? AND MONTH(EventDate) = ? GROUP BY Day ORDER BY EventDate ASC");
        $sqlFour->bind_param("ii", $year, $month);

        $sqlFour->execute();

        $sqlFourResult = $sqlFour->get_result();

        displaySorter($sqlFourResult);
      }
    }
  } else {
  $sqlFive = $link->prepare("SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime', DAY(EventDate) AS Day FROM timeline WHERE hide = 0 AND YEAR(EventDate) = ? AND MONTH(EventDate) = ? AND DAY(EventDate) = ? ORDER BY EventDate ASC");
  $sqlFive->bind_param("iii", $year, $month, $day);

  $year = $_GET['year'];
  $month = $_GET['month'];

  $dateObj = DateTime::createFromFormat('!m', $month);
  $monthName = $dateObj->format('F');

  $day = $_GET['day'];

  $sqlFive->execute();

  $sqlFiveResult = $sqlFive->get_result();

  displayAllEvents($sqlFiveResult, $link);

  $sqlFive->close();
}

$link->close();
 ?>

</main>
<footer>
  <p>&copy; 2021 Ephraim Becker</p>
</footer>
<script src="../js/script.js"></script>
<script src="js/ajax.js"></script>
</body>
</html>
