<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class EditPaycheckDateForm extends Base
{
  private $isAdmin;
  private $link;
  private $id;

  function __construct()
  {
    $this->setIsAdmin();

    if(!$this->getIsAdmin()) {
      header("location: ../../../");
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

  function displayCurrentBalance(): string
  {
    $sql = "SELECT (SELECT SUM(DepositAmount) from deposits) - (SELECT SUM(WithdrawalAmount) FROM withdrawals) AS currentBalance";
    $sqlResult = mysqli_query($this->getLink(), $sql);

    if(mysqli_num_rows($sqlResult) > 0) {
      while($row = mysqli_fetch_array($sqlResult)){
        $currentBalance = floatval($row['currentBalance']);
      }
    }

    if(is_null($currentBalance)) {
      $currentBalance = 0.00;
    }

    $html = '<h2>Current balance: $' . $currentBalance . '</h2>';

    return $html;
  }

  function changePaycheckDateForm(): string
  {
    $totalHours = 0;
    $totalDaysPerWeek = 0;
    $totalPayPerHour = 0;

    $sql = "SELECT SUM(hoursWorked) AS totalHours, SUM(daysPerWeek) AS totalDaysPerWeek, SUM(payPerHour) AS totalPayPerHour FROM payroll";
    $sqlResult = mysqli_query($this->getLink(), $sql);

    if(mysqli_num_rows($sqlResult) > 0) {
      while($row = mysqli_fetch_array($sqlResult)) {
        $totalHours += intval($row['totalHours']);
        $totalDaysPerWeek += intval($row['totalDaysPerWeek']);
        $totalPayPerHour += floatval($row['totalPayPerHour']);
      }
    }

    $sqlTwo = $this->getLink()->prepare("SELECT * FROM payrollDates WHERE payrollDates_id=?");
    $sqlTwo->bind_param('i', $id);

    $id = $this->getId();

    $sqlTwo->execute();

    $sqlTwoResult = $sqlTwo->get_result();

    while($rowTwo = mysqli_fetch_array($sqlTwoResult)) {
      $payrollDate = date("Y-m-d", strtotime($rowTwo['PayrollDate'])) . '\T' . date("H:i:s", strtotime($rowTwo['PayrollDate']));
    }

    $html = '
      <form action="editPaycheckDate.php" method="post">
        <div>
          <label for="hoursWorked">Paycheck date:</label>
          <input type="datetime-local" name="paycheckDate" value="' . date($payrollDate) . '" />
        </div>
        <br />
        <input type="hidden" name="id" value="' . $this->getId() . '" />
        <input type="submit" value="Change paycheck date" />
      </form>
    ';

    return $html;
  }

  function main(): string
  {
    $html = $this->displayCurrentBalance();
    $html .= $this->changePaycheckDateForm();

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$editPaycheckDateForm = new EditPaycheckDateForm();
$editPaycheckDateForm->setLink($link);
$editPaycheckDateForm->setId(intval($_GET['id']));
$editPaycheckDateForm->setTitle("Ephraim Becker - Budgeting - Change paycheck date form");
$editPaycheckDateForm->setLocalStyleSheet('css/style.css');
$editPaycheckDateForm->setLocalScript(NULL);
$editPaycheckDateForm->setHeader('Budgeting - Change paycheck date form');
$editPaycheckDateForm->setUrl($_SERVER['REQUEST_URI']);
$editPaycheckDateForm->setBody($editPaycheckDateForm->main());

$editPaycheckDateForm->html();
