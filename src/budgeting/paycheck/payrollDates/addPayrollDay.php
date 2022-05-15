<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class AddPayrollDay
{
  private $isAdmin;
  private $link;
  private $paycheckDay;

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

  function setPaycheckDay($paycheckDay): void
  {
    $this->paycheckDay = $paycheckDay;
  }

  function getPaycheckDay(): int
  {
    return $this->paycheckDay;
  }

  function addPayrollDay(): void
  {
    $sql = $this->getLink()->prepare("INSERT INTO payrollDates (DateCreated, PayrollDay)
    VALUES (?, ?)");
    $sql->bind_param('si', $dateNow, $payrollDay);

    $dateNow = date("Y-m-d H:i:s");
    $payrollDay = $this->getPaycheckDay();

    $sql->execute();

    $sql->close();

    header("location: index.php");
  }
}
$config = new Config();
$link = $config->connectToServer();

$addPayrollDay = new AddPayrollDay();
$addPayrollDay->setLink($link);
$addPayrollDay->setPaycheckDay(intval($_GET['day']));

$addPayrollDay->addPayrollDay();
