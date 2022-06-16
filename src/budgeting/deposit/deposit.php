<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class Deposit
{
  private $isAdmin;
  private $link;
  private $depositAmount;
  private $depositDescription;

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

  function setDepositAmount($depositAmount): void
  {
    $this->depositAmount = $depositAmount;
  }

  function getDepositAmount(): float
  {
    return $this->depositAmount;
  }

  function setDepositDescription($depositDescription): void
  {
    $this->depositDescription = $depositDescription;
  }

  function getDepositDescription(): string
  {
    return $this->depositDescription;
  }

  function deposit(): string
  {
    $sql = $this->getLink()->prepare("INSERT INTO deposits (DateCreated, DepositAmount, DepositDescription)
    VALUES (?, ?, ?)");
    $sql->bind_param('sds', $dateNow, $depositAmount, $depositDescription);

    $dateNow = date("Y-m-d H:i:s");
    $depositAmount = $this->getDepositAmount();
    $depositDescription = $this->getDepositDescription();

    $sql->execute();

    $sql->close();
    $this->getLink()->close();

    header("location: ../");
  }
}
$config = new Config();
$link = $config->connectToServer();

$deposit = new Deposit();
$deposit->setLink($link);
$deposit->setDepositAmount(floatval($_POST['depositAmount']));
$deposit->setDepositDescription($_POST['depositDescription']);
$deposit->deposit();
