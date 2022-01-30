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
  private $query;

  function __construct()
  {

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

  function getQuery(): string
  {
    return $this->query;
  }

  function setQuery($query): void
  {
    $this->query = $query;
  }

  function getIsAdmin(): bool
  {
    return $this->isAdmin;
  }

  function getYear(): int
  {
    return $this->year;
  }

  function setYear($year): void
  {
    $this->year = $year;
  }

  function getMonth(): int
  {
    return $this->month;
  }

  function setMonth($month): void
  {
    $this->month = $month;
  }

  function getDay(): int
  {
    return $this->day;
  }

  function setDay($day): void
  {
    $this->day = $day;
  }

  function selectQuery(): void
  {
    $query = 'SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, " ", EventTime) - INTERVAL EventTimeZoneOffset SECOND, "%Y-%m-%d"), EventDate) AS "LocalDate", IFNULL(TIME_FORMAT(concat(EventDate, " ", EventTime) - INTERVAL EventTimeZoneOffset SECOND, "%H:%i:%s"), NULL) AS "LocalTime"';

    if($this->getYear() == 0 && $this->getMonth() == 0 && $this->getDay() == 0) {
      $query .= ', DATE_FORMAT(EventDate - INTERVAL EventTimeZoneOffset SECOND, "%Y") AS Year';
    } else if($this->getYear() > 0 && $this->getMonth() == 0 && $this->getDay() == 0) {
      $query .= ', MONTH(EventDate) AS Month';
    } else if($this->getYear() > 0 && $this->getMonth() > 0 && $this->getDay() == 0) {
      $query .= ', DAY(EventDate) AS Day';
    }

    $query .= ' FROM timeline';

    if($this->getYear() > 0) {
      $query .= ' WHERE YEAR(EventDate) = ?';
    }

    if($this->getMonth() > 0) {
      $query .= ' AND MONTH(EventDate) = ?';
    }

    if($this->getDay() > 0) {
      $query .= ' AND DAY(EventDate) = ?';
    }

    if($this->getIsAdmin() == false) {
      if($this->getYear() > 0) {
        $query .= ' AND';
      } else {
        $query .= ' WHERE';
      }

      $query .= ' hide = 0';
    }

    if($this->getYear() == 0 && $this->getMonth() == 0 && $this->getDay() == 0) {
      $query .= ' GROUP BY Year';
    } else {
      if($this->getMonth() == 0) {
        $query .= ' GROUP BY Month';
      } else if($this->getDay() == 0) {
        $query .= ' GROUP BY Day';
      }
       $query .= ' ORDER BY EventDate, EventTime';
    }

    $this->setQuery($query);
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

  function fetchEvents(): mysqli_result
  {
    if($this->getYear() > 0) {
      $sql = $this->getLink()->prepare($this->getQuery());

      if($this->getDay() > 0) {
        $sql->bind_param("iii", $year, $month, $day);
      } else if($this->getMonth() > 0) {
        $sql->bind_param("ii", $year, $month);
      } else {
        $sql->bind_param("i", $year);
      }

      $year = $this->getYear();
      $month = $this->getMonth();
      $day = $this->getDay();

      $sql->execute();

      $sqlResult = $sql->get_result();
    } else {
      $sql = $this->getQuery();
      $sqlResult = mysqli_query($this->getLink(), $sql);
    }

    return $sqlResult;
  }

  function navButtons(): string
  {
    $body = '<div class="row">';

    if($this->getIsAdmin()) {
      $sqlDatesDesc = $this->getLink()->prepare("SELECT EventDate, YEAR(EventDate) AS Year, MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE EventDate<? GROUP BY EventDate ORDER BY EventDate DESC LIMIT 1");
    } else {
      $sqlDatesDesc = $this->getLink()->prepare("SELECT EventDate, YEAR(EventDate) AS Year, MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE EventDate<? AND hide = 0 GROUP BY EventDate ORDER BY EventDate DESC LIMIT 1");
    }
    $sqlDatesDesc->bind_param("s", $navvedEventDateDesc);

    $navvedEventDateDesc = $this->getYear() . "-" . $this->getMonth() . "-" . $this->getDay();

    $sqlDatesDesc->execute();

    $sqlDatesDescResult = $sqlDatesDesc->get_result();

    if($sqlDatesDescResult->num_rows > 0) {
      while($row = mysqli_fetch_array($sqlDatesDescResult)) {
        $prevYear = $row['Year'];
        $prevMonth = $row['Month'];
        $prevDay = $row['Day'];
      }
      if($this->getMonth() > 0 && $this->getDay() > 0) {
        $year = $prevYear;
        $month = $prevMonth;
        $day = $prevDay;
      } else if($this->getMonth() == 0 && $this->getDay() == 0) {
        if($this->getIsAdmin()) {
          $sqlDatesTwoDesc = $this->getLink()->prepare("SELECT EventDate, YEAR(EventDate) AS Year, MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE EventDate<? GROUP BY Year ORDER BY EventDate DESC LIMIT 1");
        } else {
          $sqlDatesTwoDesc = $this->getLink()->prepare("SELECT EventDate, YEAR(EventDate) AS Year, MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE EventDate<? AND hide = 0 GROUP BY Year ORDER BY EventDate DESC LIMIT 1");
        }
        $sqlDatesTwoDesc->bind_param("s", $navvedEventDateDesc);

        $navvedEventDateDesc = $this->getYear() . "-0-0";

        $sqlDatesTwoDesc->execute();

        $sqlDatesTwoDescResult = $sqlDatesTwoDesc->get_result();

        while($rowTwoDesc = mysqli_fetch_array($sqlDatesTwoDescResult)) {
          $year = $rowTwoDesc['Year'];
          $month = 0;
          $day = 0;
        }
      } else if($this->getMonth() > 0 && $this->getDay() == 0) {
        if($this->getIsAdmin()) {
          $sqlDatesThreeDesc = $this->getLink()->prepare("SELECT EventDate, YEAR(EventDate) AS Year, MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE EventDate<? ORDER BY EventDate DESC LIMIT 1");
        } else {
          $sqlDatesThreeDesc = $this->getLink()->prepare("SELECT EventDate, YEAR(EventDate) AS Year, MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE EventDate<? AND hide = 0 ORDER BY EventDate DESC LIMIT 1");
        }
        $sqlDatesThreeDesc->bind_param("s", $navvedEventDateDesc);

        $navvedEventDateDesc = $this->getYear() . "-" . $this->getMonth() . "-0";

        $sqlDatesThreeDesc->execute();

        $sqlDatesThreeDescResult = $sqlDatesThreeDesc->get_result();

        while($rowThreeDesc = mysqli_fetch_array($sqlDatesThreeDescResult)) {
          $year = $rowThreeDesc['Year'];
          $month = $rowThreeDesc['Month'];
          $day = 0;
        }
      }

      $body .= '<br />
      <div class="navButton">
        <a href="./index.php?year=' . $year . '&month=' . $month . '&day=' . $day . '">
         <p><</p>
        </a>
      </div>
      </a>';
    }

    if($this->getIsAdmin()) {
      $sqlDates = $this->getLink()->prepare("SELECT EventDate, YEAR(EventDate) AS Year, MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE EventDate>? GROUP BY EventDate LIMIT 1");
    } else {
      $sqlDates = $this->getLink()->prepare("SELECT EventDate, YEAR(EventDate) AS Year, MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE EventDate>? AND hide = 0 GROUP BY EventDate LIMIT 1");
    }
    $sqlDates->bind_param("s", $navvedEventDate);

    $navvedEventDate = $this->getYear() . "-" . $this->getMonth() . "-" . $this->getDay();

    $sqlDates->execute();

    $sqlDatesResult = $sqlDates->get_result();

    if($sqlDatesResult->num_rows > 0) {
      while($row = mysqli_fetch_array($sqlDatesResult)) {
        $nextYear = $row['Year'];
        $nextMonth = $row['Month'];
        $nextDay = $row['Day'];
      }

      if($this->getMonth() > 0 && $this->getDay() > 0) {
        $year = $nextYear;
        $month = $nextMonth;
        $day = $nextDay;
      } else if($this->getMonth() == 0 && $this->getDay() == 0) {
        $sqlDatesTwo = $this->getLink()->prepare("SELECT EventDate, YEAR(EventDate) AS Year, MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE EventDate>? AND hide=0 GROUP BY Year LIMIT 1");
        $sqlDatesTwo->bind_param("s", $navvedEventDate);

        $navvedEventDate = $nextYear . "-12-31";

        $sqlDatesTwo->execute();

        $sqlDatesTwoResult = $sqlDatesTwo->get_result();

        while($rowTwo = mysqli_fetch_array($sqlDatesTwoResult)) {
          $year = $rowTwo['Year'];
          $month = 0;
          $day = 0;
        }
      } else if($this->getMonth() > 0 && $this->getDay() == 0) {
        if($this->getIsAdmin()) {
            $sqlDatesThree = $this->getLink()->prepare("SELECT EventDate, YEAR(EventDate) AS Year, MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE EventDate>? LIMIT 1");
        } else {
          $sqlDatesThree = $this->getLink()->prepare("SELECT EventDate, YEAR(EventDate) AS Year, MONTH(EventDate) AS Month, DAY(EventDate) AS Day FROM timeline WHERE EventDate>? AND hide=0 LIMIT 1");
        }
        $sqlDatesThree->bind_param("s", $navvedEventDate);

        $navvedEventDate = $nextYear . "-" . $this->getMonth() . "-31";

        $sqlDatesThree->execute();

        $sqlDatesThreeResult = $sqlDatesThree->get_result();

        while($rowThree = mysqli_fetch_array($sqlDatesThreeResult)) {
          $year = $rowThree['Year'];
          $month = $rowThree['Month'];
          $day = 0;
        }
      }

      $body .= '
      <div class="navButton">
        <a href="./index.php?year=' . $year . '&month=' . $month . '&day=' . $day . '">
         <p>></p>
        </a>
      </div>';
    }

    $body .= '</div>';

    return $body;
  }

  function yearView($sqlResult, $year): string
  {
    $html = '<div class="card album-cover" id="album-cover-' . $year . '">';

    if($this->getIsAdmin()) {
      $html .= '<a class="edit-background-image" href="changeBackground/index.php?id=' . $year . '"><img src="/img/icons/edit_black_24dp.svg" width="25" height="25" /></a>';
    }

    $html .= '<a href="./index.php?year=' . $year . '&month=0&day=0">';
    $html .= '<h3>' . $year . '</h3>';
    $html .= '<p>All the events in ' . $year . ' when I was ' . ($year-1996) . ' years old</p>';
    $html .= '</a>';
    $html .= '</div>';

    return $html;
  }

  function displayAllEvents($sqlResult, $row) {
    $this->setHeader('Ephraim Becker - Timeline');

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

      if(!is_null($endEventDate)) {
        $endEventDateFormatted = date("F d, Y", strtotime($endEventDate));
      }

      $eventDateFormatted = date("F d, Y", strtotime($localDate));

      if(!is_null($eventTime)) {
        $eventTimeFormatted = date("h:i A", strtotime($localTime));
      }

      $eventTitle = $row['EventTitle'];
      $eventDescription = $row['EventDescription'];
      $memoryType = $row['MemoryType'];

      $eventYouTubeLink = $row['EventYouTubeLink'];

      $eventMedia = $row['EventMedia'];
      $eventMediaPortrait = $row['EventMediaPortrait'];
      $eventMediaDescription = $row['EventMediaDescription'];

      $sqlThoughtsRaw = "SELECT COUNT(*) AS NumberOfThoughts FROM thoughts WHERE TimelineId=?";

      if(!$this->getIsAdmin()) {
        $sqlThoughtsRaw .= " AND hide=0";
      }

      $sqlThoughts = $this->getLink()->prepare($sqlThoughtsRaw);
      $sqlThoughts->bind_param("i", $id);

      $sqlThoughts->execute();

      $sqlThoughtsResult = $sqlThoughts->get_result();

      while($rowTwo = mysqli_fetch_array($sqlThoughtsResult)) {
        $numberOfThoughts = $rowTwo['NumberOfThoughts'];
      }

      $body = '<div style="margin-bottom: 10px;" class="event ';
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
              $body .= '<img src="img/' . $eventMedia . '" alt="' . $eventMediaDescription . '" width="200px" height="113px" />';
            } else {
              $body .= '<img src="img/' . $eventMedia . '" alt="' . $eventMediaDescription . '" width="113px" height="200px" />';
            }
          }
          $body .= '</a>';

          if($this->getIsAdmin()) {
            $body .= '<ul class="row actionButtons">
              <li><a class="edit" href="editEvent/index.php?id=' . $id . '&year=' . $this->getYear() . '&month=' . $this->getMonth() . '&day=' . $this->getDay() . '">Edit</a></li>';
              if($hide == 0) {
                $body .= '<li><a class="hide" href="hideEvent.php?id=' . $id . '&year=' . $this->getYear() . '&month=' . $this->getMonth() . '&day=' . $this->getDay() . '">Hide</a></li>';
              }
              else if($hide == 1) {
                $body .= '<li><a class="hide" href="unhideEvent.php?id=' . $id . '&year=' . $this->getYear() . '&month=' . $this->getMonth() . '&day=' . $this->getDay() . '">Unhide</a></li>';
              }
              $body .= '<li><a class="delete" href="confirmation.php?id=' . $id . '&year=' . $this->getYear() . '&month=' . $this->getMonth() . '&day=' . $this->getDay() . '">Delete</a></li>
              </ul>';
            }

        $body .= '</div>
        <br />';

    return $body;
  }

  function displaySorter($sqlResult, $row) {
    if(isset($row['Month'])) {
      $month = $row['Month'];
    } else {
      $month = $this->getMonth();
    }

    $dateObj = DateTime::createFromFormat('!m', strval($month));
    $monthName = $dateObj->format('F');

    if(isset($row['Day'])) {
      $day = $row['Day'];
    } else {
      $day = 0;
    }

    $body = '<div class="card album-cover" id="album-cover-';
    if($day != 0) {
      $body .= $this->getYear() . '-' . $month . '-' . $day;
      } else {
        $body .= $this->getYear() . '-' . $month;
      }
      $body .= '">';
      $body .= '<a href="./index.php?year=' . $this->getYear() . '&month=' . $month . '&day=' . $day . '">';

      if($day != 0) {
        $body .= '<h3>' . $monthName . " " . $day . '</h3>';
        $body .= '<p>All the events on ' . $monthName . " " . $day . '</p>';
      } else {
        $body .= '<h3>' . $monthName . '</h3>';
        $body .= '<p>All the events on ' . $monthName . '</p>';
      }
      $body .= '</a>';
      $body .= '</div>';

      return $body;
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
      if($this->getYear() == 0 || $this->getMonth() == 0 || $this->getDay() == 0) {
        $html .= '<div id="grid-container">';
      } else {
        $html .= '<div id="row">';
      }
      $sqlResult = $this->fetchEvents();

      while($row = mysqli_fetch_array($sqlResult)) {
        if(isset($row['Year'])) {
          $year = $row['Year'];
        }

        if(isset($row['month'])) {
          $month = $row['month'];
        }

        if(isset($row['day'])) {
          $day = $row['day'];
        }

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

        if(!is_null($endEventDate)) {
          $endEventDateFormatted = date("F d, Y", strtotime($endEventDate));
        }

        $eventDateFormatted = date("F d, Y", strtotime($localDate));

        if(!is_null($eventTime)) {
          $eventTimeFormatted = date("h:i A", strtotime($localTime));
        }

        $eventTitle = $row['EventTitle'];
        $eventDescription = $row['EventDescription'];
        $memoryType = $row['MemoryType'];

        $eventYouTubeLink = $row['EventYouTubeLink'];

        $eventMedia = $row['EventMedia'];
        $eventMediaPortrait = $row['EventMediaPortrait'];
        $eventMediaDescription = $row['EventMediaDescription'];

        if($this->getYear() == 0) {
          $html .= $this->yearView($sqlResult, $year);
        } else {
          if($this->getMonth() == 0 || $this->getDay() == 0) {
            $html .= $this->displaySorter($sqlResult, $row);
          } else {
            $html .= $this->displayAllEvents($sqlResult, $row);
          }
        }
      }

      $html .= '</div>';

      if($this->getYear() > 0) {
        $html .= $this->navButtons();
      }

      return $html;
  }
}

$config = new Config();
$link = $config->connectToServer();

$timeline = new Timeline();
$timeline->setLink($link);
$timeline->setIsAdmin();

if(isset($_GET['year'])) {
  $year = intval($_GET['year']);
} else {
  $year = 0;
}
$timeline->setYear($year);

if(isset($_GET['month'])) {
  $month = intval($_GET['month']);
} else {
  $month = 0;
}
$timeline->setMonth($month);

if(isset($_GET['day'])) {
  $day = intval($_GET['day']);
} else {
  $day = 0;
}
$timeline->setDay($day);

$timeline->selectQuery();
$timeline->setTitle("Ephraim Becker - Timeline");
$timeline->setLocalStyleSheet('css/style.css');
$timeline->setLocalScript(NULL);
$timeline->setHeader('Ephraim Becker - Timeline');
$timeline->setUrl("/timeline/index.php%3Fyear=" . $timeline->getYear() . "%26month=" . $timeline->getMonth() . "%26day=" . $timeline->getDay());
$timeline->setBody($timeline->main());

$timeline->html();
?>
