<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditThought
{
  private $isAdmin;
  private $link;
  private $id;
  private $thought;
  private $hidden;

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

  function setThought($thought): void
  {
    $this->thought = $thought;
  }

  function getThought(): string
  {
    return $this->thought;
  }

  function setHidden($hidden): void
  {
    $this->hidden = $hidden;
  }

  function getHidden(): int
  {
    return $this->hidden;
  }

  function editThought(): void
  {
    $sql = $this->getLink()->prepare("UPDATE thoughts SET DateModified=?, Thought=?, hide=? WHERE ThoughtId=?");
    $sql->bind_param('ssii', $dateModified, $thought, $hidden, $id);

    $id = $this->getId();

    $dateModified = date("Y-m-d H:i:s");
    $thought = $this->getThought();
    $hidden = $this->getHidden();

    $sql->execute();

    $sql->close();

    $sqlTwo = $this->getLink()->prepare("SELECT TimelineId FROM thoughts WHERE ThoughtId=? LIMIT 1");

    $sqlTwo->bind_param("i", $id);

    $sqlTwo->execute();

    $sqlTwoResult = $sqlTwo->get_result();

    while($row = mysqli_fetch_array($sqlTwoResult)) {
      $timelineId = $row['TimelineId'];
    }

    header("location: ../index.php?id=". $timelineId);
  }
}
$config = new Config();
$link = $config->connectToServer();

$editThought = new EditThought();
$editThought->setLink($link);
$editThought->setId(intval($_POST['id']));
$editThought->setThought($_POST['thought']);

if(empty($_POST['hidden'])) {
  $hidden = 0;
} else {
  $hidden = $_POST['hidden'];
}
$editThought->setHidden(intval($hidden));

$editThought->editThought();
?>
