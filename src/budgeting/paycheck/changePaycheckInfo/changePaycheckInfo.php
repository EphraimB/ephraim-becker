<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class ChangePaycheckInfo
{
  private $isAdmin;
  private $link;
  private $hourWorked;
  private $daysPerWeek;
  private $payPerHour;

  function __construct()
  {
    $this->setIsAdmin();

    if(!$this->getIsAdmin()) {
      header("location: ../../../");
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

  function setHourWorked($hourWorked): void
  {
    $this->hourWorked = $hourWorked;
  }

  function getHourWorked(): int
  {
    return $this->hourWorked;
  }

  function setDaysPerWeek($daysPerWeek): void
  {
    $this->daysPerWeek = $daysPerWeek;
  }

  function getDaysPerWeek(): int
  {
    return $this->daysPerWeek;
  }

  function setPayPerHour($payPerHour): void
  {
    $this->payPerHour = $payPerHour;
  }

  function getPayPerHour(): float
  {
    return $this->payPerHour;
  }

  function changePaycheckInfo(): string
  {
    $totalHours = 0;
    $totalDaysPerWeek = 0;
    $totalPayPerHour = 0;

    $sql = "SELECT SUM(hoursWorked) AS totalHours, SUM(daysPerWeek) AS totalDaysPerWeek, SUM(payPerHour) AS totalPayPerHour FROM payroll";
    $sqlResult = mysqli_query($this->getLink(), $sql);

    if(mysqli_num_rows($sqlResult) > 0) {
      while($row = mysqli_fetch_array($sqlResult)) {
        $totalHours += intval($row['totalHours']);
        $totalDaysPerWeek += intval($row['totalDaysPerWeek']);
        $totalPayPerHour += floatval($row['totalPayPerHour']);
      }
    }

     $sqlTwo = $this->getLink()->prepare("INSERT INTO payroll (DateCreated, hoursWorked, daysPerWeek, payPerHour)
     VALUES (?, ?, ?, ?)");
     $sqlTwo->bind_param('siid', $dateNow, $hoursWorked, $daysPerWeek, $payPerHour);

     $dateNow = date("Y-m-d H:i:s");
     $hoursWorked = $this->getHourWorked() - $totalHours;
     $daysPerWeek = $this->getDaysPerWeek() - $totalDaysPerWeek;
     $payPerHour = $this->getPayPerHour() - $totalPayPerHour;

     $sqlTwo->execute();

     header("location: ../");
  }
}
$config = new Config();
$link = $config->connectToServer();

$changePaycheckInfo = new ChangePaycheckInfo();
$changePaycheckInfo->setLink($link);
$changePaycheckInfo->setHourWorked(intval($_POST['hoursWorked']));
$changePaycheckInfo->setDaysPerWeek(intval($_POST['daysPerWeek']));
$changePaycheckInfo->setPayPerHour(floatval($_POST['payPerHour']));
$changePaycheckInfo->changePaycheckInfo();
