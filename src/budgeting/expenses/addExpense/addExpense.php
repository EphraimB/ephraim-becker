<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class AddExpense
{
  private $isAdmin;
  private $link;
  private $cronTabManager;
  private $price;
  private $expenseTitle;
  private $startDate;
  private $timezone;
  private $timezoneOffset;
  private $endDate;
  private $frequency;

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

  function setPrice($price): void
  {
    $this->price = $price;
  }

  function getPrice(): float
  {
    return $this->price;
  }

  function setExpenseTitle($expenseTitle): void
  {
    $this->expenseTitle = $expenseTitle;
  }

  function getExpenseTitle(): string
  {
    return $this->expenseTitle;
  }

  function setStartDate($startDate): void
  {
    $this->startDate = $startDate;
  }

  function getStartDate(): string
  {
    return $this->startDate;
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

  function setEndDate($endDate): void
  {
    $this->endDate = $endDate;
  }

  function getEndDate()
  {
    return $this->endDate;
  }

  function setFrequency($frequency): void
  {
    $this->frequency = $frequency;
  }

  function getFrequency(): int
  {
    return $this->frequency;
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

  function addExpense(): void
  {
    $sql = $this->getLink()->prepare("INSERT INTO expenses (CronJobId, DateCreated, DateModified, ExpenseTitle, ExpensePrice, ExpenseBeginDate, timezone, timezoneOffset, ExpenseEndDate, FrequencyOfExpense)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $sql->bind_param('isssdssisi', $cronJobId, $dateNow, $dateNow, $expenseTitle, $price, $beginDate, $timezone, $timezoneOffset, $endDate, $frequency);

    $dateNow = date("Y-m-d H:i:s");
    $price = $this->getPrice();

    $expenseTitle = $this->getExpenseTitle();

    $beginDate = date('Y-m-d H:i:s', strtotime($this->getStartDate()) + $this->getTimezoneOffset());

    if(!isset($_POST['endDateExist'])) {
      $endDate = NULL;
    } else {
      $endDate = date('Y-m-d H:i:s', strtotime($this->getEndDate()) + $this->getTimezoneOffset());
    }

    $timezone = $this->getTimezone();
    $timezoneOffset = $this->getTimezoneOffset();

    $frequency = $this->getFrequency();

    $uniqueId = uniqid();
    $command = intval(date("i", strtotime($beginDate))) . ' ' . intval(date("H", strtotime($beginDate))) . ' ' . date("j", strtotime($beginDate)) . ' ' . date("n", strtotime($beginDate)) . ' * /usr/local/bin/php /home/s8gphl6pjes9/public_html/budgeting/cron/withdrawalCronJob.php withdrawalAmount=' . $price . ' withdrawalDescription=Expenses id=' . $uniqueId;
    $cronJobId = $this->addCronJobToDB($uniqueId, $command);

    $crontab = $this->getCronTabManager();
    $crontab->append_cronjob($command);

    $sql->execute();

    $sql->close();

    header("location: ../");
  }
}
$config = new Config();
$link = $config->connectToServer();
$cronTabManager = $config->connectToCron();

$addExpense = new AddExpense();
$addExpense->setLink($link);
$addExpense->setCronTabManager($cronTabManager);
$addExpense->setPrice(floatval($_POST['price']));
$addExpense->setExpenseTitle($_POST['expenseTitle']);
$addExpense->setStartDate($_POST['startDate']);
$addExpense->setTimezone($_POST['timezone']);
$addExpense->setTimezoneOffset(intval($_POST['timezoneOffset']));

if(isset($_POST['endDateExists'])) {
  $addExpense->setEndDate($_POST['endDate']);
} else {
  $addExpense->setEndDate(NULL);
}

$addExpense->setFrequency(1);

$addExpense->addExpense();
