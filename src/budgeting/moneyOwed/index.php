<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class MoneyOwed extends Base
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

  function addMoneyOwedButton(): string
  {
    $html = '
    <div class="row">
        <ul class="subNav">
          <li><a style="text-decoration: none;" href="addMoneyOwed/">+</a></li>
        </ul>
      </div>';

    return $html;
  }

  function showMoneyOwedTable($transactions): string
  {
    $html = '';

    if($transactions > 0) {
      $sqlTwo = "SELECT * FROM moneyOwed";
      $sqlTwoResult = mysqli_query($this->getLink(), $sqlTwo);

      while($row = mysqli_fetch_array($sqlTwoResult)) {
        $id = $row['moneyOwed_id'];
        $recipient = $row['MoneyOwedRecipient'];
        $for = $row['MoneyOwedFor'];
        $amount = $row['MoneyOwedAmount'];

        $html .= '
        <div class="list">
          <div class="row">
            <p style="font-weight: bold;">You owe $' . $amount . ' to ' . $recipient . ' for ' . $for . '</p>

          </div>
          <ul class="row actionButtons">
            <li><a class="edit" href="editMoneyOwed/index.php?id=' . $id . '">Edit money owed</a></li>
            <li><a class="delete" href="confirmation.php?id=' . $id . '">Delete money owed</a></li>
          </ul>
        </div>';
        }
    }

    if($html == '') {
      $html .= '<p>No money owed!</p>';
    }

    return $html;
  }

  function main(): string
  {
    $transactions = $this->getCurrentBalance()[0];
    $currentBalance = $this->getCurrentBalance()[1];
    $html = $this->displayCurrentBalance($currentBalance);
    $html .= '<br />';
    $html .= $this->addMoneyOwedButton();
    $html .= $this->showMoneyOwedTable($transactions);

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$moneyOwed = new MoneyOwed();
$moneyOwed->setLink($link);
$moneyOwed->setTitle("Ephraim Becker - Budgeting - Money owed");
$moneyOwed->setLocalStyleSheet('css/style.css');
$moneyOwed->setLocalScript(NULL);
$moneyOwed->setHeader('Budgeting - Money owed');
$moneyOwed->setUrl($_SERVER['REQUEST_URI']);
$moneyOwed->setBody($moneyOwed->main());

$moneyOwed->html();
