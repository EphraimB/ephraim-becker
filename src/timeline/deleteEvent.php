<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class DeleteEvent
{
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

  function deleteEvent(): void
  {
    $sql = $this->getLink()->prepare("DELETE FROM timeline WHERE TimelineId=?");
    $sql->bind_param("i", $id);

    $id = $_GET['id'];

    $sql->execute();

    $sql->close();
    $this->getLink()->close();

    header("location: ../");
  }
}
$config = new Config();
$link = $config->connectToServer();

$deleteEvent = new DeleteEvent();
$deleteEvent->setLink($link);
$deleteEvent->deleteEvent();
?>
