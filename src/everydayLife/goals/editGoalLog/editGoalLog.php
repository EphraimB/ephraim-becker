<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditGoalLog
{
  private $isAdmin;
  private $link;
  private $id;
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

  function setId($id): void
  {
    $this->id = $id;
  }

  function getId(): int
  {
    return $this->id;
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

  function editGoalLog(): void
  {
    $sql = $this->getLink()->prepare("UPDATE GoalLog SET GoalId = ?, Log = ?, DateModified = ? WHERE GoalLogId = ?");
    $sql->bind_param('issi', $goalId, $log, $dateNow, $id);

    $goalId = $this->getGoalId();
    $log = $this->getLog();

    $dateNow = date("Y-m-d H:i:s");

    $id = $this->getId();

    $sql->execute();

    header("location: ../");
  }
}

$config = new Config();
$link = $config->connectToServer();

$editGoalLog = new EditGoalLog();
$editGoalLog->setLink($link);
$editGoalLog->setId(intval($_POST['id']));
$editGoalLog->setGoalId(intval($_POST['goalId']));
$editGoalLog->setLog($_POST['log']);

$editGoalLog->editGoalLog();
?>
