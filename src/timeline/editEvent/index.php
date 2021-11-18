<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditEventForm extends Base
{
  private $isAdmin;
  private $link;
  private $query;
  private $year;
  private $month;
  private $day;
  private $id;

  function __construct()
  {
    $this->setIsAdmin();

    if(!$this->getIsAdmin()) {
      header("location: ../");
    }
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

  function setLink($link): void
  {
    $this->link = $link;
  }

  function getLink()
  {
    return $this->link;
  }

  function setYear($year): void
  {
    $this->year = $year;
  }

  function getYear(): int
  {
    return intval($this->year);
  }

  function setMonth($month): void
  {
    $this->month = $month;
  }

  function getMonth(): int
  {
    return intval($this->month);
  }

  function setDay($day): void
  {
    $this->day = $day;
  }

  function getDay(): int
  {
    return intval($this->day);
  }

  function setId($id): void
  {
    $this->id = $id;
  }

  function getId(): int
  {
    return intval($this->id);
  }

  function getQuery(): string
  {
    return $this->query;
  }

  function setQuery($query): void
  {
    $this->query = $query;
  }

  function fetchEvent(): mysqli_result
  {
    $sql = $this->getLink()->prepare($this->getQuery());
    $sql->bind_param("i", $id);

    $id = $this->getId();

    $sql->execute();

    $sqlResult = $sql->get_result();

    return $sqlResult;
  }

  function main(): string
  {
    $body = '<form action="editEvent.php" method="post" enctype="multipart/form-data">
          <div class="row">';

    $sqlResult = $this->fetchEvent();

    while($row = mysqli_fetch_array($sqlResult)) {
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

      $body .= '
        <div>
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
            $body = "checked";
          }
          $body .= '>
          <label for="endEventDateExist">End event date exist?</label>
        </div>
      </div>
      <br />
      <div>
        <label for="eventTitle">Event title:</label>
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
      <input type="hidden" name="year" value="' . $this->getYear() . '" />
      <input type="hidden" name="month" value="' . $this->getMonth() . '" />
      <input type="hidden" name="day" value="' . $this->getDay() . '" />
      <br />
      <input type="submit" id="submit" value="Edit event" />
      <br />
    </form>';

    return $body;
  }
}
$config = new Config();
$link = $config->connectToServer();

$editEventForm = new EditEventForm();
$editEventForm->setLink($link);
$editEventForm->setYear($_GET['year']);
$editEventForm->setMonth($_GET['month']);
$editEventForm->setDay($_GET['day']);
$editEventForm->setId($_GET['id']);
$editEventForm->setQuery("SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime' FROM timeline WHERE TimelineId=?");

$editEventForm->setLocalStyleSheet("css/style.css");
$editEventForm->setLocalScript("js/script.js");
$editEventForm->setTitle("Ephraim Becker - Timeline - Edit Event");
$editEventForm->setHeader("Timeline - Edit Event");
$editEventForm->setUrl($_SERVER['REQUEST_URI']);
$editEventForm->setBody($editEventForm->main());

$editEventForm->html();
?>
