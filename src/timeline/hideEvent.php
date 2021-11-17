<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class HideEvent
{
  private $year;
  private $month;
  private $day;
  private $link;
  private $isAdmin;

  function __construct()
  {
    $this->setIsAdmin();

    if(!$this->getIsAdmin()) {
      header("location: ../");
    }
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

  function hideEvent(): void
  {
    $sql = $this->getLink()->prepare("UPDATE timeline SET hide = 1 WHERE TimelineId=?");
    $sql->bind_param("i", $id);

    $id = $_GET['id'];

    $sql->execute();

    $sql->close();
    $this->getLink()->close();

    header("location: index.php?year=" . $this->getYear() . "&month=" . $this->getMonth() . "&day=" . $this->getDay());
  }
}

$config = new Config();
$link = $config->connectToServer();

$hideEvent = new HideEvent();
$hideEvent->setLink($link);
$hideEvent->setYear($_GET['year']);
$hideEvent->setMonth($_GET['month']);
$hideEvent->setDay($_GET['day']);
$hideEvent->hideEvent();
?>
