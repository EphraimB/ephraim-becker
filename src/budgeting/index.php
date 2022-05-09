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
        $currentBalance = number_format(round(floatval($row['currentBalance']), 2), 2);
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

  function calculateAmount($amount, $type, $index, $currentBalance, $budget): float
  {
    if($type == 0) {
      if($index == 0) {
        $result = $currentBalance + $amount;
      } else {
        $result = $budget[$index - 1]["amount"] + $amount;
      }
    } else if($type == 1) {
      if($index == 0) {
        $result = $currentBalance - $amount;
      } else {
        $result = $budget[$index - 1]["amount"] - $amount;
      }
    }

    return $result;
  }

  function displayExpensesTable($currentBalance): string
  {
    $index = 0;
    $budget = array();

    $html = '
    <table>
        <tr>
          <th>Date</th>
          <th>Title</th>
          <th>Amount</th>
        </tr>';

    $sqlTwo = "SELECT 'Paycheck' AS title, (SELECT SUM(payPerHour) * SUM(hoursWorked) * SUM(daysPerWeek) * 2.167 - (SELECT SUM(taxAmount) FROM payrollTaxes WHERE fixed = 1) - (SELECT SUM(taxAmount) * (SELECT SUM(payPerHour) * SUM(hoursWorked) * SUM(daysPerWeek) * 2.167 FROM payroll) FROM payrollTaxes WHERE fixed = 0) FROM payroll) AS amount, YEAR(CURDATE()) AS beginYear, IF(MONTH(CURDATE()) >= MONTH(CURDATE()), IF(15 > DAY(CURDATE()), MONTH(CURDATE()), MONTH(DATE_ADD(CURDATE(), INTERVAL 1 MONTH))), CURDATE()) AS beginMonth, DAY(PayrollDate) AS beginDay, 0 AS frequency, 0 AS type FROM payrollDates UNION SELECT ExpenseTitle AS title, ExpensePrice AS amount, Year(ExpenseBeginDate) AS beginYear, IF(MONTH(CURDATE()) >= MONTH(ExpenseBeginDate), IF(DAY(CURDATE()) > DAY(ExpenseBeginDate), MONTH(DATE_ADD(CURDATE(), INTERVAL 1 MONTH)), MONTH(CURDATE())), MONTH(ExpenseBeginDate)) AS beginMonth, Day(ExpenseBeginDate) AS beginDay, FrequencyOfExpense AS frequency, 1 AS type FROM expenses WHERE ExpenseEndDate > CURRENT_DATE() OR ISNULL(ExpenseEndDate) UNION SELECT concat(MoneyOwedFor, ' payback to ', MoneyOwedRecipient) AS title, planAmount AS amount, YEAR(date) AS beginYear, IF(MONTH(CURDATE()) >= MONTH(date), IF(DAY(CURDATE()) > DAY(date), MONTH(DATE_ADD(CURDATE(), INTERVAL 1 MONTH)), MONTH(CURDATE())), MONTH(date)) AS beginMonth, DAY(date) AS beginDay, frequency AS frequency, 1 AS type FROM moneyOwed ORDER BY beginYear, beginMonth, beginDay";
    $sqlTwoResult = mysqli_query($this->getLink(), $sqlTwo);

    while($row = mysqli_fetch_array($sqlTwoResult)) {
      $title = $row['title'];
      $amount = floatval($row['amount']);
      $beginYear = $row['beginYear'];
      $beginMonth = $row['beginMonth'];
      $beginDay = $row['beginDay'];
      $frequency = $row['frequency'];
      $type = intval($row['type']);

      $currentMonth = date('m');
      $currentDay = date('d');

      for($i = $beginMonth; $i <= $beginMonth; $i++) {
        $balance = $this->calculateAmount($amount, $type, $index, $currentBalance, $budget);
        $index++;

        array_push($budget, array(
          "year" => $beginYear,
          "month" => $i,
          "day" => $beginDay,
          "title" => $title,
          "amount" => $balance,
          "type" => $type
        ));
      }
    }

    // var_dump($budget);

    for($j = 0; $j < count($budget); $j++) {
      $html .= '<tr>
          <td>' . $budget[$j]["month"] . '/' . $budget[$j]["day"] . '/' . $budget[$j]["year"] . '</td>
          <td>' . $budget[$j]["title"] . '</td>
          <td>$' . number_format(round($budget[$j]["amount"], 2), 2) . '</td>
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
