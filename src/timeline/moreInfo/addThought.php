<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class AddThought
{
  private $isAdmin;
  private $link;
  private $id;
  private $timezone;
  private $timezoneOffset;
  private $thought;

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

  function setTimezone($timezone): void
  {
    $this->timezone = $timezone;
  }

  function getTimezone(): string
  {
    return $this->timezone;
  }

  function setTimezoneOffset($timezoneOffset): void
  {
    $this->timezoneOffset = $timezoneOffset;
  }

  function getTimezoneOffset(): int
  {
    return $this->timezoneOffset;
  }

  function setThought($thought): void
  {
    $this->thought = $thought;
  }

  function getThought(): string
  {
    return $this->thought;
  }

  function addThought(): void
  {
    $sql = $this->getLink()->prepare("INSERT INTO thoughts (TimelineId, DateCreated, DateModified, timezone, timezoneOffset, Thought)
    VALUES (?, ?, ?, ?, ?, ?)");
    $sql->bind_param("isssis", $id, $now, $now, $timezone, $timezoneOffset, $thought);

    $id = $this->getId();
    $now = date("Y-m-d H:i:s");
    $timezone = $this->getTimezone();
    $timezoneOffset = $this->getTimezoneOffset();
    $thought = $this->getThought();

    $sql->execute();

    $sql->close();
    $this->getLink()->close();

    header("location: index.php?id=" . $this->getId());
  }
}
$config = new Config();
$link = $config->connectToServer();

$addThought = new AddThought();
$addThought->setLink($link);
$addThought->setId(intval($_POST['id']));
$addThought->setTimezone($_POST['timezone']);
$addThought->setTimezoneOffset(intval($_POST['timezoneOffset']));
$addThought->setThought($_POST['thought']);
$addThought->addThought();
?>
