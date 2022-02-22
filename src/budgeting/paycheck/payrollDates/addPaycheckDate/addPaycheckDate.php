<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class AddPaycheckDate
{
  private $isAdmin;
  private $link;
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

  function setPaycheckDate($setPaycheckDate): void
  {
    $this->setPaycheckDate = $setPaycheckDate;
  }

  function getPaycheckDate(): string
  {
    return $this->setPaycheckDate;
  }

  function addPaycheckDate(): void
  {
    $sql = $this->getLink()->prepare("INSERT INTO payrollDates (DateCreated, DateModified, PayrollDate)
    VALUES (?, ?, ?)");
    $sql->bind_param('sss', $dateNow, $dateNow, $payrollDate);

    $dateNow = date("Y-m-d H:i:s");
    $payrollDate = $this->getPaycheckDate();

    $sql->execute();

    $sql->close();

    header("location: ../");
  }
}
$config = new Config();
$link = $config->connectToServer();

$addPaycheckDate = new AddPaycheckDate();
$addPaycheckDate->setLink($link);
$addPaycheckDate->setPaycheckDate($_POST['paycheckDate']);

$addPaycheckDate->addPaycheckDate();
