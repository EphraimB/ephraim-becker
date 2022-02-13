<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class EditExpenseForm extends Base
{
  private $isAdmin;
  private $link;
  private $id;

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

  function setId($id): void
  {
    $this->id = $id;
  }

  function getId(): int
  {
    return $this->id;
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

  function editExpenseForm(): string
  {
    $sqlTwo = $this->getLink()->prepare("SELECT *, DATE_FORMAT(ExpenseBeginDate - INTERVAL timezoneOffset SECOND, '%Y-%m-%dT%H:%i:%s') AS began, IFNULL(DATE_FORMAT(ExpenseEndDate - INTERVAL timezoneOffset SECOND, '%Y-%m-%dT%H:%i:%s'), NULL) AS end FROM expenses WHERE ExpenseId=?");
    $sqlTwo->bind_param("i", $id);

    $id = $this->getId();

    $sqlTwo->execute();

    $sqlTwoResult = $sqlTwo->get_result();

    while($row = mysqli_fetch_array($sqlTwoResult)){
      $title = $row['ExpenseTitle'];
      $price = $row['ExpensePrice'];
      $began = $row['began'];
      $timezone = $row['timezone'];
      $timezoneOffset = $row['timezoneOffset'];
      $end = $row['end'];
      $frequency = $row['FrequencyOfExpense'];
    }

    $sqlTwo->close();

    $html = '
    <form action="editExpense.php" method="post">
      <input type="hidden" name="id" value="' . $this->getId() . '" />
      <div class="row">
        <label for="title">Enter title of expense: </label>
        <input type="text" id="expenseTitle" name="expenseTitle" value="' . $title . '" required />
      </div>
      <br />
      <div class="row">
        <label for="price">Enter cost of expense (xx.xx): </label>
        &nbsp;
        $<input type="number" min="0" step="any" id="price" name="price" value="' . $price . '" required />
      </div>
      <br />
      <div class="row">
        <label for="startDate">Enter start date and time of expense: </label>
        <input type="datetime-local" id="startDate" name="startDate" value="' . $began . '" required />
        <input type="hidden" id="timezone" name="timezone" value="' . $timezone . '" />
        <input type="hidden" id="timezoneOffset" name="timezoneOffset" value="' . $timezoneOffset . '" />
      </div>
      <br />
      <div class="row">
        <label for="endDate">Enter end date and time of expense: </label>
        <input type="datetime-local" id="endDate" name="endDate" value="' . $end . '" />
        <div>
          <input type="checkbox" id="endDateExist" name="endDateExist" value="endDateExist" ';
          if(!is_null($end)) {
            $html .= 'checked';
           }
           $html .= '/>
          <label for="endDateExist">End expense date exist?</label>
        </div>
      </div>
      <br />
      <div class="row">
        <label for="frequency">Enter start date and time of expense: </label>
        <select name="frequency" id="frequency" required>
          <option value="0" ';

          if($frequency == 0) {
            $html .= 'selected';
          }

          $html .= '>Monthly</option>
          <option value="1" ';

          if($frequency == 1) {
            $html .= 'selected';
          }

          $html .= '>Weekly</option>
          <option value="2" ';

          if($frequency == 2) {
            $html .= 'selected';
           }

          $html .= '>Daily</option>
          <option value="3" ';

          if($frequency == 3) {
            $html .= 'selected';
          }

          $html .= '>One-time</option>
        </select>
      </div>
      <br />

      <input type="submit" id="submit" />
    </form>';

    return $html;
  }

  function main(): string
  {
    $currentBalance = $this->getCurrentBalance();
    $html = $this->displayCurrentBalance($currentBalance);
    $html .= $this->editExpenseForm();

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$editExpenseForm = new EditExpenseForm();
$editExpenseForm->setLink($link);
$editExpenseForm->setTitle("Ephraim Becker - Budgeting - Edit expense form");
$editExpenseForm->setLocalStyleSheet('css/style.css');
$editExpenseForm->setLocalScript('js/script.js');
$editExpenseForm->setHeader('Budgeting - Edit expense form');
$editExpenseForm->setUrl($_SERVER['REQUEST_URI']);
$editExpenseForm->setId(intval($_GET['id']));
$editExpenseForm->setBody($editExpenseForm->main());

$editExpenseForm->html();
