<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class Timeline extends Base
{
  private $link;
  private $isAdmin;
  private $year;
  private $month;
  private $day;

  function __construct()
  {
    $this->setIsAdmin();
    $this->setDate();
  }

  function setLink($link)
  {
    $this->link = $link;
  }

  function getLink()
  {
    return $this->link;
  }

  function setIsAdmin(): void
  {
    if(isset($_SESSION['username'])) {
      $this->isAdmin = true;
    } else {
      $this->isAdmin = false;
    }
  }

  function getIsAdmin(): bool
  {
    return $this->isAdmin;
  }

  function getYear(): int
  {
    return $this->year;
  }

  function setYear(): void
  {
    $this->year = $_GET['year'] || 0;
  }

  function getMonth(): int
  {
    return $this->year;
  }

  function setMonth(): void
  {
    $this->month = $_GET['month'] || 0;
  }

  function getDay(): int
  {
    return $this->day;
  }

  function setDay(): void
  {
    $this->year = $_GET['day'] || 0;
  }

  function addEvent(): string
  {
    if($this->getIsAdmin() == true) {
      $html = '<div class="row">
            <ul class="subNav">
              <li><a style="text-decoration: none;" href="addEvent/">+</a></li>
            </ul>
          </div>';
      } else {
        $html = '';
      }

      return $html;
  }

  function fetchEvents($link): mysqli_result
  {
    if($this->getYear() == 0 && $this->getMonth() == 0 && $this->getDay() == 0) {
      if($this->getIsAdmin()) {
        $sql = "SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime', DATE_FORMAT(EventDate - INTERVAL EventTimeZoneOffset SECOND, '%Y') AS Year FROM timeline GROUP BY Year ORDER BY EventDate ASC";
      } else {
        $sql = "SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime', DATE_FORMAT(EventDate - INTERVAL EventTimeZoneOffset SECOND, '%Y') AS Year FROM timeline WHERE hide = 0 GROUP BY Year ORDER BY EventDate ASC";
      }
      $sqlResult = mysqli_query($this->getLink(), $sql);
    } else {
      if($this->getIsAdmin()) {
        $sql = $link->prepare("SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime', MONTH(EventDate) AS Month FROM timeline WHERE YEAR(EventDate) = ? ORDER BY EventDate ASC");
      } else {
        $sql = $link->prepare("SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime', MONTH(EventDate) AS Month FROM timeline WHERE hide = 0 AND YEAR(EventDate) = ? ORDER BY EventDate ASC");
      }
      $sql->bind_param("i", $year);

      $year = $_GET['year'];

      $sql->execute();

      $sqlResult = $sql->get_result();
    }

    return $sqlResult;
  }

  function yearView($sqlResult, $year): string
  {
    $html = '<div class="card album-cover" id="album-cover-' . $year . '" onclick="filterTimeline(\'' . $year . '\')">';
    $html .= '<h3>' . $year . '</h3>';
    $html .= '<p>All the events in ' . $year . ' when I was ' . ($year-1996) . ' years old</p>';
    $html .= '</div>';

    return $html;
  }

  function navButtons($link, $body): string
  {
    $year = $_GET['year'];
    $month = $_GET['month'];
    $day = $_GET['day'];

    $body .= '<div class="row">';

    if($this->getIsAdmin()) {
      $sqlDatesDesc = $link->prepare("SELECT EventDate, YEAR(EventDate) AS Year, MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE EventDate<? GROUP BY EventDate ORDER BY EventDate DESC LIMIT 1");
    } else {
      $sqlDatesDesc = $link->prepare("SELECT EventDate, YEAR(EventDate) AS Year, MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE EventDate<? AND hide = 0 GROUP BY EventDate ORDER BY EventDate DESC LIMIT 1");
    }
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
        if($this->getIsAdmin()) {
          $sqlDatesTwoDesc = $link->prepare("SELECT EventDate, YEAR(EventDate) AS Year, MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE EventDate<? GROUP BY Year ORDER BY EventDate DESC LIMIT 1");
        } else {
          $sqlDatesTwoDesc = $link->prepare("SELECT EventDate, YEAR(EventDate) AS Year, MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE EventDate<? AND hide = 0 GROUP BY Year ORDER BY EventDate DESC LIMIT 1");
        }
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

    if($this->getIsAdmin()) {
      $sqlDates = $link->prepare("SELECT EventDate, YEAR(EventDate) AS Year, MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE EventDate>? GROUP BY EventDate LIMIT 1");
    } else {
      $sqlDates = $link->prepare("SELECT EventDate, YEAR(EventDate) AS Year, MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE EventDate>? AND hide = 0 GROUP BY EventDate LIMIT 1");
    }
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

  function displayAllEvents($sqlResult, $link, $header, $body) {
    $year = $_GET['year'];
    $month = $_GET['month'];
    $day = $_GET['day'];

    $this->setHeader('Ephraim Becker - Timeline');

    while($row = mysqli_fetch_array($sqlResult)) {
      $id = $row['TimelineId'];

      if($this->getIsAdmin()) {
        $hide = $row['hide'];
      }

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
        $body .= 'diary-memory';
      }

      if($this->getIsAdmin()) {
        if($hide == 1) {
          $body .= ' hidden-memory';
        }
      }

      $body .= '">
        <a class="more-info-link" href="moreInfo/index.php?id=' . $id . '">
          <div class="row">
            <h3><time datetime="' . $localDate . '">';
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
             $body .= '</h3>';

            if($numberOfThoughts > 0) {
            $body .= '<div class="number-of-thoughts">
              <span>' . $numberOfThoughts . '</span>
            </div>';
            }
          $body .= '</div>
          <h4>' . $eventTitle . '</h4>
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
    $body .= '</a>';

    if($this->getIsAdmin()) {
      $body .= '<ul class="row actionButtons">
        <li><a class="edit" href="editEvent/index.php?id=' . $id . '&year=' . $year . '&month=' . $month . '&day=' . $day . '">Edit</a></li>';
        if($hide == 0) {
          $body .= '<li><a class="hide" href="hideEvent.php?id=' . $id . '&year=' . $year . '&month=' . $month . '&day=' . $day . '">Hide</a></li>';
        }
        else if($hide == 1) {
          $body .= '<li><a class="hide" href="unhideEvent.php?id=' . $id . '&year=' . $year . '&month=' . $month . '&day=' . $day . '">Unhide</a></li>';
        }
        $body .= '<li><a class="delete" href="confirmation.php?id=' . $id . '">Delete</a></li>
        </ul>';
      }

  $body .= '</div>
  <br />';
    }

    $body = navButtons($link, $body);

    return array($header, $body);
  }

  function main(): string
  {
    $html = '<table>
        <tr>
          <td rowspan="3">Legend</td>
          <td class="remembered-memory">Remembered memory</td>
        </tr>
        <tr>
          <td class="diary-memory">Diary memory</td>
        </tr>';

        if($this->getIsAdmin()) {
          $html .= '<tr>
            <td class="diary-memory hidden-memory">Hidden memory</td>
          </tr>';
        }

      $html .= '</table>
      <br />';

      $html .= $this->addEvent();
      $html .= '<div id="grid-container">';
      $sqlResult = $this->fetchEvents($this->link, $this->getDate());

      while($row = mysqli_fetch_array($sqlResult)) {
        $year = $row['Year'];

        $html .= $this->yearView($sqlResult);
      }

      $html .= '</div>';
    }

    return $html;
  }
}

$config = new Config();
$link = $config->connectToServer();

$timeline = new Timeline();
$timeline->setLink($link);
$timeline->setTitle("Ephraim Becker - Timeline");
$timeline->setLocalStyleSheet('css/style.css');
$timeline->setLocalScript('js/ajax.js');
$timeline->setHeader('Ephraim Becker - Timeline');
$timeline->setUrl($_SERVER['REQUEST_URI']);
$timeline->setBody($timeline->main());

$timeline->html();
?>
