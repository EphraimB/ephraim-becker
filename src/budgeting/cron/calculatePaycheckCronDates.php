<?php
declare(strict_types=1);

session_start();

$home = getenv('HOME');

require_once($home . '/config.php');

class CalculatePaycheckCronDates
{
  private $link;
  private $cronTabManager;
  private $cronJobUniqueId;

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

  function setCronTabManager($cronTabManager)
  {
    $this->cronTabManager = $cronTabManager;
  }

  function getCronTabManager()
  {
    return $this->cronTabManager;
  }

  function paycheckQuery(): string
  {
    $grossPay = "SELECT SUM(payPerHour) * SUM(hoursWorked) * SUM(daysPerWeek) * 2.167";
    $beginYear = "YEAR(CURDATE())";
    $beginMonth = "MONTH(CURDATE())";
    $caseQuery = "CASE WEEKDAY(concat(" . $beginYear . ", '-', " . $beginMonth . ", '-', payrollDay)) WHEN 5 THEN 1 WHEN 6 THEN 2 ELSE 0 END DAY))";

    $query = "SELECT 'Paycheck' AS title, (" . $grossPay . " - (SELECT SUM(taxAmount) FROM payrollTaxes WHERE fixed = 1) - (SELECT SUM(taxAmount) * (" . $grossPay . " FROM payroll) FROM payrollTaxes WHERE fixed = 0) FROM payroll) AS amount, " . $beginYear . " AS year, " . $beginMonth . " AS month, IF(PayrollDay = 31, DAY(date_sub(LAST_DAY(concat(" . $beginYear . ", '-', " . $beginMonth . ", '-', 1)), INTERVAL " . $caseQuery . ", DAY(date_sub(concat(" . $beginYear . ", '-', " . $beginMonth . ", '-', payrollDay), INTERVAL " . $caseQuery . ") AS day, 0 AS frequency, 0 AS type FROM payrollDates";

    return $query;
  }

  function addPayrollToCronJob(): array
  {
    $index = 0;
    $payrollDays = '';

    $sql = $this->paycheckQuery();
    $sqlResult = mysqli_query($this->getLink(), $sql);

    if(mysqli_num_rows($sqlResult) > 0) {
      while($row = mysqli_fetch_array($sqlResult)) {
        if($index > 0) {
          $payrollDays .= ',';
        }

        $payrollDays .= $row['day'];
        $amount = $row['amount'];

        $index++;
      }
    }

    return array($payrollDays, $amount);
  }

  function setDepositCronJob()
  {
    $paycheckInfo = $this->addPayrollToCronJob();

    $uniqueId = 'paycheck';
    $command = '0 8 ' . $paycheckInfo[0] . ' * * /usr/local/bin/php /home/s8gphl6pjes9/public_html/budgeting/cron/depositCronJob.php depositAmount=' . $paycheckInfo[1] . ' depositDescription=Paycheck id=' . $uniqueId;

    $crontab = $this->getCronTabManager();
    $crontab->remove_cronjob('/id=' . $uniqueId . '/');
    $crontab->append_cronjob($command);
  }
}
$config = new Config();
$link = $config->connectToServer();
$cronTabManager = $config->connectToCron();

$calculatePaycheckCronDates = new CalculatePaycheckCronDates();
$calculatePaycheckCronDates->setLink($link);
$calculatePaycheckCronDates->setCronTabManager($cronTabManager);
$calculatePaycheckCronDates->setDepositCronJob();
