<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class AddToWishlistForm extends Base
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

  function addToWishlistForm(): string
  {
    $html = '
    <form action="addToWishlist.php" method="post">
      <div class="row">
        <label for="item">Enter item: </label>
        <input type="text" id="item" name="item" required />
      </div>
      <br />
      <div class="row">
        <label for="link">Enter link to item (ex: URL): </label>
        <input type="text" id="link" name="link" required />
      </div>
      <br />
      <div class="row">
        <label for="price">Enter cost of item (xxx.xx): </label>
        &nbsp;
        $<input type="number" min="0" step="any" id="price" name="price" required />
      </div>
      <br />

      <input type="submit" id="submit" value="Add to wishlist" />
    </form>';

    return $html;
  }

  function main(): string
  {
    $currentBalance = $this->getCurrentBalance();
    $html = $this->displayCurrentBalance($currentBalance);
    $html .= $this->addToWishlistForm();

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$addToWishlistForm = new AddToWishlistForm();
$addToWishlistForm->setLink($link);
$addToWishlistForm->setTitle("Ephraim Becker - Budgeting - Add to wishlist form");
$addToWishlistForm->setLocalStyleSheet('css/style.css');
$addToWishlistForm->setLocalScript(NULL);
$addToWishlistForm->setHeader('Budgeting - Add to wishlist form');
$addToWishlistForm->setUrl($_SERVER['REQUEST_URI']);
$addToWishlistForm->setBody($addToWishlistForm->main());

$addToWishlistForm->html();
