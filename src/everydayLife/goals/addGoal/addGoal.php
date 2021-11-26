<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class AddGoal
{
  private $isAdmin;
  private $link;
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

  function addGoal(): void
  {
    $sql = $this->getLink()->prepare("INSERT INTO goals (Goal, How, dateCreated, dateModified) VALUES (?, ?, ?, ?)");
    $sql->bind_param('ssss', $goal, $how, $dateNow, $dateNow);

    $goal = $this->getGoal();
    $how = $this->getHow();

    $dateNow = date("Y-m-d H:i:s");

    $sql->execute();

    header("location: ../");
  }
}

$config = new Config();
$link = $config->connectToServer();

$addGoal = new AddGoal();
$addGoal->setLink($link);
$addGoal->setGoal($_POST['goal']);
$addGoal->setHow($_POST['how']);

$addGoal->addGoal();
?>
