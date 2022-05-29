<?php
declare(strict_types=1);

session_start();

$home = getenv('HOME');

require_once($home . '/config.php');

class WithdrawalCronJobStart
{
  private $link;
  private $cronTabManager;
  private $withdrawalAmount;
  private $withdrawalDescription;

  function __construct()
  {
    $isCLI = (php_sapi_name() == 'cli');

    if(!$isCLI) {
      die("cannot run!");
    } else {
      parse_str(implode('&', array_slice($_SERVER['argv'], 1)), $_GET);
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

  function setCronTabManager($cronTabManager)
  {
    $this->cronTabManager = $cronTabManager;
  }

  function getCronTabManager()
  {
    return $this->cronTabManager;
  }

  function getCronJobId(): int
  {
    $sql = $this->getLink()->prepare("SELECT CronJobId FROM moneyOwed WHERE ExpenseId=?");
    $sql->bind_param('i', $id);

    $id = $this->getId();

    $sql->execute();

    $sqlResult = $sql->get_result();

    while($row = mysqli_fetch_array($sqlResult)) {
      $id = $row['CronJobId'];
    }

    return $id;
  }

  function getCronJobUniqueId($cronJobId): string
  {
    $sql = $this->getLink()->prepare("SELECT UniqueId FROM CronJobs WHERE CronJobId=?");
    $sql->bind_param('i', $id);

    $id = $cronJobId;

    $sql->execute();

    $sqlResult = $sql->get_result();

    while($row = mysqli_fetch_array($sqlResult)) {
      $uniqueId = $row['UniqueId'];
    }

    return $uniqueId;
  }

  function deleteCronJobFromDB(): void
  {
     $sql = $this->getLink()->prepare("DELETE FROM CronJobs WHERE CronJobId=?");
     $sql->bind_param('i', $id);

     $id = $this->getCronJobId();

     $sql->execute();
  }

  function addCronJobToDB($uniqueId, $command): int
  {
    $sql = $this->getLink()->prepare("INSERT INTO CronJobs (UniqueId, Command, DateCreated, DateModified)
     VALUES (?, ?, ?, ?)");
     $sql->bind_param('ssss', $uniqueId, $command, $dateNow, $dateNow);

     $dateNow = date("Y-m-d H:i:s");

     $sql->execute();

     $sqlTwo = "SELECT LAST_INSERT_ID() AS id";
     $sqlTwoResult = mysqli_query($this->getLink(), $sqlTwo);

     while($row = mysqli_fetch_array($sqlTwoResult)) {
       $id = intval($row['id']);
     }

     return $id;
  }

  function setWithdrawalCronJob()
  {

  }
}
$config = new Config();
$link = $config->connectToServer();
$cronTabManager = $config->connectToCron();

$withdrawalCronJobStart = new WithdrawalCronJobStart();
$withdrawalCronJobStart->setLink($link);
$withdrawalCronJobStart->setCronTabManager($cronTabManager);
$withdrawalCronJobStart->withdrawal();
