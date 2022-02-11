<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class WithdrawalForm extends Base
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

  function displayCurrentBalance(): string
  {
    $sql = "SELECT (SELECT SUM(DepositAmount) from deposits) - (SELECT SUM(WithdrawalAmount) FROM withdrawals) AS currentBalance";
    $sqlResult = mysqli_query($this->getLink(), $sql);

    if(mysqli_num_rows($sqlResult) > 0) {
      while($row = mysqli_fetch_array($sqlResult)){
        $currentBalance = $row['currentBalance'];
      }
    }

    if(is_null($currentBalance)) {
      $currentBalance = 0.00;
    }

    $html = '<h2>Current balance: $' . $currentBalance . '</h2>';

    return $html;
  }

  function displayWithdrawalForm(): string
  {
    $html = '
    <form action="withdrawal.php" method="post">
        <div class="row">
          <label for="withdrawalAmount">Enter balance to subtract from current (xxx.xx): $</label>
          <input type="number" min="0" step="any" id="withdrawalAmount" name="withdrawalAmount" />
        </div>
        <br />
        <div class="row">
          <label for="withdrawalDescription">Enter balance description:</label>
          <input type="text" id="withdrawalDescription" name="withdrawalDescription" />
        </div>
        <br />

        <input type="submit" />
      </form>';

    return $html;
  }

  function main(): string
  {
    $html = $this->displayCurrentBalance();
    $html .= $this->displayWithdrawalForm();

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$withdrawalForm = new WithdrawalForm();
$withdrawalForm->setLink($link);
$withdrawalForm->setTitle("Ephraim Becker - Budgeting - Withdrawal form");
$withdrawalForm->setLocalStyleSheet('css/style.css');
$withdrawalForm->setLocalScript(NULL);
$withdrawalForm->setHeader('Budgeting - Withdrawal form');
$withdrawalForm->setUrl($_SERVER['REQUEST_URI']);
$withdrawalForm->setBody($withdrawalForm->main());

$withdrawalForm->html();
