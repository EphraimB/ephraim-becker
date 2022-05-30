<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditMoneyOwed
{
  private $isAdmin;
  private $link;
  private $cronTabManager;
  private $id;
  private $recipient;
  private $for;
  private $amount;
  private $planAmount;
  private $frequency;
  private $date;
  private $timezone;
  private $timezoneOffset;

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

  function setRecipient($recipient): void
  {
    $this->recipient = $recipient;
  }

  function getRecipient(): string
  {
    return $this->recipient;
  }

  function setFor($for): void
  {
    $this->for = $for;
  }

  function getFor(): string
  {
    return $this->for;
  }

  function setAmount($amount): void
  {
    $this->amount = $amount;
  }

  function getAmount(): float
  {
    return $this->amount;
  }

  function setPlanAmount($planAmount): void
  {
    $this->planAmount = $planAmount;
  }

  function getPlanAmount(): float
  {
    return $this->planAmount;
  }

  function setFrequency($frequency): void
  {
    $this->frequency = $frequency;
  }

  function getFrequency(): int
  {
    return $this->frequency;
  }

  function setDate($date): void
  {
    $this->date = $date;
  }

  function getDate(): String
  {
    return $this->date;
  }

  function setTimezone($timezone): void {
    $this->timezone = $timezone;
  }

  function getTimezone(): String
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

  function getCronJobId(): int
  {
    $sql = $this->getLink()->prepare("SELECT CronJobId FROM moneyOwed WHERE moneyOwed_Id=?");
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

  function editMoneyOwed(): void
  {
    $sql = $this->getLink()->prepare("UPDATE moneyOwed SET DateModified=?, MoneyOwedRecipient=?, MoneyOwedFor=?, MoneyOwedAmount=?, planAmount=?, frequency=?, date=?, timezone=?, timezoneOffset=? WHERE moneyOwed_id=?");
    $sql->bind_param('sssddissii', $dateNow, $recipient, $for, $amount, $planAmount, $frequency, $date, $timezone, $timezoneOffset, $id);

    $id = $this->getId();

    $dateNow = date("Y-m-d H:i:s");
    $recipient = $this->getRecipient();
    $for = $this->getFor();
    $amount = $this->getAmount();
    $planAmount = $this->getPlanAmount();
    $frequency = $this->getFrequency();
    $timezone = $this->getTimezone();
    $timezoneOffset = $this->getTimezoneOffset();

    $date = date('Y-m-d H:i:s', strtotime($this->getDate()) + $timezoneOffset);

    $uniqueId = $this->getCronJobUniqueId();
    if(date("n", strtotime($date)) > date("n")) {
      $command = intval(date("i", strtotime($date))) . ' ' . intval(date("H", strtotime($date))) . ' 1 ' . date("n", strtotime($date)) . ' * /usr/local/bin/php /home/s8gphl6pjes9/public_html/budgeting/cron/withdrawalCronJobStart.php withdrawalAmount=' . $planAmount . ' withdrawalDescription=loan\ payback\ for\ ' . $for . ' date=' . $date . ' id=' . $uniqueId;
    } else {
      $command = intval(date("i", strtotime($date))) . ' ' . intval(date("H", strtotime($date))) . ' ' . date("j", strtotime($date)) . ' * * /usr/local/bin/php /home/s8gphl6pjes9/public_html/budgeting/cron/withdrawalCronJob.php withdrawalAmount=' . $planAmount . ' withdrawalDescription=loan\ payback\ for\ ' . $for . ' id=' . $uniqueId;
    }
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

$editMoneyOwed = new EditMoneyOwed();
$editMoneyOwed->setLink($link);
$editMoneyOwed->setCronTabManager($cronTabManager);
$editMoneyOwed->setId(intval($_POST['id']));
$editMoneyOwed->setRecipient($_POST['recipient']);
$editMoneyOwed->setFor($_POST['for']);
$editMoneyOwed->setAmount(floatval($_POST['amount']));
$editMoneyOwed->setPlanAmount(floatval($_POST['planAmount']));
$editMoneyOwed->setFrequency(1);
$editMoneyOwed->setDate($_POST['date']);
$editMoneyOwed->setTimezone($_POST['timezone']);
$editMoneyOwed->setTimezoneOffset(intval($_POST['timezoneOffset']));
$editMoneyOwed->editMoneyOwed();
