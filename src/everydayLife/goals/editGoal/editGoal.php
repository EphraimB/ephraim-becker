<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditGoal
{
  private $isAdmin;
  private $link;
  private $id;
  private $goal;
  private $how;

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

  function setGoal($goal): void
  {
    $this->goal = $goal;
  }

  function getGoal(): string
  {
    return $this->goal;
  }

  function setHow($how): void
  {
    $this->how = $how;
  }

  function getHow(): string
  {
    return $this->how;
  }

  function editGoal(): void
  {
    $sql = $this->getLink()->prepare("UPDATE goals SET Goal = ?, How = ?, dateModified = ? WHERE GoalId = ?");
    $sql->bind_param('sssi', $goal, $how, $dateNow, $id);

    $goal = $this->getGoal();
    $how = $this->getHow();

    $dateNow = date("Y-m-d H:i:s");

    $id = $this->getId();

    $sql->execute();

    header("location: ../");
  }
}

$config = new Config();
$link = $config->connectToServer();

$editGoal = new EditGoal();
$editGoal->setLink($link);
$editGoal->setId(intval($_POST['id']));
$editGoal->setGoal($_POST['goal']);
$editGoal->setHow($_POST['how']);

$editGoal->editGoal();
?>
