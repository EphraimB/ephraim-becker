<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class AddGoalLog
{
  private $isAdmin;
  private $link;
  private $goalId;
  private $log;

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

  function setGoalId($goalId): void
  {
    $this->goalId = $goalId;
  }

  function getGoalId(): int
  {
    return $this->goalId;
  }

  function setLog($log): void
  {
    $this->log = $log;
  }

  function getLog(): string
  {
    return $this->log;
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

  function addGoalLog(): void
  {
    $sql = $this->getLink()->prepare("INSERT INTO GoalLog (GoalId, Log, DateCreated, DateModified, timezone, timezoneOffset) VALUES (?, ?, ?, ?, ?, ?)");
    $sql->bind_param('issssi', $goalId, $log, $dateNow, $dateNow, $timezone, $timezoneOffset);

    $goalId = $this->getGoalId();
    $log = $this->getLog();

    $dateNow = date("Y-m-d H:i:s");

    $timezone = $this->getTimezone();
    $timezoneOffset = $this->getTimezoneOffset();

    $sql->execute();

    header("location: ../");
  }
}

$config = new Config();
$link = $config->connectToServer();

$addGoalLog = new AddGoalLog();
$addGoalLog->setLink($link);
$addGoalLog->setGoalId(intval($_POST['goalId']));
$addGoalLog->setLog($_POST['log']);
$addGoalLog->setTimezone($_POST['timezone']);
$addGoalLog->setTimezoneOffset(intval($_POST['timezoneOffset']));

$addGoalLog->addGoalLog();
?>
