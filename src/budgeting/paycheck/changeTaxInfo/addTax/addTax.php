<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class AddTax
{
  private $isAdmin;
  private $link;
  private $taxTitle;
  private $taxAmount;

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

  function setTaxTitle($taxTitle): void
  {
    $this->taxTitle = $taxTitle;
  }

  function getTaxTitle(): string
  {
    return $this->taxTitle;
  }

  function setTaxAmount($taxAmount): void
  {
    $this->taxAmount = $taxAmount;
  }

  function getTaxAmount(): float
  {
    return $this->taxAmount;
  }

  function addTax(): string
  {
    $sql = $this->getLink()->prepare("INSERT INTO payrollTaxes (DateCreated, DateModified, taxTitle, taxAmount)
    VALUES (?, ?, ?, ?)");
    $sql->bind_param('sssd', $dateNow, $dateNow, $taxTitle, $taxAmount);

    $dateNow = date("Y-m-d H:i:s");
    $taxTitle = $this->getTaxTitle();
    $taxAmount = $this->getTaxAmount();

    $sql->execute();

    header("location: ../");
  }
}
$config = new Config();
$link = $config->connectToServer();

$addTax = new AddTax();
$addTax->setLink($link);
$addTax->setTaxTitle($_POST['taxTitle']);
$addTax->setTaxAmount(floatval($_POST['taxAmount']));
$addTax->addTax();
