<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class MoreInfo extends Base
{
  private $link;
  private $isAdmin;
  private $year;
  private $month;
  private $day;
  private $query;
  private $id;

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

  function setId($id): void
  {
    $this->id = $id;
  }

  function getId(): int
  {
    return intval($this->id);
  }

  function selectEventQuery(): void
  {
    $sql = "SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime' FROM timeline WHERE TimelineId=?";

    if(!$this->getIsAdmin()) {
      $sql .= " AND hide=0";
    }

    $this->setQuery($sql);
  }

  function selectThoughtsQuery(): void
  {
    $sql = "SELECT * FROM thoughts WHERE TimelineId=?";

    if(!$this->getIsAdmin()) {
      $sql .= " AND hide=0";
    }

    $this->setQuery($sql);
  }

  function fetchData(): mysqli_result
  {
    $sql = $this->getLink()->prepare($this->getQuery());
    $sql->bind_param("i", $id);

    $id = $this->getId();

    $sql->execute();

    $sqlResult = $sql->get_result();

    return $sqlResult;
  }

  function showEvent(): string
  {
    $this->selectEventQuery();

    $sqlResult = $this->fetchData();

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

      if(is_null($endEventDate)) {
        $endEventDateFormatted = NULL;
      } else {
        $endEventDateFormatted = date("F d, Y", strtotime($endEventDate));
      }

      $eventDateFormatted = date("F d, Y", strtotime($localDate));

      if(is_null($eventTime)) {
        $eventTimeFormatted = NULL;
      } else {
        $eventTimeFormatted = date("h:i A", strtotime($localTime));
      }

      $eventTitle = $row['EventTitle'];
      $eventDescription = $row['EventDescription'];
      $memoryType = $row['MemoryType'];

      $eventYouTubeLink = $row['EventYouTubeLink'];

      $eventMedia = $row['EventMedia'];
      $eventMediaPortrait = $row['EventMediaPortrait'];
      $eventMediaDescription = $row['EventMediaDescription'];
    }

    $this->setHeader("Timeline - " . $eventTitle);

    $body = '<div class="';

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

        if($this->getIsAdmin()) {
          $body .= '<ul class="row actionButtons">
            <li><a class="edit" href="../editEvent/index.php?id=' . $this->getId() . '&year=0&month=0&day=0">Edit</a></li>';

            if($hide == 0) {
              $body .= '<li><a class="hide" href="../hideEvent.php?id=' . $this->getId() . '&year=0&month=0&day=0">Hide</a></li>';
            }

            else if($hide == 1) {
              $body .= '<li><a class="hide" href="../unhideEvent.php?id=' . $this->getId() . '&year=0&month=0&day=0">Unhide</a></li>';
            }

            $body .= '<li><a class="delete" href="../confirmation.php?id=' . $this->getId() . '&year=0&month=0&day=0">Delete</a></li>
          </ul>';
        }

    $body .= '</div>';

    return $body;
  }

  function showThoughts(): string
  {
    $body = '';

    if(isset($_GET['offset'])) {
      $offset = $_GET['offset'];
    } else {
      $offset = NULL;
    }

    $this->selectThoughtsQuery();
    $sqlResult = $this->fetchData();

    while($row = mysqli_fetch_array($sqlResult)) {
      $hide = $row['hide'];
      $thoughtId = $row['ThoughtId'];
      $date = $row['DateCreated'];
      $dateModified = $row['DateModified'];
      $thought = $row['Thought'];

      $body .= '<div class="thought';

      if($hide == 1) {
        $body .= ' hidden-memory';
      }
      $body .= '"><h2 class="date"><time datetime="' . date('Y-m-d H:i:s', strtotime($date) - intval($offset)) . '">' . date('m/d/Y h:i A', strtotime($date) - intval($offset)) . '</time></h2>
          <p>' . $thought . '</p>';
          if($this->getIsAdmin()) {
            $body .= '<ul class="row actionButtons">
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

     if($this->getIsAdmin()) {
       $body .= '<br />
       <br />
       <form action="addThought.php" method="post">
         <textarea name="thought" rows="6" cols="45" required></textarea>
         <input type="hidden" name="id" value="' . $this->getId() . '" />

         <input class="thoughtButton" type="submit" value="Add thought" />
       </form>';
     }

     return $body;
  }

  function main(): string
  {
    $html = $this->showEvent();
    $html .= "<br />";
    $html .= $this->showThoughts();

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$moreInfo = new MoreInfo();
$moreInfo->setLink($link);
$moreInfo->setId($_GET['id']);
$moreInfo->setIsAdmin();
$moreInfo->setTitle("Ephraim Becker - Timeline - More info");
$moreInfo->setLocalStyleSheet('../css/style.css');
$moreInfo->setLocalScript('js/script.js');
$moreInfo->setUrl($_SERVER['REQUEST_URI']);
$moreInfo->setBody($moreInfo->main());

$moreInfo->html();
?>
