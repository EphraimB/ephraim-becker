<?php
  session_start();

  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;

  if(!isset($_SESSION['username'])) {
    header("location: ../");
  }

  $title = "Ephraim Becker - Admin - Timeline - Edit Event";
  $header = 'Admin - Timeline - Edit Event';
  $localStyleSheet = '<link rel="stylesheet" href="css/style.css" />';
  $localScript = '<script src="js/script.js"></script>';

  $body = '<form action="editEvent.php" method="post" enctype="multipart/form-data">
        <div class="row">';

          $sql = $link->prepare("SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime' FROM timeline WHERE TimelineId=?");
          $sql->bind_param("i", $id);

          $id = $_GET['id'];

          $sql->execute();

          $sqlResult = $sql->get_result();

          while($row = mysqli_fetch_array($sqlResult)){
            $hide = $row['hide'];
            $timelineId = $row['TimelineId'];
            $eventDate = $row['EventDate'];
            $eventTime = $row['EventTime'];

            $endEventDate = $row['EndEventDate'];

            $eventTimeZone = $row['EventTimeZone'];
            $eventTimeZoneOffset = $row['EventTimeZoneOffset'];

            $localDate = $row['LocalDate'];
            $localTime = $row['LocalTime'];

            $eventTitle = $row['EventTitle'];
            $eventDescription = $row['EventDescription'];
            $memoryType = $row['MemoryType'];

            $eventYouTubeLink = $row['EventYouTubeLink'];

            $eventMedia = $row['EventMedia'];
            $eventMediaDescription = $row['EventMediaDescription'];
          }

          $body .= '<div>
            <label for="eventDate">Start event date:</label>
            <br />
            <input type="date" id="eventDate" name="eventDate" value="' . $localDate . '" required />
          </div>
          <div>
            <label for="eventTime">Event time (optional):</label>
            <br />
            <input type="time" id="eventTime" name="eventTime" value="' . $localTime . '" />
            <input type="hidden" id="timezone" name="timezone" value="' . $eventTimeZone . '" />
            <input type="hidden" id="timezoneOffset" name="timezoneOffset" value="' . $eventTimeZoneOffset . '" />
          </div>
          <div>
            <input type="checkbox" id="allDay" name="allDay" value="allDay" ';
            if(is_null($eventTime)) {
              $body .= "checked";
            }

            $body .= '>
            <label for="allDay">allDay?</label>
          </div>
        </div>
        <br />
        <div class="row">
          <div>
            <label for="endEventDate">End event date:</label>
            <br />
            <input type="date" id="endEventDate" name="endEventDate" value="' . $endEventDate . '" />
          </div>
          <div>
            <input type="checkbox" id="endEventDateExist" name="endEventDateExist" value="endEventDateExist" ';

            if(!is_null($endEventDate)) {
              $body .= "checked";
             }

             $body .= '>
            <label for="endEventDateExist">End event date exist?</label>
          </div>
        </div>
        <br />
        <div>
          <label for="eventTitle">Event title:</label />
          <br />
          <input type="text" id="eventTitle" name="eventTitle" value="' . $eventTitle . '" required />
        </div>
        <br />
        <div>
          <label for="eventDescription">Event description:</label>
          <br />
          <textarea id="eventDescription" name="eventDescription" rows="6" cols="50" required>' . $eventDescription . '</textarea>
        </div>
        <br />
        <div>
          <label for="eventImage">Image:</label>
          <br />
          <input id="eventImage" name="eventImage" type="file" />
        </div>
        <br />
        <div>
          <label for="eventImageDescription">Image description:</label>
          <br />
          <input id="eventImageDescription" name="eventImageDescription" type="text" />
        </div>
        <br />
        <div>
          <label for="eventImage">YouTube link:</label>
          <br />
          <input id="eventYouTubeLink" name="eventYouTubeLink" type="text" value="' . $eventYouTubeLink . '" />
        </div>
        <div>
          <h3>Event memory type:</h3>
          <div class="row">
            <div class="remembered-memory">
              <input type="radio" id="remembered" name="memory" value="0" ';

              if($memoryType == 0) {
                $body .= "checked";
               }
               $body .= ' required />
              <label for="remembered">Remembered memory</label>
            </div>
            <div class="diary-memory">
              <input type="radio" id="diary" name="memory" value="1" ';

              if($memoryType == 1) {
                $body .= "checked";
               }

               $body .= ' />
              <label for="diary">Diary memory</label>
            </div>
            <div class="hidden-memory remembered-memory">
              <input type="checkbox" id="hidden" name="hidden" value="1" ';

              if($hide == 1) {
                $body .= "checked";
              }
              $body .= ' />
              <label for="hidden">Hidden memory</label>
            </div>
          </div>
        </div>
        <input type="hidden" name="id" value="' . $timelineId . '" />
        <br />
        <input type="submit" id="submit" value="Edit event" />
        <br />
      </form>';

  $sql->close();
  $link->close();

  $url = $_SERVER['REQUEST_URI'];
  require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
 ?>
