<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class Wishlist extends Base
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

  function addToWishlistButton(): string
  {
    $html = '
    <div class="row">
        <ul class="subNav">
          <li><a style="text-decoration: none;" href="addToWishlist/">+</a></li>
        </ul>
      </div>';

    return $html;
  }

  function showWishlist($transactions): string
  {
    $html = '';

    if($transactions > 0) {
      $sqlTwo = "SELECT *, DATE_FORMAT(Date - INTERVAL TimezoneOffset SECOND, '%m/%d/%Y %h:%i:%s %p') AS DateFormat FROM WantToBuy";
      $sqlTwoResult = mysqli_query($this->getLink(), $sqlTwo);

        while($row = mysqli_fetch_array($sqlTwoResult)) {
          $id = $row['WantToBuyId'];
          $item = $row['Item'];
          $link = $row['Link'];
          $price = $row['Price'];
          $dateFormat = $row['DateFormat'];

          $html .= '
          <div class="list">
            <div class="row">
              <p style="font-weight: bold;">' . $item . '&nbsp;</p>
              <br />
              <p>$' . $price . '&nbsp;</p>
              <p>' . $dateFormat . '&nbsp;</p>
            </div>
            <ul class="row actionButtons">
              <li><a class="edit" href="editWantToBuy/index.php?id=' . $id . '">Edit item</a></li>
              <li><a class="delete" href="confirmation.php?id=' . $id . '">Delete item</a></li>
            </ul>
          </div>';
        }
    }

    if($html == '') {
      $html .= '<p>Nothing on my wishlist</p>';
    }

    return $html;
  }

  function main(): string
  {
    $transactions = $this->getCurrentBalance()[0];
    $currentBalance = $this->getCurrentBalance()[1];
    $html = $this->displayCurrentBalance($currentBalance);
    $html .= '<br />';
    $html .= $this->addToWishlistButton();
    $html .= $this->showWishlist($transactions);

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$wishlist = new Wishlist();
$wishlist->setLink($link);
$wishlist->setTitle("Ephraim Becker - Budgeting - Wishlist");
$wishlist->setLocalStyleSheet('css/style.css');
$wishlist->setLocalScript(NULL);
$wishlist->setHeader('Budgeting - Wishlist');
$wishlist->setUrl($_SERVER['REQUEST_URI']);
$wishlist->setBody($wishlist->main());

$wishlist->html();
