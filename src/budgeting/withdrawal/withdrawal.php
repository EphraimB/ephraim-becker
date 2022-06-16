<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class Withdrawal
{
  private $isAdmin;
  private $link;
  private $withdrawalAmount;
  private $withdrawalDescription;

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

  function setWithdrawalAmount($withdrawalAmount): void
  {
    $this->withdrawalAmount = $withdrawalAmount;
  }

  function getWithdrawalAmount(): float
  {
    return $this->withdrawalAmount;
  }

  function setWithdrawalDescription($withdrawalDescription): void
  {
    $this->withdrawalDescription = $withdrawalDescription;
  }

  function getWithdrawalDescription(): string
  {
    return $this->withdrawalDescription;
  }

  function withdrawal(): string
  {
    $sql = $this->getLink()->prepare("INSERT INTO withdrawals (DateCreated, WithdrawalAmount, WithdrawalDescription)
     VALUES (?, ?, ?)");
     $sql->bind_param('sds', $dateNow, $withdrawalAmount, $withdrawalDescription);

     $dateNow = date("Y-m-d H:i:s");
     $withdrawalAmount = $this->getWithdrawalAmount();
     $withdrawalDescription = $this->getWithdrawalDescription();

     $sql->execute();

     $sql->close();
     $this->getLink()->close();

     header("location: ../");
  }
}
$config = new Config();
$link = $config->connectToServer();

$withdrawal = new Withdrawal();
$withdrawal->setLink($link);
$withdrawal->setWithdrawalAmount(floatval($_POST['withdrawalAmount']));
$withdrawal->setWithdrawalDescription($_POST['withdrawalDescription']);
$withdrawal->withdrawal();
