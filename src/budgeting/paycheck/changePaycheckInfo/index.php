<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class ChangePaycheckInfoForm extends Base
{
  private $isAdmin;
  private $link;

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

  function displayPaycheckInfoForm(): string
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

    $html = '
      <form action="changePaycheckInfo.php" method="post">
        <div>
          <label for="hoursWorked">Amount of hours currently working per day:</label>
          <input type="number" name="hoursWorked" value="' . $totalHours . '" />
        </div>
        <br />
        <div>
          <label for="daysPerWeek">Amount of days currently working per week:</label>
          <input type="number" name="daysPerWeek" value="' . $totalDaysPerWeek . '" />
        </div>
        <br />
        <div>
          <label for="payPerHour">Amount of pay per hour: $</label>
          <input type="number" name="payPerHour" value="' . $totalPayPerHour . '" />
        </div>
        <br />
        <input type="submit" />
      </form>
    ';

    return $html;
  }

  function main(): string
  {
    $html = $this->displayCurrentBalance();
    $html .= $this->displayPaycheckInfoForm();

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$changePaycheckInfoForm = new ChangePaycheckInfoForm();
$changePaycheckInfoForm->setLink($link);
$changePaycheckInfoForm->setTitle("Ephraim Becker - Budgeting - Change paycheck info form");
$changePaycheckInfoForm->setLocalStyleSheet('css/style.css');
$changePaycheckInfoForm->setLocalScript(NULL);
$changePaycheckInfoForm->setHeader('Budgeting - Change paycheck info form');
$changePaycheckInfoForm->setUrl($_SERVER['REQUEST_URI']);
$changePaycheckInfoForm->setBody($changePaycheckInfoForm->main());

$changePaycheckInfoForm->html();
