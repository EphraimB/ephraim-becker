<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class PaycheckInfo extends Base
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

  function displayActionButtons(): string
  {
    $html = '
    <div class="grid-container">
      <div style="background-color: yellow;" class="card">
        <a style="color: black;" href="changePaycheckInfo/">
          <h3>Change paycheck info</h3>
          <p>Click to change paycheck information</p>
        </a>
      </div>
    </div>';

    return $html;
  }

  function main(): string
  {
    $html = $this->displayCurrentBalance();
    $html .= $this->showPaycheckInfo();
    $html .= $this->displayActionButtons();

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$paycheckInfo = new PaycheckInfo();
$paycheckInfo->setLink($link);
$paycheckInfo->setTitle("Ephraim Becker - Budgeting - Paycheck info");
$paycheckInfo->setLocalStyleSheet('css/style.css');
$paycheckInfo->setLocalScript(NULL);
$paycheckInfo->setHeader('Budgeting - Paycheck info');
$paycheckInfo->setUrl($_SERVER['REQUEST_URI']);
$paycheckInfo->setBody($paycheckInfo->main());

$paycheckInfo->html();
