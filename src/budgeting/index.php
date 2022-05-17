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
      <div style="background-color: gold;" class="card">
        <a style="color: black;" href="wishlist/">
          <h3>Wishlist</h3>
          <p>Click to set a goal on when you can buy what I want from my wishlist</p>
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
        $result = $budget[$index - 1]["balance"] + $amount;
      }
    } else if($type == 1) {
      if($index == 0) {
        $result = $currentBalance - $amount;
      } else {
        $result = $budget[$index - 1]["balance"] - $amount;
      }
    }

    return $result;
  }

  function paycheckQuery(): string
  {
    $grossPay = "SELECT SUM(payPerHour) * SUM(hoursWorked) * SUM(daysPerWeek) * 2.167";
    $beginYear = "YEAR(CURDATE())";
    $beginMonth = "IF(MONTH(CURDATE()) >= MONTH(CURDATE()), IF(PayrollDay > DAY(CURDATE()), MONTH(CURDATE()), MONTH(DATE_ADD(CURDATE(), INTERVAL 1 MONTH))), CURDATE())";

    $query = "SELECT 'Paycheck' AS title, (" . $grossPay . " - (SELECT SUM(taxAmount) FROM payrollTaxes WHERE fixed = 1) - (SELECT SUM(taxAmount) * (" . $grossPay . " FROM payroll) FROM payrollTaxes WHERE fixed = 0) FROM payroll) AS amount, " . $beginYear . " AS beginYear, " . $beginMonth . " AS beginMonth, IF(PayrollDay = 31, DAY(LAST_DAY(concat(" . $beginYear . ", '-', " . $beginMonth . ", '-', 01))), PayrollDay) AS beginDay, 0 AS frequency, 0 AS type FROM payrollDates";

    return $query;
  }

  function moneyOwnedQuery(): string
  {
    $query = "SELECT ExpenseTitle AS title, ExpensePrice AS amount, Year(ExpenseBeginDate) AS beginYear, IF(MONTH(CURDATE()) >= MONTH(ExpenseBeginDate), IF(DAY(CURDATE()) > DAY(ExpenseBeginDate), MONTH(DATE_ADD(CURDATE(), INTERVAL 1 MONTH)), MONTH(CURDATE())), MONTH(ExpenseBeginDate)) AS beginMonth, Day(ExpenseBeginDate) AS beginDay, FrequencyOfExpense AS frequency, 1 AS type FROM expenses WHERE ExpenseEndDate > CURRENT_DATE() OR ISNULL(ExpenseEndDate) UNION SELECT concat(MoneyOwedFor, ' payback to ', MoneyOwedRecipient) AS title, planAmount AS amount, YEAR(date) AS beginYear, IF(MONTH(CURDATE()) >= MONTH(date), IF(DAY(CURDATE()) > DAY(date), MONTH(DATE_ADD(CURDATE(), INTERVAL 1 MONTH)), MONTH(CURDATE())), MONTH(date)) AS beginMonth, DAY(date) AS beginDay, frequency AS frequency, 1 AS type FROM moneyOwed";

    return $query;
  }

  function foodExpensesQuery(): string
  {
    $caseQuery = "CASE WHEN WEEKDAY(CURDATE()) + 1 >= MealDayId
         THEN (CURDATE() + INTERVAL (6 - WEEKDAY(CURDATE())) DAY) + INTERVAL MealDayId DAY
         ELSE (CURDATE() + INTERVAL (0 - WEEKDAY(CURDATE())) DAY) + INTERVAL (MealDayId-1) DAY
    END";

    $query = "SELECT 'Food expenses' AS title, SUM(MealPrice) AS amount, (SELECT
    YEAR(" . $caseQuery . ")) AS beginYear, (SELECT
    MONTH(" . $caseQuery . ")) AS beginMonth, (SELECT
    DAY(" . $caseQuery . ")) AS beginDay, 1 AS frequency, 1 AS type FROM MealPlan GROUP BY MealDayId";

    return $query;
  }

  function commuteExpensesQuery(): string
  {
    $caseQuery = "CASE WHEN WEEKDAY(CURDATE()) + 1 >= CommuteDayId
         THEN (CURDATE() + INTERVAL (6 - WEEKDAY(CURDATE())) DAY) + INTERVAL CommuteDayId DAY
         ELSE (CURDATE() + INTERVAL (0 - WEEKDAY(CURDATE())) DAY) + INTERVAL (CommuteDayId-1) DAY
    END";

    $query = "SELECT 'Commute expenses' AS title, SUM(Price) AS amount, (SELECT
    YEAR(" . $caseQuery . ")) AS beginYear, (SELECT
    MONTH(" . $caseQuery . ")) AS beginMonth, (SELECT
    DAY(" . $caseQuery . ")) AS beginDay, 1 AS frequency, 1 AS type FROM CommutePlan GROUP BY CommuteDayId ";

    return $query;
  }

  function expensesTableQuery(): string
  {
    $query = $this->paycheckQuery();
    $query .= " UNION ";
    $query .= $this->moneyOwnedQuery();
    $query .= " UNION ";
    $query .= $this->foodExpensesQuery();
    $query .= " UNION ";
    $query .= $this->commuteExpensesQuery();
    $query .= "ORDER BY beginYear, beginMonth, beginDay";

    return $query;
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
          <th>Balance</th>
        </tr>';


    $query = $this->expensesTableQuery();
    $queryResult = mysqli_query($this->getLink(), $query);

    while($row = mysqli_fetch_array($queryResult)) {
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
          "amount" => number_format(round($amount, 2), 2),
          "balance" => $balance,
          "type" => $type
        ));
      }
    }

    // var_dump($budget);

    for($j = 0; $j < count($budget); $j++) {
      $html .= '<tr>
          <td>' . $budget[$j]["month"] . '/' . $budget[$j]["day"] . '/' . $budget[$j]["year"] . '</td>
          <td>' . $budget[$j]["title"] . '</td>
          <td>$' . $budget[$j]["amount"] . '</td>
          <td>$' . number_format(round($budget[$j]["balance"], 2), 2) . '</td>
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
