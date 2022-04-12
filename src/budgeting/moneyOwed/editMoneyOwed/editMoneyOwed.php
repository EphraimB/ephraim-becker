<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditMoneyOwed
{
  private $isAdmin;
  private $link;
  private $id;
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

  function setId($id): void
  {
    $this->id = $id;
  }

  function getId(): int
  {
    return $this->id;
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

  function editMoneyOwed(): void
  {
    $sql = $this->getLink()->prepare("UPDATE moneyOwed SET DateModified=?, MoneyOwedRecipient=?, MoneyOwedFor=?, MoneyOwedAmount=?, planAmount=?, frequency=? WHERE moneyOwed_id=?");
    $sql->bind_param('sssddii', $dateNow, $recipient, $for, $amount, $planAmount, $frequency, $id);

    $id = $this->getId();

    $dateNow = date("Y-m-d H:i:s");
    $recipient = $this->getRecipient();
    $for = $this->getFor();
    $amount = $this->getAmount();
    $planAmount = $this->getPlanAmount();
    $frequency = $this->getFrequency();

    $sql->execute();

    $sql->close();

    header("location: ../");
  }
}
$config = new Config();
$link = $config->connectToServer();

$editMoneyOwed = new EditMoneyOwed();
$editMoneyOwed->setLink($link);
$editMoneyOwed->setId(intval($_POST['id']));
$editMoneyOwed->setRecipient($_POST['recipient']);
$editMoneyOwed->setFor($_POST['for']);
$editMoneyOwed->setAmount(floatval($_POST['amount']));
$editMoneyOwed->setPlanAmount(floatval($_POST['planAmount']));
$editMoneyOwed->setFrequency(intval($_POST['frequency']));
$editMoneyOwed->editMoneyOwed();
