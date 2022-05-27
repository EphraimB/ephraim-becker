<?php
declare(strict_types=1);

session_start();

$home = getenv('HOME');

require_once($home . '/config.php');

class DepositCronJob
{
  private $link;
  private $depositAmount;
  private $depositDescription;

  function __construct()
  {
    $isCLI = (php_sapi_name() == 'cli');

    if(!$isCLI) {
      die("cannot run!");
    } else {
      parse_str(implode('&', array_slice($_SERVER['argv'], 1)), $_GET);
    }
  }

  function setLink($link)
  {
    $this->link = $link;
  }

  function getLink()
  {
    return $this->link;
  }

  function setDepsoitAmount($depositAmount): void
  {
    $this->depositAmount = $depositAmount;
  }

  function getDepositAmount(): float
  {
    return $this->depositAmount;
  }

  function setWithdrawalDescription($depositDescription): void
  {
    $this->depositDescription = $depositDescription;
  }

  function getDepositDescription(): string
  {
    return $this->depositDescription;
  }

  function withdrawal()
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
  }
}
$config = new Config();
$link = $config->connectToServer();

$depositCronJob = new DepositCronJob();
$depositCronJob->setLink($link);
$depositCronJob->setDepositAmount(floatval($_GET['depositAmount']));
$depositCronJob->setDepositDescription($_GET['depositDescription']);
$depositCronJob->withdrawal();
