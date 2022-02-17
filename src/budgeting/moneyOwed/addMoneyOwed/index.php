<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class AddMoneyOwedForm extends Base
{
  private $isAdmin;
  private $link;

  function __construct()
  {
    $this->setIsAdmin();

    if(!$this->getIsAdmin()) {
      header("location: ../../../");
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
        $currentBalance = floatval($row['currentBalance']);
      }
    }

    if(is_null($currentBalance)) {
      $currentBalance = 0.00;
    }

    $html = '<h2>Current balance: $' . $currentBalance . '</h2>';

    return $html;
  }

  function addMoneyOwedForm(): string
  {
    $html = '
      <form action="addMoneyOwed.php" method="post">
        <div>
          <label for="recipient">Recipient:</label>
          <input type="text" name="recipient" />
        </div>
        <br />
        <div>
          <label for="for">What I\'m borrowing for:</label>
          <input type="text" name="for" />
        </div>
        <br />
        <div>
          <label for="amount">Amount I\'m borrowing: $</label>
          <input type="number" name="amount" />
        </div>
        <br />
        <input type="submit" value="Add money owed" />
      </form>
    ';

    return $html;
  }

  function main(): string
  {
    $html = $this->displayCurrentBalance();
    $html .= $this->addMoneyOwedForm();

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$addMoneyOwedForm = new AddMoneyOwedForm();
$addMoneyOwedForm->setLink($link);
$addMoneyOwedForm->setTitle("Ephraim Becker - Budgeting - Add money owed form");
$addMoneyOwedForm->setLocalStyleSheet('css/style.css');
$addMoneyOwedForm->setLocalScript(NULL);
$addMoneyOwedForm->setHeader('Budgeting - Add money owed form');
$addMoneyOwedForm->setUrl($_SERVER['REQUEST_URI']);
$addMoneyOwedForm->setBody($addMoneyOwedForm->main());

$addMoneyOwedForm->html();
