<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;

  $title = "";
  $header = "";
  $localStyleSheet = '<link rel="stylesheet" href="css/style.css" />';
  $body = "";
  $localScript = '<script src="js/ajax.js"></script>';


  function navButtons($link, $body) {
    $year = $_GET['year'];
    $month = $_GET['month'];
    $day = $_GET['day'];

    $body .= '<div class="row">';

    $sqlDatesDesc = $link->prepare("SELECT EventDate, YEAR(EventDate) AS Year, MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE EventDate<? AND hide=0 GROUP BY EventDate ORDER BY EventDate DESC LIMIT 1");
    $sqlDatesDesc->bind_param("s", $navvedEventDateDesc);

    $navvedEventDateDesc = $year . "-" . $month . "-" . $day;

    $sqlDatesDesc->execute();

    $sqlDatesDescResult = $sqlDatesDesc->get_result();

    if($sqlDatesDescResult->num_rows > 0) {
      while($row = mysqli_fetch_array($sqlDatesDescResult)) {
        $prevYear = $row['Year'];
        $prevMonth = $row['Month'];
        $prevDay = $row['Day'];
      }
      if($month > 0 && $day > 0) {
        $year = $prevYear;
        $month = $prevMonth;
        $day = $prevDay;
      } else if($month == 0 && $day == 0) {
        $sqlDatesTwoDesc = $link->prepare("SELECT EventDate, YEAR(EventDate) AS Year, MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE EventDate<? AND hide=0 GROUP BY Year ORDER BY EventDate DESC LIMIT 1");
        $sqlDatesTwoDesc->bind_param("s", $navvedEventDateDesc);

        $navvedEventDateDesc = $_GET['year'] . "-0-0";

        $sqlDatesTwoDesc->execute();

        $sqlDatesTwoDescResult = $sqlDatesTwoDesc->get_result();

        while($rowTwoDesc = mysqli_fetch_array($sqlDatesTwoDescResult)) {
          $year = $rowTwoDesc['Year'];
        }
      }

      $body .= '<br />
      <div class="navButton" onclick="filterTimeline(' . $year . ', ' . $month . ', ' . $day . ')">
         <p><</p>
      </div>';
    }


    $year = $_GET['year'];
    $month = $_GET['month'];
    $day = $_GET['day'];

    $sqlDates = $link->prepare("SELECT EventDate, YEAR(EventDate) AS Year, MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE EventDate>? AND hide=0 GROUP BY EventDate LIMIT 1");
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
        $sqlDatesTwo = $link->prepare("SELECT EventDate, YEAR(EventDate) AS Year, MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE EventDate>? AND hide=0 GROUP BY Year LIMIT 1");
        $sqlDatesTwo->bind_param("s", $navvedEventDate);

        $navvedEventDate = $nextYear . "-12-31";

        $sqlDatesTwo->execute();

        $sqlDatesTwoResult = $sqlDatesTwo->get_result();

        while($rowTwo = mysqli_fetch_array($sqlDatesTwoResult)) {
          $year = $rowTwo['Year'];
        }
      }

      $body .= '<div class="navButton" onclick="filterTimeline(\'' . $year . '\', \'' . $month . '\', \'' . $day . '\')">
         <p>></p>
      </div>';
    }

    $body .= '</div>';

    return $body;
  }

  function displayAllEvents($sqlResult, $link, $title, $header, $body) {
    $year = $_GET['year'];
    $month = $_GET['month'];
    $day = $_GET['day'];

    $title .= "Ephraim Becker - Timeline - " . $year . " album";
    $header .= "Ephraim Becker - Timeline - " . $year . " album";

    $body .= '<table>
        <tr>
          <td rowspan="3">Legend</td>
          <td class="remembered-memory">Remembered memory</td>
        </tr>
        <tr>
          <td class="diary-memory">Diary memory</td>
        </tr>
      </table>
      <br />';

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

      $body .= '<div style="margin-bottom: 10px;" class="event ';
       if($memoryType == 0) {
         $body .= 'remembered-memory';
        } else if($memoryType == 1) {
          $body .= 'diary-memory'; }
          $body .= '">
        <a class="more-info-link" href="moreInfo/index.php?id=' . $id . '">
          <div class="row">
            <h2><time datetime="' . $localDate . '">';
            if(!is_null($localTime)) {
              $body .= $eventDateFormatted . " " . $eventTimeFormatted . " " . $eventTimeZone;
             } else {
               $body .= $eventDateFormatted;
             }
             $body .= '</time>';
              if(!is_null($endEventDate)) {
                $body .= " - <time datetime='" . $endEventDate . "'>"
               . $endEventDateFormatted . "</time>";
             }
             $body .= '</h2>';

            if($numberOfThoughts > 0) {
            $body .= '<div class="number-of-thoughts">
              <span>' . $numberOfThoughts . '</span>
            </div>';
            }
          $body .= '</div>
          <h3>' . $eventTitle . '</h3>
          <p>' . $eventDescription . '</p>';

          if(!is_null($eventYouTubeLink)) {
          $body .= '<iframe width="560" height="315" src="' . $eventYouTubeLink . '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';

          }

          if(!is_null($eventMedia)) {
            if($eventMediaPortrait == 0) {
            $body .= '<img src="../../timeline/img/' . $eventMedia . " alt=" . $eventMediaDescription . '" width="200px" height="113px" />';
          } else {
            $body .= '<img src="../../timeline/img/' . $eventMedia . '" alt="' . $eventMediaDescription . '" width="113px" height="200px" />';
          }
        }
    $body .= '</a>
  </div>
  <br />';
    }

    $body = navButtons($link, $body);

    return array($title, $header, $body);
  }

  function displaySorter($sqlResult, $link, $title, $header, $body) {
    $year = $_GET['year'];
    $month = $_GET['month'];

    $title .= "Ephraim Becker - Timeline - " . $year . " album";
    $header .= "Ephraim Becker - Timeline - " . $year . " album";

      $body .= '<table>
        <tr>
          <td rowspan="3">Legend</td>
          <td class="remembered-memory">Remembered memory</td>
        </tr>
        <tr>
          <td class="diary-memory">Diary memory</td>
        </tr>
      </table>
      <div id="grid-container">';

      while($row = mysqli_fetch_array($sqlResult)) {
        $month = $row['Month'];

        $dateObj = DateTime::createFromFormat('!m', $month);
        $monthName = $dateObj->format('F');

        if(isset($row['Day'])) {
          $day = $row['Day'];
        } else {
          $day = NULL;
        }

        $body .= '<div class="card album-cover" id="album-cover-';
        if(!is_null($day)) {
          $body .= $year . '-' . $month . '-' . $day;
         } else {
           $body .= $year . '-' . $month;
          }
          $body .= '" onclick="filterTimeline(\'' . $year . '\', \'' . $month .
         '\', \'' . $day . '\')">
          <h2>' . $monthName . " " . $day . '</h2>
          <p>All the events on ' . $monthName . " " . $day . '</p>
        </div>';
      }
      $body .= '</div>
      <br />';

      $body = navButtons($link, $body);

      return array($title, $header, $body);
    }

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

      list($title, $header, $body) = displaySorter($sqlTwoResult, $link, $title, $header, $body);
    } else {
        list($title, $header, $body) = displayAllEvents($sqlResult, $link, $title, $header, $body);
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
      list($title, $header, $body) = displayAllEvents($sqlThreeResult, $link, $title, $header, $body);

      $sqlThree->close();
    } else {
        $sqlFour = $link->prepare("SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime', MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE hide = 0 AND YEAR(EventDate) = ? AND MONTH(EventDate) = ? GROUP BY Day ORDER BY EventDate ASC");
        $sqlFour->bind_param("ii", $year, $month);

        $sqlFour->execute();

        $sqlFourResult = $sqlFour->get_result();

        list($title, $header, $body) = displaySorter($sqlFourResult, $link, $title, $header, $body);
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

  list($title, $header, $body) = displayAllEvents($sqlFiveResult, $link, $title, $header, $body);

  $sqlFive->close();
}

$link->close();

require("../base.php");
 ?>
