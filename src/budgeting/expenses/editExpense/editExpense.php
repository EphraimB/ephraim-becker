<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditExpense
{
  private $isAdmin;
  private $link;
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

  function getEndDate(): string
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

    $sql->execute();

    $sql->close();

    header("location: ../");
  }
}
$config = new Config();
$link = $config->connectToServer();

$editExpense = new EditExpense();
$editExpense->setLink($link);
$editExpense->setId(intval($_POST['id']));
$editExpense->setPrice(floatval($_POST['price']));
$editExpense->setExpenseTitle($_POST['expenseTitle']);
$editExpense->setStartDate($_POST['startDate']);
$editExpense->setTimezone($_POST['timezone']);
$editExpense->setTimezoneOffset(intval($_POST['timezoneOffset']));
$editExpense->setEndDate($_POST['endDate']);
$editExpense->setFrequency(intval($_POST['frequency']));

$editExpense->editExpense();
