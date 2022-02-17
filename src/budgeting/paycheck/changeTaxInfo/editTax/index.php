<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class EditTaxForm extends Base
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

  function displayEditTaxForm(): string
  {
    $sqlTwo = $this->getLink()->prepare("SELECT * FROM payrollTaxes WHERE payrollTax_id=?");
    $sqlTwo->bind_param("i", $id);

    $id = $this->getId();

    $sqlTwo->execute();

    $sqlTwoResult = $sqlTwo->get_result();

    while($row = mysqli_fetch_array($sqlTwoResult)){
      $title = $row['taxTitle'];
      $price = $row['taxAmount'];
    }

    $sqlTwo->close();

    $html = '
    <form action="EditTax.php" method="post">
      <div class="row">
        <label for="taxTitle">Enter tax title:</label>
        <input type="text" id="taxTitle" name="taxTitle" value="' . $title . '" />
      </div>
      <br />
      <div class="row">
        <label for="taxAmount">Enter balance of tax amount (xxx.xxxxx): $</label>
        <input type="number" min="0" step="any" id="taxAmount" name="taxAmount" value="' . $price . '" />
      </div>
      <br />

      <input type="submit" value="Edit tax" />
    </form>';

    return $html;
  }

  function main(): string
  {
    $html = $this->displayCurrentBalance();
    $html .= $this->displayEditTaxForm();

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$editTaxForm = new EditTaxForm();
$editTaxForm->setLink($link);
$editTaxForm->setId(intval($_GET['id']));
$editTaxForm->setTitle("Ephraim Becker - Budgeting - Edit tax form");
$editTaxForm->setLocalStyleSheet('css/style.css');
$editTaxForm->setLocalScript(NULL);
$editTaxForm->setHeader('Budgeting - Edit tax form');
$editTaxForm->setUrl($_SERVER['REQUEST_URI']);
$editTaxForm->setBody($editTaxForm->main());

$editTaxForm->html();
