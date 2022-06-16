<?php
declare(strict_types=1);

session_start();

$home = getenv('HOME');

require_once($home . '/config.php');

class WithdrawalCronJob
{
  private $link;
  private $deductAmount;
  private $primaryId;

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

  function setDeductAmount($deductAmount): void
  {
    $this->deductAmount = $deductAmount;
  }

  function getDeductAmount(): float
  {
    return $this->deductAmount;
  }

  function setPrimaryId($primaryId): void
  {
    $this->primaryId = $primaryId;
  }

  function getPrimaryId(): int
  {
    return $this->primaryId;
  }

  function getMoneyOwedAmount(): float
  {
    $sql = $this->getLink()->prepare("SELECT MoneyOwedAmount FROM moneyOwed WHERE moneyOwed_id=?");
    $sql->bind_param('i', $id);

    $id = $this->getPrimaryId();

    $sql->execute();

    $sqlResult = $sql->get_result();

    while($row = mysqli_fetch_array($sqlResult)){
      $amount = floatval($row['MoneyOwedAmount']);
    }

    return $amount;
  }

  function deductMoneyOwed()
  {
     $sql = $this->getLink()->prepare("UPDATE moneyOwed SET DateModified=?, MoneyOwedAmount=? WHERE moneyOwed_id=?");
     $sql->bind_param('sdi', $dateNow, $withdrawalAmount, $id);

     $dateNow = date("Y-m-d H:i:s");
     $withdrawalAmount = $this->getMoneyOwedAmount() - $this->getDeductAmount();

     $id = $this->getPrimaryId();

     $sql->execute();

     $sql->close();
     $this->getLink()->close();
  }
}
$config = new Config();
$link = $config->connectToServer();

$withdrawalCronJob = new WithdrawalCronJob();
$withdrawalCronJob->setLink($link);
$withdrawalCronJob->setDeductAmount(floatval($_GET['deductAmount']));
$withdrawalCronJob->setPrimaryId(intval($_GET['primaryId']));
$withdrawalCronJob->deductMoneyOwed();
