<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class PaycheckDatesInfo extends Base
{
  private $isAdmin;
  private $link;

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

  function showPaycheckInfo(): string
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
        $totalPayPerHour += intval($row['totalPayPerHour']);
      }
    }

    $html = '
      <p>I\'m working ' . $totalHours . ' hours for ' . $totalDaysPerWeek . ' days per week and get paid $' . $totalPayPerHour . ' an hour.</p>
    ';

    return $html;
  }

  function addPaycheckDateButton(): string
  {
    $html = '
    <div class="row">
        <ul class="subNav">
          <li><a style="text-decoration: none;" href="addPaycheckDate/">+</a></li>
        </ul>
      </div>';

    return $html;
  }

  function showPaycheckDates(): string
  {
    $html = '';

    $sqlTwo = "SELECT * FROM payrollDates";
    $sqlTwoResult = mysqli_query($this->getLink(), $sqlTwo);

    while($row = mysqli_fetch_array($sqlTwoResult)) {
      $id = $row['payrollDates_id'];
      $payrollDay = $row['PayrollDay'];
    }

      $html .= '
      <div id="payrollCalendarGridContainer">';

      for($i = 1; $i < 32; $i++) {
        if($i == intval($payrollDay)) {
          $html .= '<div class="payrollCalendarGridSelected"><a href="deletePayrollDay.php?id=' . $i . '">' . $i . '</a></div>';
        } else {
          $html .= '<div class="payrollCalendarGrid"><a href="addPayrollDay.php?day=' . $i . '">' . $i . '</a></div>';
        }
      }

      $html .= '</div>';

    return $html;
  }

  function main(): string
  {
    $html = $this->displayCurrentBalance();
    $html .= $this->showPaycheckInfo();
    $html .= $this->addPaycheckDateButton();
    $html .= '<br />';
    $html .= $this->showPaycheckDates();

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$paycheckDatesInfo = new PaycheckDatesInfo();
$paycheckDatesInfo->setLink($link);
$paycheckDatesInfo->setTitle("Ephraim Becker - Budgeting - Paycheck dates info");
$paycheckDatesInfo->setLocalStyleSheet('css/style.css');
$paycheckDatesInfo->setLocalScript(NULL);
$paycheckDatesInfo->setHeader('Budgeting - Paycheck dates info');
$paycheckDatesInfo->setUrl($_SERVER['REQUEST_URI']);
$paycheckDatesInfo->setBody($paycheckDatesInfo->main());

$paycheckDatesInfo->html();
