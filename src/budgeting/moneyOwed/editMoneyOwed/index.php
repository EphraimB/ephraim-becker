<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class EditMoneyOwedForm extends Base
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

  function displayMoneyOwedForm(): string
  {
    $sqlTwo = $this->getLink()->prepare("SELECT * FROM moneyOwed WHERE moneyOwed_id=?");
    $sqlTwo->bind_param("i", $id);

    $id = $this->getId();

    $sqlTwo->execute();

    $sqlTwoResult = $sqlTwo->get_result();

    while($row = mysqli_fetch_array($sqlTwoResult)){
      $recipient = $row['MoneyOwedRecipient'];
      $for = $row['MoneyOwedFor'];
      $amount = $row['MoneyOwedAmount'];
    }

    $html = '
    <form action="editMoneyOwed.php" method="post">
      <div>
        <label for="recipient">Recipient:</label>
        <input type="text" name="recipient" value="' . $recipient . '" />
      </div>
      <br />
      <div>
        <label for="for">What I\'m borrowing for:</label>
        <input type="text" name="for" value="' . $for . '" />
      </div>
      <br />
      <div>
        <label for="amount">Amount I\'m borrowing: $</label>
        <input type="number" step="any" name="amount" value="' . $amount . '" />
      </div>
      <br />
      <input type="hidden" name="id" value="' . $this->getId() . '" />
      <input type="submit" value="Edit money owed" />
    </form>';

    return $html;
  }

  function main(): string
  {
    $html = $this->displayCurrentBalance();
    $html .= $this->displayMoneyOwedForm();

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$editMoneyOwedForm = new EditMoneyOwedForm();
$editMoneyOwedForm->setLink($link);
$editMoneyOwedForm->setId(intval($_GET['id']));
$editMoneyOwedForm->setTitle("Ephraim Becker - Budgeting - Edit money owed form");
$editMoneyOwedForm->setLocalStyleSheet('css/style.css');
$editMoneyOwedForm->setLocalScript(NULL);
$editMoneyOwedForm->setHeader('Budgeting - Edit money owed form');
$editMoneyOwedForm->setUrl($_SERVER['REQUEST_URI']);
$editMoneyOwedForm->setBody($editMoneyOwedForm->main());

$editMoneyOwedForm->html();
