<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class Expenses extends Base
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

  function getCurrentBalance(): array
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

    return array(mysqli_num_rows($sqlResult), $currentBalance);
  }

  function displayCurrentBalance($currentBalance): string
  {
    $html = '<h2>Current balance: $' . $currentBalance . '</h2>';

    return $html;
  }

  function addExpenseButton(): string
  {
    $html = '
    <div class="row">
        <ul class="subNav">
          <li><a style="text-decoration: none;" href="addExpense/">+</a></li>
        </ul>
      </div>';

    return $html;
  }

  function showExpensesTable($transactions): string
  {
    $sqlTwo = "SELECT *, DATE_FORMAT(ExpenseBeginDate - INTERVAL timezoneOffset SECOND, '%m/%d/%Y %h:%i:%s %p') AS began, IFNULL(DATE_FORMAT(ExpenseEndDate - INTERVAL timezoneOffset SECOND, '%m/%d/%Y %h:%i:%s %p'), NULL) AS end FROM expenses";
    $sqlTwoResult = mysqli_query($this->getLink(), $sqlTwo);

      if($transactions > 0) {
        while($row = mysqli_fetch_array($sqlTwoResult)){
          $id = $row['ExpenseId'];
          $title = $row['ExpenseTitle'];
          $price = $row['ExpensePrice'];
          $began = $row['began'];
          $end = $row['end'];
          $frequency = $row['FrequencyOfExpense'];

          $html = '
          <div class="list">
            <div class="row">
              <p style="font-weight: bold;">' . $title . '&nbsp;</p>
              <br />
              <p>$' . $price . '&nbsp;</p>
              <p>' . $began . '&nbsp;</p>';

              if(!is_null($end)) {
                $html .= '<p>-' . $end . '&nbsp;</p>';
              }

              switch ($frequency) {
                case 0:
                  $html .= '<p>Monthly</p>';
                  break;
                case 1:
                  $html .= '<p>Weekly</p>';
                  break;
                case 2:
                  $html .= '<p>Daily</p>';
                  break;
                case 3:
                  $html .= '<p>One-time</p>';
                default:
                  $html .= '<p></p>';
                }

          $html .= '</div>
            <ul class="row actionButtons">
              <li><a class="edit" href="editExpense/index.php?id=' . $id . '">Edit Expense</a></li>
              <li><a class="delete" href="confirmation.php?id=' . $id . '">Delete Expense</a></li>
            </ul>
          </div>';
        }
    } else {
      $html .= '<p>No expenses!</p>';
    }

    return $html;
  }

  function main(): string
  {
    $transactions = $this->getCurrentBalance()[0];
    $currentBalance = $this->getCurrentBalance()[1];
    $html = $this->displayCurrentBalance($currentBalance);
    $html .= '<br />';
    $html .= $this->addExpenseButton();
    $html .= $this->showExpensesTable($transactions);

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$expenses = new Expenses();
$expenses->setLink($link);
$expenses->setTitle("Ephraim Becker - Budgeting - Expenses");
$expenses->setLocalStyleSheet('css/style.css');
$expenses->setLocalScript(NULL);
$expenses->setHeader('Budgeting - Expenses');
$expenses->setUrl($_SERVER['REQUEST_URI']);
$expenses->setBody($expenses->main());

$expenses->html();
