<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class Deposit
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

  function deposit(): string
  {
    $sql = $this->getLink()->prepare("INSERT INTO deposits (DateCreated, DepositAmount, DepositDescription)
    VALUES (?, ?, ?)");
    $sql->bind_param('sds', $dateNow, $depositAmount, $depositDescription);

    $dateNow = date("Y-m-d H:i:s");
    $depositAmount = $_POST['depositAmount'];
    $depositDescription = $_POST['depositDescription'];

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
$deposit->deposit();
