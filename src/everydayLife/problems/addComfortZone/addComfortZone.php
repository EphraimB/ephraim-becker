<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class AddComfortZone
{
  private $isAdmin;
  private $link;
  private $comfortZone;
  private $reason;

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

  function setComfortZone($comfortZone): void
  {
    $this->comfortZone = $comfortZone;
  }

  function getComfortZone(): string
  {
    return $this->comfortZone;
  }

  function setReason($reason): void
  {
    $this->reason = $reason;
  }

  function getReason(): string
  {
    return $this->reason;
  }

  function addComfortZone(): void
  {
    $sql = $this->getLink()->prepare("INSERT INTO ComfortZone (ComfortZone, reason, dateCreated, dateModified) VALUES (?, ?, ?, ?)");
    $sql->bind_param('ssss', $comfortZone, $reason, $dateNow, $dateNow);

    $comfortZone = $this->getComfortZone();
    $reason = $this->getReason();

    $dateNow = date("Y-m-d H:i:s");

    $sql->execute();

    header("location: ../");
  }
}

$config = new Config();
$link = $config->connectToServer();

$addComfortZone = new AddComfortZone();
$addComfortZone->setLink($link);
$addComfortZone->setComfortZone($_POST['ComfortZone']);
$addComfortZone->setReason($_POST['reason']);

$addComfortZone->addComfortZone();
?>
