<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Ephraim Becker - Admin - Timeline</title>
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
        <li id="first"><img src="../../img/ephraim-becker.jpg" alt="Photo of Ephraim Becker" width="70px" height="70px" /></li>
        <li id="hamburger-icon"><a href="#" onclick="toggleNavMenu()">&#9776;</a></li>
        <div id="links">
          <li><a href="../">Admin</a></li>
          <li class="focus"><a href="../timeline/">Timeline</a></li>
        </div>
      </ul>
    </nav>
    <header>
      <h1 style="font-weight: bold;">Admin - Timeline</h1>
    </header>
    <main>
      <?php
      $sql = $link->prepare("SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime' FROM timeline WHERE TimelineId=?");
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
      ?>

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
        <ul class="row actionButtons">
          <li><a class="edit" href="editEvent/index.php?id=<?php echo $timelineId ?>">Edit</a></li>
          <?php if($hide == 0) {
            echo "<li><a class='hide' href='hideEvent.php?id=$timelineId'>Hide</a></li>";
          }
          else if($hide == 1) {
            echo "<li><a class='hide' href='unhideEvent.php?id=$timelineId'>Unhide</a></li>";
          }
          ?>
          <li><a class="delete" href="confirmation.php?id=<?php echo $timelineId ?>">Delete</a></li>
        </ul>
      </div>
    </main>
    <script src="../js/script.js"></script>
    <script src="js/ajax.js"></script>
  </body>
</html>
