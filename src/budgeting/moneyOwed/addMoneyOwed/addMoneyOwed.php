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
  private $planAmount;
  private $frequency;

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

  function setPlanAmount($planAmount): void
  {
    $this->planAmount = $planAmount;
  }

  function getPlanAmount(): float
  {
    return $this->planAmount;
  }

  function setFrequency($frequency): void
  {
    $this->frequency = $frequency;
  }

  function getFrequency(): int
  {
    return $this->frequency;
  }

  function addMoneyOwed(): string
  {
    $sql = $this->getLink()->prepare("INSERT INTO moneyOwed (DateCreated, DateModified, MoneyOwedRecipient, MoneyOwedFor, MoneyOwedAmount, planAmount, frequency)
     VALUES (?, ?, ?, ?, ?, ?, ?)");
     $sql->bind_param('ssssddi', $dateNow, $dateNow, $recipient, $for, $amount, $planAmount, $frequency);

     $dateNow = date("Y-m-d H:i:s");
     $recipient = $this->getRecipient();
     $for = $this->getFor();
     $amount = $this->getAmount();
     $planAmount = $this->getPlanAmount();
     $frequency = $this->getFrequency();

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
$addMoneyOwed->setPlanAmount(floatval($_POST['planAmount']));
$addMoneyOwed->setFrequency(intval($_POST['frequency']));
$addMoneyOwed->addMoneyOwed();
