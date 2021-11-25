<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditComfortZone
{
  private $isAdmin;
  private $link;
  private $id;
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

  function setId($id): void
  {
    $this->id = $id;
  }

  function getId(): int
  {
    return $this->id;
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

  function editComfortZone(): void
  {
    $sql = $this->getLink()->prepare("UPDATE ComfortZone SET ComfortZone = ?, reason = ?, dateModified = ? WHERE ComfortZoneId = ?");
    $sql->bind_param('sssi', $comfortZone, $reason, $dateNow, $id);

    $comfortZone = $this->getComfortZone();
    $reason = $this->getReason();

    $dateNow = date("Y-m-d H:i:s");

    $id = $this->getId();

    $sql->execute();

    header("location: ../");
  }
}

$config = new Config();
$link = $config->connectToServer();

$editComfortZone = new EditComfortZone();
$editComfortZone->setLink($link);
$editComfortZone->setId(intval($_POST['id']));
$editComfortZone->setComfortZone($_POST['ComfortZone']);
$editComfortZone->setReason($_POST['reason']);

$editComfortZone->editComfortZone();
?>
