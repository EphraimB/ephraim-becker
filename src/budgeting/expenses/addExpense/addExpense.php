<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class AddExpense
{
  private $isAdmin;
  private $link;
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

  function addExpense(): void
  {
    $sql = $this->getLink()->prepare("INSERT INTO expenses (DateCreated, DateModified, ExpenseTitle, ExpensePrice, ExpenseBeginDate, timezone, timezoneOffset, ExpenseEndDate, FrequencyOfExpense)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $sql->bind_param('sssdssisi', $dateNow, $dateNow, $expenseTitle, $price, $beginDate, $timezone, $timezoneOffset, $endDate, $frequency);

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

    $sql->execute();

    $sql->close();

    header("location: ../");
  }
}
$config = new Config();
$link = $config->connectToServer();

$addExpense = new AddExpense();
$addExpense->setLink($link);
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
