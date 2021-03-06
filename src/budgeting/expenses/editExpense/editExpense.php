<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditExpense
{
  private $isAdmin;
  private $link;
  private $cronTabManager;
  private $id;
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

  function setId($id): void
  {
    $this->id = $id;
  }

  function getId(): int
  {
    return $this->id;
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

  function getCronJobId(): int
  {
    $sql = $this->getLink()->prepare("SELECT CronJobId FROM expenses WHERE ExpenseId=?");
    $sql->bind_param('i', $id);

    $id = $this->getId();

    $sql->execute();

    $sqlResult = $sql->get_result();

    while($row = mysqli_fetch_array($sqlResult)) {
      $id = $row['CronJobId'];
    }

    return $id;
  }

  function getCronJobUniqueId(): string
  {
    $sql = $this->getLink()->prepare("SELECT UniqueId FROM CronJobs WHERE CronJobId=?");
    $sql->bind_param('i', $id);

    $id = $this->getCronJobId();

    $sql->execute();

    $sqlResult = $sql->get_result();

    while($row = mysqli_fetch_array($sqlResult)) {
      $uniqueId = $row['UniqueId'];
    }

    return $uniqueId;
  }

  function updateCronJobInDB($command): void
  {
     $sql = $this->getLink()->prepare("UPDATE CronJobs SET Command=?, DateModified=? WHERE CronJobId=?");
     $sql->bind_param('ssi', $command, $dateNow, $cronJobId);

     $dateNow = date("Y-m-d H:i:s");
     $cronJobId = $this->getCronJobId();

     $sql->execute();
  }

  function editExpense(): void
  {
    $sql = $this->getLink()->prepare("UPDATE expenses SET DateModified=?, ExpenseTitle=?, ExpensePrice=?, ExpenseBeginDate=?, timezone=?, timezoneOffset=?, ExpenseEndDate=?, FrequencyOfExpense=? WHERE ExpenseId=?");
    $sql->bind_param('ssdssisii', $dateNow, $expenseTitle, $price, $beginDate, $timezone, $timezoneOffset, $endDate, $frequency, $id);

    $id = $this->getId();

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

    $uniqueId = $this->getCronJobUniqueId();
    $command = intval(date("i", strtotime($beginDate))) . ' ' . intval(date("H", strtotime($beginDate))) . ' ' . date("j", strtotime($beginDate)) . ' * * /usr/local/bin/php /home/s8gphl6pjes9/public_html/budgeting/cron/withdrawalCronJob.php withdrawalAmount=' . $price . ' withdrawalDescription=Expenses id=' . $uniqueId;
    $this->updateCronJobInDB($command);

    $sql->execute();

    $crontab = $this->getCronTabManager();
    $crontab->remove_cronjob('/' . $uniqueId . '/');
    $crontab->append_cronjob($command);

    $sql->close();

    header("location: ../");
  }
}
$config = new Config();
$link = $config->connectToServer();
$cronTabManager = $config->connectToCron();

$editExpense = new EditExpense();
$editExpense->setLink($link);
$editExpense->setCronTabManager($cronTabManager);
$editExpense->setId(intval($_POST['id']));
$editExpense->setPrice(floatval($_POST['price']));
$editExpense->setExpenseTitle($_POST['expenseTitle']);
$editExpense->setStartDate($_POST['startDate']);
$editExpense->setTimezone($_POST['timezone']);
$editExpense->setTimezoneOffset(intval($_POST['timezoneOffset']));

if(isset($_POST['endDateExist'])) {
  $editExpense->setEndDate($_POST['endDate']);
} else {
  $editExpense->setEndDate(NULL);
}

$editExpense->setFrequency(1);

$editExpense->editExpense();
