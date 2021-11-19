<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class UnhideEvent
{
  private $year;
  private $month;
  private $day;
  private $link;
  private $isAdmin;
  private $id;

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

  function setId($id): void
  {
    $this->id = $id;
  }

  function getId(): int
  {
    return intval($this->id);
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

  function unhideEvent(): void
  {
    $sql = $this->getLink()->prepare("UPDATE timeline SET hide = 0 WHERE TimelineId=?");
    $sql->bind_param("i", $id);

    $id = $this->getId();

    $sql->execute();

    $sql->close();
    $this->getLink()->close();

    if($this->getYear() == 0) {
      header("location: moreInfo/index.php?id=" . $this->getId());
    } else {
      header("location: index.php?year=" . $this->getYear() . "&month=" . $this->getMonth() . "&day=" . $this->getDay());
    }
  }
}

$config = new Config();
$link = $config->connectToServer();

$unhideEvent = new UnhideEvent();
$unhideEvent->setLink($link);
$unhideEvent->setId($_GET['id']);
$unhideEvent->setYear($_GET['year']);
$unhideEvent->setMonth($_GET['month']);
$unhideEvent->setDay($_GET['day']);
$unhideEvent->unhideEvent();
?>
