<?php
declare(strict_types=1);

session_start();

$home = getenv('HOME');

require_once($home . '/config.php');

class WithdrawalCronJob
{
  private $link;
  private $withdrawalAmount;
  private $withdrawalDescription;

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

  function withdrawal()
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
  }
}
$config = new Config();
$link = $config->connectToServer();

$withdrawalCronJob = new WithdrawalCronJob();
$withdrawalCronJob->setLink($link);
$withdrawalCronJob->setWithdrawalAmount(floatval($_GET['withdrawalAmount']));
$withdrawalCronJob->setWithdrawalDescription($_GET['withdrawalDescription']);
$withdrawalCronJob->withdrawal();
