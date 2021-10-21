<?php
  session_start();

  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;

  if(isset($_SESSION['username'])) {
    $sql = $link->prepare("SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime' FROM timeline WHERE TimelineId=?");
  } else {
    $sql = $link->prepare("SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime' FROM timeline WHERE hide=0 AND TimelineId=?");
  }
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

  if($memoryType == 0) {
    $body .= 'remembered-memory';
  } else if($memoryType == 1) {
    $body .= 'diary-memory';
  }

  if(isset($_SESSION['username'])) {
    if($hide == 1) {
      $body .= ' hidden-memory';
    }
  }

  $body .= '">
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

      if(isset($_SESSION['username'])) {
        $body .= '<ul class="row actionButtons">
          <li><a class="edit" href="editEvent/index.php?id=' . $id . '">Edit</a></li>';

          if($hide == 0) {
            $body .= '<li><a class="hide" href="hideEvent.php?id=' . $id . '">Hide</a></li>';
          }

          else if($hide == 1) {
            $body .= '<li><a class="hide" href="unhideEvent.php?id=' . $id . '">Unhide</a></li>';
          }

          $body .= '<li><a class="delete" href="confirmation.php?id=' . $id . '">Delete</a></li>
        </ul>';
      }

      $body .= '</div>
      <br />';

        if(isset($_GET['offset'])) {
          $offset = $_GET['offset'];
        } else {
          $offset = NULL;
        }

        if(isset($_SESSION['username'])) {
          $sqlTwo = $link->prepare("SELECT * FROM thoughts WHERE TimelineId=?");
        } else {
          $sqlTwo = $link->prepare("SELECT * FROM thoughts WHERE TimelineId=? AND hide=0");
        }
        $sqlTwo->bind_param("i", $id);

        $sqlTwo->execute();

        $sqlTwoResult = $sqlTwo->get_result();

       while($rowTwo = mysqli_fetch_array($sqlTwoResult)) {
         $thoughtId = $rowTwo['ThoughtId'];
         $date = $rowTwo['DateCreated'];
         $dateModified = $rowTwo['DateModified'];
         $thought = $rowTwo['Thought'];

         if(isset($_SESSION['username'])) {
           $body .= '<div class="thought">
             <h2 class="date"><time datetime="' . date('Y-m-d H:i:s', strtotime($date) - intval($offset)) . '">' . date('m/d/Y h:i A', strtotime($date) - intval($offset)) . '</time></h2>
             <p>' . $thought . '</p>
             <ul class="row actionButtons">
               <li><a class="edit" href="editThought/index.php?id=' . $thoughtId . '">Edit</a></li>';

               if($hide == 0) {
                 $body .= '<li><a class="hide" href="hideThought.php?id=' . $thoughtId . '">Hide</a></li>';
               }

               else if($hide == 1) {
                 $body .= '<li><a class="hide" href="unhideThought.php?id=' . $thoughtId . '">Unhide</a></li>';
               }

               $body .= '<li><a class="delete" href="confirmationThought.php?id=' . $thoughtId . '">Delete</a></li>
             </ul>';
        }
     $body .= '</div>';
    }

    if(isset($_SESSION['username'])) {
      $body .= '<br />
      <br />
      <form action="addThought.php?id=' . $id . '" method="post">
        <textarea name="thought" rows="6" cols="45" required></textarea>
        <input type="hidden" name="id" value="' . $id . '" />

        <input class="thoughtButton" type="submit" value="Add thought" />
      </form>';
    }

    $url = $_SERVER['REQUEST_URI'];
    require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
?>
