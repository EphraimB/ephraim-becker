<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class DepositForm extends Base
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

  function displayDepositForm(): string
  {
    $html = '
    <form action="deposit.php" method="post">
        <div class="row">
          <label for="depositAmount">Enter balance to add to current (xx.xx): $</label>
          <input type="number" min="0" step="any" id="depositAmount" name="depositAmount" />
        </div>
        <br />
        <div class="row">
          <label for="depositDescription">Enter balance description:</label>
          <input type="text" id="depositDescription" name="depositDescription" />
        </div>
        <br />

        <input type="submit" />
      </form>';

    return $html;
  }

  function main(): string
  {
    $html = $this->displayCurrentBalance();
    $html .= $this->displayDepositForm();

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$depositForm = new DepositForm();
$depositForm->setLink($link);
$depositForm->setTitle("Ephraim Becker - Budgeting - Deposit form");
$depositForm->setLocalStyleSheet('css/style.css');
$depositForm->setLocalScript(NULL);
$depositForm->setHeader('Budgeting - Deposit form');
$depositForm->setUrl($_SERVER['REQUEST_URI']);
$depositForm->setBody($depositForm->main());

$depositForm->html();
