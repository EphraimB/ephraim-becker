<?php
declare(strict_types=1);

session_start();

$home = getenv('HOME');

require_once($home . '/config.php');
require($home . '/vendor/autoload.php');

class GenerateSpreadsheet
{
  private $link;
  private $depositAmount;
  private $depositDescription;

  function __construct()
  {
    $isCLI = (php_sapi_name() == 'cli');

    if(!$isCLI) {
      die("cannot run!");
    } else {
      parse_str(implode('&', array_slice($_SERVER['argv'], 1)), $_GET);
    }
  }

  function setLink($link)
  {
    $this->link = $link;
  }

  function getLink()
  {
    return $this->link;
  }

  /**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
  function getClient()
  {
      $home = getenv('HOME');
      $client = new Google\Client();

      $client->setAuthConfig($home . '/credentials.json');
      $client->setApplicationName("Ephraim Becker");
      $client->setScopes(['https://www.googleapis.com/auth/spreadsheets']);

      return $client;
  }

  function getSpreadsheetInfo(): array
  {
    $client = $this->getClient();

    $service = new Google_Service_Sheets($client);

    // The ID of the spreadsheet to update.
    $spreadsheetId = '1aQUD3MkEMHnwN069EZW9dwsW6OVicOQ89P40nKVQwhI';

    // The A1 notation of the values to update.
    $range = 'A2';

    $values = [
      [
        // Cell values ...
        "Successful"
      ],
      // Additional rows ...
    ];

    $requestBody = new Google_Service_Sheets_ValueRange([
      'values' => $values
    ]);

    $params = [
      'valueInputOption' => 2
    ];

    $response = $service->spreadsheets_values->update($spreadsheetId, $range, $requestBody, $params);
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

    return $query;
  }

  function moneyOwnedQuery($monthIncrement): string
  {
    $monthIncrementQuery = "+ INTERVAL " . $monthIncrement . " MONTH";
    $beginMonth = "IF(MONTH(CURDATE() " . $monthIncrementQuery . ") >= MONTH(date), IF(DAY(CURDATE() " . $monthIncrementQuery . ") > DAY(date" . $monthIncrementQuery . "), MONTH(DATE_ADD(CURDATE(), INTERVAL " . $monthIncrement . "+1 MONTH)), MONTH(CURDATE() " . $monthIncrementQuery . ")), MONTH(date" . $monthIncrementQuery . "))";

    $query = "SELECT concat(MoneyOwedFor, ' payback to ', MoneyOwedRecipient) AS title, planAmount AS amount, YEAR(date) AS beginYear, " . $beginMonth . " AS beginMonth, DAY(date) AS beginDay, frequency AS frequency, 1 AS type FROM moneyOwed";

    return $query;
  }

  function foodExpensesQuery($weekIncrement): string
  {
    $caseQuery = "CASE WHEN WEEKDAY(CURDATE() + INTERVAL " . $weekIncrement . " WEEK) + 1 >= MealDayId
         THEN (CURDATE() + INTERVAL " . $weekIncrement . " WEEK + INTERVAL (6 - WEEKDAY(CURDATE() + INTERVAL " . $weekIncrement . " WEEK)) DAY) + INTERVAL MealDayId DAY
         ELSE (CURDATE() + INTERVAL " . $weekIncrement . " WEEK + INTERVAL (0 - WEEKDAY(CURDATE() + INTERVAL " . $weekIncrement . " WEEK)) DAY) + INTERVAL (MealDayId-1) DAY
    END";

    $query = "SELECT 'Food expenses' AS title, SUM(MealPrice) AS amount, (SELECT
    YEAR(" . $caseQuery . ")) AS beginYear, (SELECT
    MONTH(" . $caseQuery . ")) AS beginMonth, (SELECT
    DAY(" . $caseQuery . ")) AS beginDay, 1 AS frequency, 1 AS type FROM MealPlan GROUP BY MealDayId";

    return $query;
  }

  function commuteExpensesQuery($weekIncrement): string
  {
    $caseQuery = "CASE WHEN WEEKDAY(CURDATE() + INTERVAL " . $weekIncrement . " WEEK) + 1 >= CommuteDayId
         THEN (CURDATE() + INTERVAL " . $weekIncrement . " WEEK + INTERVAL (6 - WEEKDAY(CURDATE() + INTERVAL " . $weekIncrement . " WEEK)) DAY) + INTERVAL CommuteDayId DAY
         ELSE (CURDATE() + INTERVAL " . $weekIncrement . " WEEK + INTERVAL (0 - WEEKDAY(CURDATE() + INTERVAL " . $weekIncrement . " WEEK)) DAY) + INTERVAL (CommuteDayId-1) DAY
    END";

    $query = "SELECT 'Commute expenses' AS title, SUM(Price) AS amount, (SELECT
    YEAR(" . $caseQuery . ")) AS beginYear, (SELECT
    MONTH(" . $caseQuery . ")) AS beginMonth, (SELECT
    DAY(" . $caseQuery . ")) AS beginDay, 1 AS frequency, 1 AS type FROM CommutePlan GROUP BY CommuteDayId";

    return $query;
  }

  function getWishlist(): string
  {
    $query = "SELECT WantToBuyId AS id, Item AS title, Price AS price, 3 AS frequency, 1 AS type FROM WantToBuy WHERE Finished = 0";

    return $query;
  }

  function calculateWishlist($index, $lastIncomeIndex, $amount, $balance, $lastIncomeYear, $lastIncomeMonth, $lastIncomeDay, $currentBalance, $budget): array
  {
    $query = $this->getWishlist();
    $queryResult = mysqli_query($this->getLink(), $query);

    while($row = mysqli_fetch_array($queryResult)) {
      $id = $row['id'];
      $title = $row['title'];
      $wishlistAmount = floatval($row['price']);
      $frequency = $row['frequency'];
      $type = intval($row['type']);

      if($wishlistAmount < $balance) {
        $balance = $this->calculateAmount($wishlistAmount, $type, $lastIncomeIndex+1, $currentBalance, $budget);
        array_splice($budget, $lastIncomeIndex+1, 0, array(array(
          "year" => $lastIncomeYear,
          "month" => $lastIncomeMonth,
          "day" => $lastIncomeDay,
          "title" => $title,
          "amount" => number_format(round($wishlistAmount, 2), 2),
          "balance" => $balance,
          "type" => $type
        )));

        $sql = $this->getLink()->prepare("UPDATE WantToBuy SET Finished=? WHERE WantToBuyId=?");
        $sql->bind_param('ii', $flag, $id);

        $flag = 1;

        $sql->execute();

        $index++;
      }
    }

    $index++;

    return array($index, $budget);
  }

  function loopWeeksUntilMonths($increment): string
  {
    $query = $this->foodExpensesQuery($increment);
    $query .= " UNION ";
    $query .= $this->commuteExpensesQuery($increment);
    $query .= " ORDER BY beginYear, beginMonth, beginDay";

    return $query;
  }

  function expensesTableQuery($increment): string
  {
    $query = $this->paycheckQuery($increment);
    $query .= " UNION ";
    $query .= $this->expensesQuery($increment);
    $query .= " UNION ";
    $query .= $this->moneyOwnedQuery($increment);
    $query .= " ORDER BY beginYear, beginMonth, beginDay";

    return $query;
  }

  function getSortArrayByDate($budget)
  {
    usort($budget, array($this, 'sortArrayByDate'));

    return $budget;
  }

  private function sortArrayByDate($a, $b)
  {
    $t1DateString = $a["year"] . '-' . $a["month"] . '-' . $a["day"];
    $t2DateString = $b["year"] . '-' . $b["month"] . '-' . $b["day"];

    $t1 = strtotime($t1DateString);
    $t2 = strtotime($t2DateString);

    return $t1 - $t2;
  }

  function generateSpreadsheet(): void
  {
    $currentBalance = $this->getCurrentBalance();
    $weeklyIndex = 0;
    $budget = array();

    $client = $this->getClient();

    $service = new Google_Service_Sheets($client);

    // The ID of the spreadsheet to update.
    $spreadsheetId = '1aQUD3MkEMHnwN069EZW9dwsW6OVicOQ89P40nKVQwhI';

    for($l = 0; $l < 3; $l++) {
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

        array_push($budget, array(
          "year" => $beginYear,
          "month" => $beginMonth,
          "day" => $beginDay,
          "title" => $title,
          "amount" => number_format(round($amount, 2), 2),
          "balance" => 0,
          "type" => $type
        ));
      }

      for($j = 0; $j < 4; $j++) {
        $queryTwo = $this->loopWeeksUntilMonths($weeklyIndex);
        $queryTwoResult = mysqli_query($this->getLink(), $queryTwo);

        while($rowTwo = mysqli_fetch_array($queryTwoResult)) {
          $title = $rowTwo['title'];
          $amount = floatval($rowTwo['amount']);
          $beginYear = $rowTwo['beginYear'];
          $beginMonth = $rowTwo['beginMonth'];
          $beginDay = $rowTwo['beginDay'];
          $frequency = $rowTwo['frequency'];
          $type = intval($rowTwo['type']);

          array_push($budget, array(
            "year" => $beginYear,
            "month" => $beginMonth,
            "day" => $beginDay,
            "title" => $title,
            "amount" => number_format(round($amount, 2), 2),
            "balance" => 0,
            "type" => $type
          ));
        }

        $weeklyIndex++;
      }
    }

    $budget = $this->getSortArrayByDate($budget);

    for($m = 0; $m < count($budget); $m++) {
      $balance = $this->calculateAmount($budget[$m]["amount"], $budget[$m]["type"], $m, $currentBalance, $budget);

      $budget[$m]["balance"] = $balance;
    }

    $lastIncomeYear = date('Y');
    $lastIncomeMonth = date('n');
    $lastIncomeDay = date('j');
    $lastIncomeIndex = 0;

    for($k = 0; $k < count($budget); $k++) {
      if($budget[$k]["type"] == 0) {
        $wishlistInBudget = $this->calculateWishlist($k-1, $lastIncomeIndex, $budget[$k-1]["amount"], $budget[$k-1]["balance"], $lastIncomeYear, $lastIncomeMonth, $lastIncomeDay, $currentBalance, $budget);
        $budget = $wishlistInBudget[1];

        $lastIncomeYear = $budget[$k]["year"];
        $lastIncomeMonth = $budget[$k]["month"];
        $lastIncomeDay = $budget[$k]["day"];
        $lastIncomeIndex = $k;

        $k = $wishlistInBudget[0];
      }
    }

    $budget = $this->getSortArrayByDate($budget);

    for($m = 0; $m < count($budget); $m++) {
      $balance = $this->calculateAmount($budget[$m]["amount"], $budget[$m]["type"], $m, $currentBalance, $budget);

      $budget[$m]["balance"] = $balance;
    }

    $sql = $this->getLink()->prepare("UPDATE WantToBuy SET Finished=?");
    $sql->bind_param('i', $flag);

    $flag = 0;

    $sql->execute();

    $values = [
      [
        "Budget", ''
      ],
      [

      ],
      [
        'Date', 'Event', 'Amount', 'Balance'
      ]
    ];

    $valuesTwo = array();
    array_push($valuesTwo, array(date("n/j/Y"), 'Now', 'N/A', '$' . $this->getCurrentBalance()));

    $valuesThree = array();
    $cell = 4;

    for($j = 0; $j < count($budget); $j++) {
      if($budget[$j]['type'] == 1) {
        array_push($valuesThree, array($budget[$j]["month"] . '/' . $budget[$j]["day"] . '/' . $budget[$j]["year"], $budget[$j]["title"], '$' . $budget[$j]["amount"], ('=D' . strval($cell)) . '-C' . $cell+1));
      } else {
        array_push($valuesThree, array($budget[$j]["month"] . '/' . $budget[$j]["day"] . '/' . $budget[$j]["year"], $budget[$j]["title"], $budget[$j]["amount"], ('=D' . strval($cell)) . '+C' . $cell+1));
      }

      $cell++;
    }

    $data = [];
    $data[] = new Google_Service_Sheets_ValueRange([
        'range' => 'A1',
        'values' => $values
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
        'range' => 'A4',
        'values' => $valuesTwo
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'A5',
      'values' => $valuesThree
    ]);

    // Additional ranges to update ...
    $body = new Google_Service_Sheets_BatchUpdateValuesRequest([
        'valueInputOption' => 2,
        'data' => $data
    ]);

    $response = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);
  }
}
$config = new Config();
$link = $config->connectToServer();

$generateSpreadsheet = new GenerateSpreadsheet();
$generateSpreadsheet->setLink($link);
$generateSpreadsheet->generateSpreadsheet();
