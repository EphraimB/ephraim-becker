<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class AddMoneyOwed
{
  private $isAdmin;
  private $link;
  private $recipient;
  private $for;
  private $amount;

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

  function setRecipient($recipient): void
  {
    $this->recipient = $recipient;
  }

  function getRecipient(): string
  {
    return $this->recipient;
  }

  function setFor($for): void
  {
    $this->for = $for;
  }

  function getFor(): string
  {
    return $this->for;
  }

  function setAmount($amount): void
  {
    $this->amount = $amount;
  }

  function getAmount(): float
  {
    return $this->amount;
  }

  function addMoneyOwed(): string
  {
    $sql = $this->getLink()->prepare("INSERT INTO moneyOwed (DateCreated, DateModified, MoneyOwedRecipient, MoneyOwedFor, MoneyOwedAmount)
     VALUES (?, ?, ?, ?, ?)");
     $sql->bind_param('ssssd', $dateNow, $dateNow, $recipient, $for, $amount);

     $dateNow = date("Y-m-d H:i:s");
     $recipient = $this->getRecipient();
     $for = $this->getFor();
     $amount = $this->getAmount();

     $sql->execute();

     $sql->close();
     $this->getLink()->close();

     header("location: ../");
  }
}
$config = new Config();
$link = $config->connectToServer();

$addMoneyOwed = new AddMoneyOwed();
$addMoneyOwed->setLink($link);
$addMoneyOwed->setRecipient($_POST['recipient']);
$addMoneyOwed->setFor($_POST['for']);
$addMoneyOwed->setAmount(floatval($_POST['amount']));
$addMoneyOwed->addMoneyOwed();
