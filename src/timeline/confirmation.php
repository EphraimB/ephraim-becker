<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EventConfirmation extends Base
{
  private $isAdmin;
  private $college_id;
  private $link;
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

  function setLink($link)
  {
    $this->link = $link;
  }

  function getLink()
  {
    return $this->link;
  }

  function setId($id): void
  {
    $this->id = $id;
  }

  function getId(): int
  {
    return $this->id;
  }

  function setYear($year): void
  {
    $this->year = $year;
  }

  function getYear(): int
  {
    return $this->year;
  }

  function setMonth($month): void
  {
    $this->month = $month;
  }

  function getMonth(): int
  {
    return $this->month;
  }

  function setDay($day): void
  {
    $this->day = $day;
  }

  function getDay(): int
  {
    return $this->day;
  }

  function main(): string
  {
    $sql = $this->getLink()->prepare("SELECT EventTitle FROM timeline WHERE TimelineId=?");
    $sql->bind_param("i", $id);

    $id = $this->getId();

    $sql->execute();

    $sqlResult = $sql->get_result();

    while($row = mysqli_fetch_array($sqlResult)){
      $eventTitle = $row['EventTitle'];
    }

    $body = '<h2>Are you sure you want to delete the event named "' . $eventTitle . '"?</h2>

    <div class="row actionButtons">';
      if($this->getYear() == 0) {
        $body .= '<a class="keep" href="moreInfo/index.php?id=' . $this->getId() . '">No</a>';
      } else {
        $body .= '<a class="keep" href="index.php?year=' . $this->getYear() . '&month=' . $this->getMonth() . '&day=' . $this->getDay() . '">No</a>';
      }
      $body .= '<a class="delete" href="deleteEvent.php?id=' . $this->getId() . '">Yes</a>
    </div>';

    return $body;
  }
}

$config = new Config();
$link = $config->connectToServer();

$eventConfirmation = new EventConfirmation();
$eventConfirmation->setLink($link);
$eventConfirmation->setId(intval($_GET['id']));
$eventConfirmation->setYear(intval($_GET['year']));
$eventConfirmation->setMonth(intval($_GET['month']));
$eventConfirmation->setDay(intval($_GET['day']));

$eventConfirmation->setTitle("Ephraim Becker - Timeline - Delete?");
$eventConfirmation->setLocalStyleSheet("css/style.css");
$eventConfirmation->setLocalScript(NULL);
$eventConfirmation->setHeader("Timeline - Delete?");
$eventConfirmation->setUrl($_SERVER['REQUEST_URI']);
$eventConfirmation->setBody($eventConfirmation->main());

$eventConfirmation->html();
?>
