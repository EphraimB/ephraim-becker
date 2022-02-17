<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditTax
{
  private $isAdmin;
  private $link;
  private $id;
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

  function setId($id): void
  {
    $this->id = $id;
  }

  function getId(): int
  {
    return $this->id;
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

  function editTax(): void
  {
    $sql = $this->getLink()->prepare("UPDATE payrollTaxes SET DateModified=?, taxTitle=?, taxAmount=? WHERE payrollTax_id=?");
    $sql->bind_param('ssdi', $dateNow, $taxTitle, $taxAmount, $id);

    $id = $this->getId();

    $dateNow = date("Y-m-d H:i:s");

    $taxTitle = $this->getTaxTitle();
    $taxAmount = $this->getTaxAmount();

    $sql->execute();

    $sql->close();

    header("location: ../");
  }
}
$config = new Config();
$link = $config->connectToServer();

$editTax = new EditTax();
$editTax->setLink($link);
$editTax->setId(intval($_POST['id']));
$editTax->setTaxTitle($_POST['taxTitle']);
$editTax->setTaxAmount(floatval($_POST['taxAmount']));

$editTax->editTax();
