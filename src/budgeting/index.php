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

  function paycheckQuery($monthIncrement): string
  {
    $grossPay = "SELECT SUM(payPerHour) * SUM(hoursWorked) * SUM(daysPerWeek) * 2.167";
    $beginYear = "YEAR(CURDATE())";
    $beginMonth = "IF(PayrollDay > DAY(CURDATE()), MONTH(CURDATE() + INTERVAL " . $monthIncrement . " MONTH), MONTH(DATE_ADD(CURDATE(), INTERVAL " . $monthIncrement . "+1 MONTH)))";
    $caseQuery = "CASE WEEKDAY(concat(" . $beginYear . ", '-', " . $beginMonth . ", '-', payrollDay)) WHEN 5 THEN 1 WHEN 6 THEN 2 ELSE 0 END DAY))";

    $query = "SELECT 'Paycheck' AS title, (" . $grossPay . " - (SELECT SUM(taxAmount) FROM payrollTaxes WHERE fixed = 1) - (SELECT SUM(taxAmount) * (" . $grossPay . " FROM payroll) FROM payrollTaxes WHERE fixed = 0) FROM payroll) AS amount, " . $beginYear . " AS beginYear, " . $beginMonth . " AS beginMonth, IF(PayrollDay = 31, DAY(date_sub(LAST_DAY(concat(" . $beginYear . ", '-', " . $beginMonth . ", '-', 1)), INTERVAL " . $caseQuery . ", DAY(date_sub(concat(" . $beginYear . ", '-', " . $beginMonth . ", '-', payrollDay), INTERVAL " . $caseQuery . ") AS beginDay, 0 AS frequency, 0 AS type FROM payrollDates";

    return $query;
  }

  function expensesQuery($monthIncrement): string
  {
    $beginMonth = "IF(MONTH(CURDATE() + INTERVAL " . $monthIncrement . " MONTH) >= MONTH(ExpenseBeginDate + INTERVAL " . $monthIncrement . " MONTH), IF(DAY(CURDATE()) > DAY(ExpenseBeginDate), MONTH(DATE_ADD(CURDATE(), INTERVAL " . $monthIncrement . "+1 MONTH)), MONTH(CURDATE() + INTERVAL " . $monthIncrement . " MONTH)), MONTH(ExpenseBeginDate + INTERVAL " . $monthIncrement . " MONTH))";

    $query = "SELECT ExpenseTitle AS title, ExpensePrice AS amount, Year(ExpenseBeginDate) AS beginYear, " . $beginMonth . " AS beginMonth, Day(ExpenseBeginDate) AS beginDay, FrequencyOfExpense AS frequency, 1 AS type FROM expenses WHERE ExpenseEndDate > CURRENT_DATE() OR ISNULL(ExpenseEndDate)";
    var_dump($query);

    return $query;
  }

  function moneyOwnedQuery(): string
  {
    $query = "SELECT concat(MoneyOwedFor, ' payback to ', MoneyOwedRecipient) AS title, planAmount AS amount, YEAR(date) AS beginYear, IF(MONTH(CURDATE()) >= MONTH(date), IF(DAY(CURDATE()) > DAY(date), MONTH(DATE_ADD(CURDATE(), INTERVAL 1 MONTH)), MONTH(CURDATE())), MONTH(date)) AS beginMonth, DAY(date) AS beginDay, frequency AS frequency, 1 AS type FROM moneyOwed";

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

  function getWishlist(): string
  {
    $query = "SELECT Item AS title, Price AS price, 3 AS frequency, 1 AS type FROM WantToBuy";

    return $query;
  }

  function calculateWishlist($index, $lastIncomeIndex, $amount, $balance, $lastIncomeYear, $lastIncomeMonth, $lastIncomeDay, $currentBalance, $budget): array
  {
    $wishlistInBudget = false;

    $query = $this->getWishlist();
    $queryResult = mysqli_query($this->getLink(), $query);

    while($row = mysqli_fetch_array($queryResult)) {
      $title = $row['title'];
      $wishlistAmount = floatval($row['price']);
      $frequency = $row['frequency'];
      $type = intval($row['type']);

      if($wishlistAmount < $balance) {
        $balance = $this->calculateAmount($wishlistAmount, $type, $lastIncomeIndex, $currentBalance, $budget);
        $index++;
        array_splice($budget, $lastIncomeIndex, 0, array(array(
          "year" => $lastIncomeYear,
          "month" => $lastIncomeMonth,
          "day" => $lastIncomeDay,
          "title" => $title,
          "amount" => number_format(round($wishlistAmount, 2), 2),
          "balance" => $balance,
          "type" => $type
        )));

        $wishlistInBudget = true;
      }

      $index++;
    }

    return array($wishlistInBudget, $budget, $index);
  }

  function expensesTableQuery($monthIncrement): string
  {
    $query = $this->paycheckQuery($monthIncrement);
    $query .= " UNION ";
    $query .= $this->expensesQuery($monthIncrement);
    $query .= " UNION ";
    $query .= $this->moneyOwnedQuery();
    $query .= " UNION ";
    $query .= $this->foodExpensesQuery();
    $query .= " UNION ";
    $query .= $this->commuteExpensesQuery();
    $query .= "ORDER BY beginYear, beginMonth, beginDay";

    return $query;
  }

  function loopUntilSpecifiedDate($index, $endYear, $endMonth, $endDay, $budget, $currentBalance, $monthIncrement): array
  {
    $offset = count($budget);

    // If I can just loop the sql query

    // $title = $budget[$index]["title"];
    //
    // $amount = $budget[$index]["amount"];
    // $type = $budget[$index]["type"];
    // $year = intval($budget[$index]["year"]);
    // $month = intval($budget[$index]["month"]) + $monthIncrement;
    // $day = intval($budget[$index]["day"]);
    // $date = $year . "-" . $month . "-" . $day;
    //
    // $nextRowMonth = $budget[$index + 1]["month"] + $monthIncrement;
    //
    // if(($day == 31) || ($month == 2 && $day > 28)) {
    //   $date = $year . "-" . $month . "-" . 01;
    //   $day = intval(date("t", strtotime($date)));
    // }
    //
    // if($title == "Paycheck") {
    //   $date = $year . "-" . $month . "-" . $day;
    //
    //   if(date("w", strtotime($date)) == 6) {
    //     $day--;
    //   } else if(date("w", strtotime($date)) == 0) {
    //     $day = $day - 2;
    //   }
    // }
    //
    // // Todo: Condition to make sure that food and commute expenses are on the appropriate days
    // if($title == "Food expenses") {
    //   $query = $this->foodExpensesQuery($monthIncrement);
    //
    //   $queryResult = mysqli_query($this->getLink(), $query);
    //
    //   $row = mysqli_fetch_array($queryResult);
    //   var_dump($row);
    //     $title = $row['title'];
    //     $amount = floatval($row['amount']);
    //     $year = intval($row['beginYear']);
    //     $month = intval($row['beginMonth']);
    //     $day = intval($row['beginDay']);
    //     $frequency = intval($row['frequency']);
    //     $type = intval($row['type']);
    // }
    //
    // // while($month > $nextRowMonth) {
    // //   $offset++;
    // // }
    // //
    // // while($month < $nextRowMonth) {
    // //   $offset--;
    // // }

    $balance = $this->calculateAmount($amount, $type, $index, $currentBalance, $budget);

    array_splice($budget, $offset, 0, array(array(
      "year" => $year,
      "month" => $month,
      "day" => $day,
      "title" => $title,
      "amount" => $amount,
      "balance" => $balance,
      "type" => $type
    )));

    return $budget;
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

    for($l = 0; $l < 2; $l++) {
      $query = $this->expensesTableQuery($l);
      $queryResult = mysqli_query($this->getLink(), $query);

      while($row = mysqli_fetch_array($queryResult)) {
        $title = $row['title'];
        $amount = floatval($row['amount']);
        $beginYear = $row['beginYear'];
        $beginMonth = $row['beginMonth'];
        $beginDay = $row['beginDay'];
        $frequency = $row['frequency'];
        $type = intval($row['type']);

        $currentMonth = date('n');
        $currentDay = date('j');

        $balance = $this->calculateAmount($amount, $type, $index, $currentBalance, $budget);
        $index++;

        array_push($budget, array(
          "year" => $beginYear,
          "month" => $beginMonth,
          "day" => $beginDay,
          "title" => $title,
          "amount" => number_format(round($amount, 2), 2),
          "balance" => $balance,
          "type" => $type
        ));
      }
    }

    // $numBudgetRows = count($budget);
    //
    // for($m = 1; $m <= 3; $m++) {
    //   for($l = 0; $l < $numBudgetRows; $l++) {
    //     $budget = $this->loopUntilSpecifiedDate($l, 2022, 12, 31, $budget, $currentBalance, $m);
    //   }
    // }

    $lastIncomeYear = date('Y');
    $lastIncomeMonth = date('n');
    $lastIncomeDay = date('j');
    $lastIncomeIndex = 0;

    for($k = 0; $k < count($budget); $k++) {
      if($budget[$k]["type"] == 0) {
        $wishlistInBudget = $this->calculateWishlist($k-1, $lastIncomeIndex, $budget[$k-1]["amount"], $budget[$k-1]["balance"], $lastIncomeYear, $lastIncomeMonth, $lastIncomeDay, $currentBalance, $budget);
        $budget = $wishlistInBudget[1];

        $lastIncomeYear = $beginYear;
        $lastIncomeMonth = $beginMonth;
        $lastIncomeDay = $beginDay;
        $lastIncomeIndex = $k;
        $k = $wishlistInBudget[2];
      }
    }

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
