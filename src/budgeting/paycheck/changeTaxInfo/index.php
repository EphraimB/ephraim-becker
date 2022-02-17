<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class ChangeTaxInfoForm extends Base
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

  function displayCurrentBalance(): array
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

    return array(mysqli_num_rows($sqlResult), $html);
  }

  function addTaxButton(): string
  {
    $html = '
    <div class="row">
        <ul class="subNav">
          <li><a style="text-decoration: none;" href="addTax/">+</a></li>
        </ul>
      </div>';

    return $html;
  }

  function showtaxes($transactions): string
  {
    $html = '';

    if($transactions > 0) {
      $sqlTwo = "SELECT * FROM payrollTaxes";
      $sqlTwoResult = mysqli_query($this->getLink(), $sqlTwo);

      while($row = mysqli_fetch_array($sqlTwoResult)) {
        $id = $row['payrollTax_id'];
        $title = $row['taxTitle'];
        $amount = $row['taxAmount'];

        $html .= '
        <div class="list">
          <div class="row">
            <p style="font-weight: bold;">' . $title . '&nbsp;</p>
            <br />
            <p>$' . $amount . '&nbsp;</p>';

          $html .= '</div>
          <ul class="row actionButtons">
            <li><a class="edit" href="editTax/index.php?id=' . $id . '">Edit tax</a></li>
            <li><a class="delete" href="confirmation.php?id=' . $id . '">Delete tax</a></li>
          </ul>
        </div>';
      }
    }

    if($html == '') {
      $html .= '<p>No taxes!</p>';
    }

    return $html;
  }

  function main(): string
  {
    $transactions = $this->displayCurrentBalance()[0];
    $html = $this->displayCurrentBalance()[1];
    $html .= $this->addTaxButton();
    $html .= $this->showtaxes($transactions);

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$changeTaxInfoForm = new ChangeTaxInfoForm();
$changeTaxInfoForm->setLink($link);
$changeTaxInfoForm->setTitle("Ephraim Becker - Budgeting - Change tax info form");
$changeTaxInfoForm->setLocalStyleSheet('css/style.css');
$changeTaxInfoForm->setLocalScript(NULL);
$changeTaxInfoForm->setHeader('Budgeting - Change tax info form');
$changeTaxInfoForm->setUrl($_SERVER['REQUEST_URI']);
$changeTaxInfoForm->setBody($changeTaxInfoForm->main());

$changeTaxInfoForm->html();
