<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class DeleteFood
{
  private $isAdmin;
  private $link;
  private $cronTabManager;
  private $id;

  function __construct()
  {
    $this->setIsAdmin();

    if(!$this->getIsAdmin()) {
      header("location: ../../");
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

  function setCronTabManager($cronTabManager)
  {
    $this->cronTabManager = $cronTabManager;
  }

  function getCronTabManager()
  {
    return $this->cronTabManager;
  }

  function setId($id): void
  {
    $this->id = $id;
  }

  function getId(): int
  {
    return $this->id;
  }

  function getCronJobId(): int
  {
    $sql = $this->getLink()->prepare("SELECT CronJobId FROM MealPlan WHERE MealPlanId=?");
    $sql->bind_param('i', $id);

    $id = $this->getId();

    $sql->execute();

    $sqlResult = $sql->get_result();

    while($row = mysqli_fetch_array($sqlResult)) {
      $id = $row['CronJobId'];
    }

    return $id;
  }

  function getCronJobCommand($cronJobId): string
  {
    $sql = $this->getLink()->prepare("SELECT command FROM CronJobs WHERE CronJobId=?");
    $sql->bind_param('i', $id);

    $id = $cronJobId;

    $sql->execute();

    $sqlResult = $sql->get_result();

    while($row = mysqli_fetch_array($sqlResult)) {
      $command = $row['command'];
    }

    return $command;
  }

  function deleteCronJobFromDB(): void
  {
     $sql = $this->getLink()->prepare("DELETE FROM CronJobs WHERE CronJobId=?");
     $sql->bind_param('i', $id);

     $id = $this->getCronJobId();

     $sql->execute();
  }

  function deleteFood(): void
  {
    $command = $this->getCronJobCommand($this->getCronJobId());
    $this->deleteCronJobFromDB();

    $crontab = $this->getCronTabManager();
    $crontab->remove_cronjob($command);

    $sql = $this->getLink()->prepare("DELETE FROM MealPlan WHERE MealPlanId=?");
    $sql->bind_param("i", $id);

    $id = $this->getId();

    $sql->execute();

    $sql->close();

    header("location: index.php");
  }
}
$config = new Config();
$link = $config->connectToServer();
$cronTabManager = $config->connectToCron();

$deleteFood = new DeleteFood();
$deleteFood->setLink($link);
$deleteFood->setCronTabManager($cronTabManager);
$deleteFood->setId(intval($_GET['id']));

$deleteFood->deleteFood();
