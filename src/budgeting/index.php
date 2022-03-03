<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class Budgeting extends Base
{
  private $isAdmin;
  private $link;

  function __construct()
  {
    $this->setIsAdmin();

    if(!$this->getIsAdmin()) {
      header("location: ../");
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

  function displayCurrentBalance(): array
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

    return array($currentBalance, $html);
  }

  function displayActionButtons(): string
  {
    $html = '
    <div class="grid-container">
        <div style="background-color: green;" class="card">
          <a href="deposit/">
            <h3>Deposit</h3>
            <p>Click to make a deposit</p>
          </a>
        </div>
        <div style="background-color: yellow; color: black;" class="card">
          <a style="color: black;" href="withdrawal/">
            <h3>Withdrawal</h3>
            <p>Click to make a withdrawal</p>
          </a>
        </div>
        <div style="background-color: red;" class="card">
          <a href="expenses/">
            <h3>Expenses</h3>
            <p>Click to add an expense</p>
          </a>
      </div>
      <div style="background-color: green;" class="card">
        <a href="paycheck/">
          <h3>Paycheck</h3>
          <p>Click to add/change paycheck information</p>
        </a>
      </div>
      <div style="background-color: red;" class="card">
        <a href="moneyOwed/">
          <h3>Money owed</h3>
          <p>Click to add money owed</p>
        </a>
      </div>
    </div>';

    return $html;
  }

  function displayExpensesTable($currentBalance): string
  {
    $html = '
    <table>
        <tr>
          <th>Date</th>
          <th>Title</th>
          <th>Amount</th>
        </tr>';

    $sqlTwo = "SELECT *, Year(ExpenseBeginDate) AS ExpenseBeginYear, Month(ExpenseBeginDate) AS ExpenseBeginMonth, Day(ExpenseBeginDate) AS ExpenseBeginDay FROM expenses WHERE ExpenseEndDate > CURRENT_DATE() OR ISNULL(ExpenseEndDate)";
    $sqlTwoResult = mysqli_query($this->getLink(), $sqlTwo);

    // SELECT SUM(payPerHour) * SUM(hoursWorked) * SUM(daysPerWeek) * 2.167 AS amount FROM payroll UNION SELECT *, ExpensePrice AS amount, Year(ExpenseBeginDate) AS ExpenseBeginYear, Month(ExpenseBeginDate) AS ExpenseBeginMonth, Day(ExpenseBeginDate) AS ExpenseBeginDay FROM expenses WHERE ExpenseEndDate > CURRENT_DATE() OR ISNULL(ExpenseEndDate)

    while($row = mysqli_fetch_array($sqlTwoResult)) {
      $expenseTitle = $row['ExpenseTitle'];
      $expensePrice = $row['ExpensePrice'];
      $expenseBeginDate = $row['ExpenseBeginDate'];
      $expenseBeginYear = $row['ExpenseBeginYear'];
      $expenseBeginMonth = $row['ExpenseBeginMonth'];
      $expenseBeginDay = $row['ExpenseBeginDay'];
      $timezone = $row['timezone'];
      $timezoneOffset = $row['timezoneOffset'];
      $expenseEndDate = $row['ExpenseEndDate'];
      $frequencyOfExpense = $row['FrequencyOfExpense'];

      $currentMonth = date('m');
      $currentDay = date('d');

      if($currentMonth > $expenseBeginMonth) {
        $expenseBeginMonth = $currentMonth;
      }

      if($expenseBeginDay < $currentDay) {
        $expenseBeginMonth++;
      }

      $html .= '<tr>
            <td>' . $expenseBeginMonth . '/' . $expenseBeginDay . '/' . $expenseBeginYear . '</td>
            <td>' . $expenseTitle . '</td>
            <td>$' . $currentBalance - $expensePrice . '</td>
          </tr>';
    }

    $html .= '</table>';

    return $html;
  }

  function main(): string
  {
    $currentBalance = $this->displayCurrentBalance()[0];
    $html = $this->displayCurrentBalance()[1];
    $html .= $this->displayActionButtons();
    $html .= '<br />';
    $html .= $this->displayExpensesTable($currentBalance);

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$budgeting = new Budgeting();
$budgeting->setLink($link);
$budgeting->setTitle("Ephraim Becker - Budgeting");
$budgeting->setLocalStyleSheet('css/style.css');
$budgeting->setLocalScript(NULL);
$budgeting->setHeader('Budgeting');
$budgeting->setUrl($_SERVER['REQUEST_URI']);
$budgeting->setBody($budgeting->main());

$budgeting->html();
