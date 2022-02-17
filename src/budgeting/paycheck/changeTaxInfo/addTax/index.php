<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class AddTaxForm extends Base
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
        $currentBalance = floatval($row['currentBalance']);
      }
    }

    if(is_null($currentBalance)) {
      $currentBalance = 0.00;
    }

    $html = '<h2>Current balance: $' . $currentBalance . '</h2>';

    return $html;
  }

  function displayAddTaxForm(): string
  {
    $html = '
    <form action="addTax.php" method="post">
      <div class="row">
        <label for="taxTitle">Enter tax title:</label>
        <input type="text" id="taxTitle" name="taxTitle" />
      </div>
      <br />
      <div class="row">
        <label for="taxAmount">Enter balance of tax amount (xxx.xxxxx): $</label>
        <input type="number" min="0" step="any" id="taxAmount" name="taxAmount" />
      </div>
      <br />

      <input type="submit" value="Add tax" />
    </form>';

    return $html;
  }

  function main(): string
  {
    $html = $this->displayCurrentBalance();
    $html .= $this->displayAddTaxForm();

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$addTaxForm = new AddTaxForm();
$addTaxForm->setLink($link);
$addTaxForm->setTitle("Ephraim Becker - Budgeting - Add tax form");
$addTaxForm->setLocalStyleSheet('css/style.css');
$addTaxForm->setLocalScript(NULL);
$addTaxForm->setHeader('Budgeting - Add tax form');
$addTaxForm->setUrl($_SERVER['REQUEST_URI']);
$addTaxForm->setBody($addTaxForm->main());

$addTaxForm->html();
