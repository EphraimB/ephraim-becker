<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class AddPayrollDay
{
  private $isAdmin;
  private $link;
  private $cronTabManager;
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

  function setCronTabManager($cronTabManager)
  {
    $this->cronTabManager = $cronTabManager;
  }

  function getCronTabManager()
  {
    return $this->cronTabManager;
  }

  function setPaycheckDay($paycheckDay): void
  {
    $this->paycheckDay = $paycheckDay;
  }

  function getPaycheckDay(): int
  {
    return $this->paycheckDay;
  }

  // function addPayrollDaysToCronJob(): string
  // {
  //   $index = 0;
  //   $payrollDays = '';
  //
  //   $sql = "SELECT * FROM payrollDates";
  //   $sqlResult = mysqli_query($this->getLink(), $sql);
  //
  //   if(mysqli_num_rows($sqlResult) > 0) {
  //     while($row = mysqli_fetch_array($sqlResult)) {
  //       if($index > 0) {
  //         $payrollDays .= ',';
  //       }
  //
  //       $payrollDays .= $row['PayrollDay'];
  //
  //       $index++;
  //     }
  //   }
  //
  //   return $payrollDays;
  // }

  function addPayrollDay(): void
  {
    $sql = $this->getLink()->prepare("INSERT INTO payrollDates (DateCreated, PayrollDay)
    VALUES (?, ?)");
    $sql->bind_param('si', $dateNow, $payrollDay);

    $dateNow = date("Y-m-d H:i:s");
    $payrollDay = $this->getPaycheckDay();

    $sql->execute();

    $payrollDays = $this->addPayrollDaysToCronJob();

    // $uniqueId = 'paycheck';
    // $command = '0 8 1 * * /usr/local/bin/php /home/s8gphl6pjes9/public_html/budgeting/cron/calculatePaycheckCronDates.php id=' . $uniqueId;

    // $crontab = $this->getCronTabManager();
    // $crontab->remove_cronjob('/id=' . $uniqueId . '/');
    // $crontab->append_cronjob($command);

    $sql->close();

    header("location: index.php");
  }
}
$config = new Config();
$link = $config->connectToServer();
$cronTabManager = $config->connectToCron();

$addPayrollDay = new AddPayrollDay();
$addPayrollDay->setLink($link);
$addPayrollDay->setCronTabManager($cronTabManager);
$addPayrollDay->setPaycheckDay(intval($_GET['day']));

$addPayrollDay->addPayrollDay();
