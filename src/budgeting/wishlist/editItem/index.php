<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class EditItemForm extends Base
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

  function getNumOfWishlistItems(): int
  {
    $sql = "SELECT COUNT(WantToBuyId) AS wishlistAmount FROM WantToBuy";
    $sqlResult = mysqli_query($this->getLink(), $sql);

    while($row = mysqli_fetch_array($sqlResult)){
      $wishlistAmount = intval($row['wishlistAmount']);
    }

    return $wishlistAmount;
  }

  function editItemForm(): string
  {
    $sqlTwo = $this->getLink()->prepare("SELECT * FROM WantToBuy WHERE WantToBuyId=?");
    $sqlTwo->bind_param("i", $id);

    $id = $this->getId();

    $sqlTwo->execute();

    $sqlTwoResult = $sqlTwo->get_result();

    while($row = mysqli_fetch_array($sqlTwoResult)){
      $item = $row['Item'];
      $link = $row['Link'];
      $price = $row['Price'];
      $priority = $row['Priority'];
    }

    $sqlTwo->close();
    $html = '
    <form action="editItem.php" method="post">
      <div class="row">
        <label for="item">Enter item: </label>
        <input type="text" id="item" name="item" value="' . $item . '" required />
      </div>
      <br />
      <div class="row">
        <label for="link">Enter link to item (ex: URL): </label>
        <input type="text" id="link" name="link" value="' . $link . '" required />
      </div>
      <br />
      <div class="row">
        <label for="price">Enter cost of item (xxx.xx): </label>
        &nbsp;
        $<input type="number" min="0" step="any" id="price" name="price" value="' . $price . '" required />
      </div>
      <br />
      <div class="row">
        <label for="price">Priority (lower number is higher): </label>
        &nbsp;
        <input type="number" min="0" max="' . $this->getNumOfWishlistItems()-1 . '" step="any" id="priority" name="priority" value="' . $priority . '" required />
      </div>
      <br />
      <input type="hidden" name="id" value="' . $this->getId() . '" />
      <input type="submit" id="submit" value="Edit item" />
    </form>';

    return $html;
  }

  function main(): string
  {
    $currentBalance = $this->getCurrentBalance();
    $html = $this->displayCurrentBalance($currentBalance);
    $html .= $this->editItemForm();

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$editItemForm = new EditItemForm();
$editItemForm->setLink($link);
$editItemForm->setTitle("Ephraim Becker - Budgeting - Edit item form");
$editItemForm->setLocalStyleSheet('css/style.css');
$editItemForm->setLocalScript(NULL);
$editItemForm->setHeader('Budgeting - Edit item form');
$editItemForm->setUrl($_SERVER['REQUEST_URI']);
$editItemForm->setId(intval($_GET['id']));
$editItemForm->setBody($editItemForm->main());

$editItemForm->html();
