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

  $title = "Ephraim Becker - Timeline - More info";
  $header = "Timeline - " . $eventTitle;
  $localStyleSheet = '<link rel="stylesheet" href="../css/style.css" />';
  $localScript = '<script src="js/script.js"></script>';

  $body = '<div class="';

  if($hide == 1) {
    $body .= 'hidden-memory "';
  }

  if($memoryType == 0) {
    $body .= 'remembered-memory"';
  } else if($memoryType == 1) {
    $body .= 'diary-memory"';
  }
  $body .= '>
        <h2><time datetime="' . $localDate . '">';

        if(!is_null($localTime)) {
          $body .= $eventDateFormatted . " " . $eventTimeFormatted . " " . $eventTimeZone;
         } else {
           $body .= $eventDateFormatted;
          }
          $body .= '</time>';

          if(!is_null($endEventDate)) {
            $body .= ' - <time datetime="' . $endEventDate . '">' . $endEventDateFormatted . '</time>';
           }
           $body .= '</h2>
        <h3>' . $eventTitle . '</h3>
        <p>' . $eventDescription . '</p>';

        if(!is_null($eventYouTubeLink)) {
          $body .= '<iframe width="560" height="315" src="' . $eventYouTubeLink . '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        }

        if(!is_null($eventMedia)) {
          if($eventMediaPortrait == 0) {
            $body .= '<img src="../../timeline/img/' . $eventMedia . '" alt="' . $eventMediaDescription . '" width="200px" height="113px" />';
        } else {
          $body .= '<img src="../../timeline/img/' . $eventMedia . '" alt="' . $eventMediaDescription . '" width="113px" height="200px" />';
        }
      }
      $body .= '</div>
      <br />';

        if(isset($_GET['offset'])) {
          $offset = $_GET['offset'];
        } else {
          $offset = NULL;
        }

        $sqlTwo = $link->prepare("SELECT * FROM thoughts WHERE TimelineId=? AND hide=0");
        $sqlTwo->bind_param("i", $id);

        $sqlTwo->execute();

        $sqlTwoResult = $sqlTwo->get_result();

       while($rowTwo = mysqli_fetch_array($sqlTwoResult)) {
         $thoughtId = $rowTwo['ThoughtId'];
         $date = $rowTwo['DateCreated'];
         $dateModified = $rowTwo['DateModified'];
         $thought = $rowTwo['Thought'];

     $body .= '<div class="thought">
       <h2 class="date"><time datetime="' . date('Y-m-d H:i:s', strtotime($date) - intval($offset)) . '">' . date('m/d/Y h:i A', strtotime($date) - intval($offset)) . '</time></h2>
       <p>' . $thought . '</p>
     </div>';
    }

    require("../../base.php");
?>
