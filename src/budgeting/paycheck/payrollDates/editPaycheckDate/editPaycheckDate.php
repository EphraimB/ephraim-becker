<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditPaycheckInfo
{
  private $isAdmin;
  private $link;
  private $id;
  private $paycheckDate;

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

  function setPaycheckDate($paycheckDate): void
  {
    $this->paycheckDate = $paycheckDate;
  }

  function getPaycheckDate(): string
  {
    return $this->paycheckDate;
  }

  function editPaycheckDate(): void
  {
    $sql = $this->getLink()->prepare("UPDATE payrollDates SET DateModified=?, PayrollDate=? WHERE payrollDates_id=?");
    $sql->bind_param('ssi', $dateNow, $payrollDate, $id);

    $id = $this->getId();

    $dateNow = date("Y-m-d H:i:s");
    $payrollDate = $this->getPaycheckDate();

    $sql->execute();

    $sql->close();

    header("location: ../");
  }
}
$config = new Config();
$link = $config->connectToServer();

$editPaycheckInfo = new EditPaycheckInfo();
$editPaycheckInfo->setLink($link);
$editPaycheckInfo->setId(intval($_POST['id']));
$editPaycheckInfo->setPaycheckDate($_POST['paycheckDate']);

$editPaycheckInfo->editPaycheckDate();
