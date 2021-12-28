<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class HideThought
{
  private $isAdmin;
  private $link;
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

  function hideThought(): void
  {
    $sql = $this->getLink()->prepare("UPDATE thoughts SET hide = 1 WHERE ThoughtId=?");
    $sql->bind_param("i", $id);

    $id = $this->getId();

    $sql->execute();

    $sql->close();

    $sqlTwo = $this->getLink()->prepare("SELECT TimelineId FROM thoughts WHERE ThoughtId=? LIMIT 1");

    $sqlTwo->bind_param("i", $id);

    $sqlTwo->execute();

    $sqlTwoResult = $sqlTwo->get_result();

    while($row = mysqli_fetch_array($sqlTwoResult)) {
      $timelineId = $row['TimelineId'];
    }

    $sqlTwo->close();
    $this->getLink()->close();

    header("location: index.php?id=" . $timelineId);
  }
}
$config = new Config();
$link = $config->connectToServer();

$hideThought = new HideThought();
$hideThought->setLink($link);
$hideThought->setId(intval($_GET['id']));
$hideThought->hideThought();
?>
