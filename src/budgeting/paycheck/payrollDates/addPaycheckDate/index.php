<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class AddPaycheckDateForm extends Base
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

  function addPaycheckDateForm(): string
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
      <form action="addPaycheckDate.php" method="post">
        <div>
          <label for="hoursWorked">Paycheck date:</label>
          <input type="datetime-local" name="paycheckDate" />
        </div>
        <br />
        <input type="submit" value="Add paycheck date" />
      </form>
    ';

    return $html;
  }

  function main(): string
  {
    $html = $this->displayCurrentBalance();
    $html .= $this->addPaycheckDateForm();

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$addPaycheckDateForm = new AddPaycheckDateForm();
$addPaycheckDateForm->setLink($link);
$addPaycheckDateForm->setTitle("Ephraim Becker - Budgeting - Add paycheck date form");
$addPaycheckDateForm->setLocalStyleSheet('css/style.css');
$addPaycheckDateForm->setLocalScript(NULL);
$addPaycheckDateForm->setHeader('Budgeting - Add paycheck date form');
$addPaycheckDateForm->setUrl($_SERVER['REQUEST_URI']);
$addPaycheckDateForm->setBody($addPaycheckDateForm->main());

$addPaycheckDateForm->html();
