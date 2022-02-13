<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class AddExpenseForm extends Base
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

  function getCurrentBalance(): float
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

    return $currentBalance;
  }

  function displayCurrentBalance($currentBalance): string
  {
    $html = '<h2>Current balance: $' . $currentBalance . '</h2>';

    return $html;
  }

  function addExpenseForm(): string
  {
    $html = '
        <form action="addExpense.php" method="post">
      <div class="row">
        <label for="title">Enter title of expense: </label>
        <input type="text" id="expenseTitle" name="expenseTitle" required />
      </div>
      <br />
      <div class="row">
        <label for="price">Enter cost of expense (xxx.xx): </label>
        &nbsp;
        $<input type="number" min="0" step="any" id="price" name="price" required />
      </div>
      <br />
      <div class="row">
        <label for="startDate">Enter start date and time of expense: </label>
        <input type="datetime-local" id="startDate" name="startDate" required />
        <input type="hidden" id="timezone" name="timezone" />
        <input type="hidden" id="timezoneOffset" name="timezoneOffset" />
      </div>
      <br />
      <div class="row">
        <label for="endDate">Enter end date and time of expense: </label>
        <input type="datetime-local" id="endDate" name="endDate" disabled />
        <div>
          <input type="checkbox" id="endDateExist" name="endDateExist" value="endDateExist" />
          <label for="endDateExist">End expense date exist?</label>
        </div>
      </div>
      <br />
      <div class="row">
        <label for="frequency">Enter start date and time of expense: </label>
        <select name="frequency" id="frequency" required>
          <option value="monthly">Monthly</option>
          <option value="weekly">Weekly</option>
          <option value="Daily">Daily</option>
          <option value="onetime">One-time</option>
        </select>
      </div>
      <br />

      <input type="submit" id="submit" disabled="disabled" />
    </form>';

    return $html;
  }

  function main(): string
  {
    $currentBalance = $this->getCurrentBalance();
    $html = $this->displayCurrentBalance($currentBalance);
    $html .= $this->addExpenseForm();

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$addExpenseForm = new AddExpenseForm();
$addExpenseForm->setLink($link);
$addExpenseForm->setTitle("Ephraim Becker - Budgeting - Add expense form");
$addExpenseForm->setLocalStyleSheet('css/style.css');
$addExpenseForm->setLocalScript('js/script.js');
$addExpenseForm->setHeader('Budgeting - Add expense form');
$addExpenseForm->setUrl($_SERVER['REQUEST_URI']);
$addExpenseForm->setBody($addExpenseForm->main());

$addExpenseForm->html();
