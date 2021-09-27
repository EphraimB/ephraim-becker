<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;

  $sql = $link->prepare("SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime' FROM timeline WHERE hide=0 AND TimelineId=?");
  $sql->bind_param("i", $id);

  $id = $_GET['id'];

  $sql->execute();

  $sqlResult = $sql->get_result();

  while($row = mysqli_fetch_array($sqlResult)) {
    $hide = $row['hide'];
    $timelineId = $row['TimelineId'];

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
  }

  $sqlTwo = $link->prepare("SELECT * FROM thoughts WHERE TimelineId=?");
  $sqlTwo->bind_param("i", $id);

  $sqlTwo->execute();

  $sqlTwoResult = $sqlTwo->get_result();

  while($rowTwo = mysqli_fetch_array($sqlTwoResult)) {
    $date = $rowTwo['DateCreated'];
    $dateModified = $rowTwo['DateModified'];
    $thought = $rowTwo['Thought'];
  }
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Ephraim Becker - Timeline - More info</title>
    <link rel="stylesheet" href="../../css/style.css" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="canonical" href="https://www.ephraimbecker.com/timeline/moreInfo/" />
    <link rel="icon" href="../../img/ephraim_becker.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="../img/ephraim-becker.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Hi! My name is Ephraim Becker and here's a timeline about my life and how people can learn from it." />
    <meta name="keywords" content="Ephraim Becker, autism, aspergers, ADHD" />
    <script src="js/script.js"></script>
  </head>
  <body>
    <nav>
      <ul>
        <li id="first"><img src="../../img/ephraim-becker.jpg" alt="Photo of Ephraim Becker" width="70px" height="70px" /></li>
        <li id="hamburger-icon"><a href="#" onclick="toggleNavMenu()">&#9776;</a></li>
        <div id="links">
          <li><a href="../../">Home</a></li>
          <li class="focus"><a href="../">Timeline</a></li>
          <div id="dropdown">
            <li><a href="#" onclick="toggleNavSubmenu()">Daily Life &emsp; &#x25BC;</a></li>
            <div id="dropdown-content">
              <li><a href="../../everydayLife/">Everyday Life</a></li>
              <li><a href="../../college/">College Life</a></li>
            </div>
          </div>
          <li><a href="../../projects/">Projects</a></li>
          <li><a href="../../resources/">Resources</a></li>
          <li><a href="../../about/">About</a></li>
        </div>
      </ul>
    </nav>
    <header>
      <h1 style="font-weight: bold;">Timeline - <?php echo $eventTitle ?></h1>
    </header>
    <main>
      <div class="<?php if($hide == 1) { echo 'hidden-memory '; } if($memoryType == 0) { echo 'remembered-memory'; } else if($memoryType == 1) { echo 'diary-memory'; } ?> ">
        <h2><time itemprop="startDate" datetime="<?php echo $localDate ?>"><?php if(!is_null($localTime)) { echo $eventDateFormatted . " " . $eventTimeFormatted . " " . $eventTimeZone; } else { echo $eventDateFormatted; } ?></time><?php if(!is_null($endEventDate)) { echo " - <time itemprop='endDate' datetime='" . $endEventDate . "'>" . $endEventDateFormatted . "</time>"; } ?></h2>
        <h3 itemprop="name"><?php echo $eventTitle ?></h3>
        <p itemprop="description"><?php echo $eventDescription ?></p>

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
      </div>
      <br />
      <?php
        if(isset($_GET['offset'])) {
          $offset = $_GET['offset'];
        } else {
          $offset = NULL;
        }
       ?>
      <div class="thought">
        <h2 class="date"><time datetime="<?php echo date('Y-m-d H:i:s', strtotime($date) - intval($offset)); ?>"><?php echo date('m/d/Y h:i A', strtotime($date) - intval($offset)); ?></time></h2>
        <p><?php echo $thought ?></p>
      </div>
    </main>
    <footer>
      <p>&copy; 2021 Ephraim Becker</p>
    </footer>
    <script src="../../js/script.js"></script>
  </body>
</html>
