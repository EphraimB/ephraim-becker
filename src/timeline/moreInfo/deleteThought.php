<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class DeleteThought
{
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
    return $this->id;
  }

  function deleteThought(): void
  {
    $sql = $this->getLink()->prepare("SELECT TimelineId FROM thoughts WHERE ThoughtId=? LIMIT 1");

    $sql->bind_param("i", $id);

    $id = $this->getId();

    $sql->execute();

    $sqlResult = $sql->get_result();

    while($row = mysqli_fetch_array($sqlResult)) {
      $timelineId = $row['TimelineId'];
    }

    $sql->close();

    $sqlTwo = $this->getLink()->prepare("DELETE FROM thoughts WHERE ThoughtId=?");
    $sqlTwo->bind_param("i", $id);

    $sqlTwo->execute();

    header("location: index.php?id=" . $timelineId);
  }
}
$config = new Config();
$link = $config->connectToServer();

$deleteThought = new DeleteThought();
$deleteThought->setLink($link);
$deleteThought->setId(intval($_GET['id']));
$deleteThought->deleteThought();
?>
