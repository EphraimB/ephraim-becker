<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class CommutePlan extends Base
{
  private $isAdmin;
  private $link;

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

  function getCurrentBalance(): array
  {
    $sql = "SELECT (SELECT SUM(DepositAmount) from deposits) - (SELECT SUM(WithdrawalAmount) FROM withdrawals) AS currentBalance";
    $sqlResult = mysqli_query($this->getLink(), $sql);

    if(mysqli_num_rows($sqlResult) > 0) {
      while($row = mysqli_fetch_array($sqlResult)){
        $currentBalance = floatval($row['currentBalance']);
      }
    }

    if(is_null($currentBalance)) {
      $currentBalance = 0.00;
    }

    return array(mysqli_num_rows($sqlResult), $currentBalance);
  }

  function displayCurrentBalance($currentBalance): string
  {
    $html = '<h2>Current balance: $' . $currentBalance . '</h2>';

    return $html;
  }

  function addMealPlanButton(): string
  {
    $html = '
    <div class="row">
        <ul class="subNav">
          <li><a style="text-decoration: none;" href="addMealPlan/">+</a></li>
        </ul>
      </div>';

    return $html;
  }

  function showCommute($transactions): string
  {
      $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Shabbat'];
      $index = 0;
      $commutePlan = array();

      $sqlTwo = "SELECT * FROM CommutePlan";
      $sqlTwoResult = mysqli_query($this->getLink(), $sqlTwo);

      while($row = mysqli_fetch_array($sqlTwoResult)) {
        $id = $row['CommutePlanId'];
        $commuteDayId = $row['CommuteDayId'];
        $commutePeriodId = $row['CommutePeriodId'];
        $zoneOfTransportation = $row['zoneOfTransportation'];
        $price = $row['Price'];

    return $html;
  }

  function main(): string
  {
    $transactions = $this->getCurrentBalance()[0];
    $currentBalance = $this->getCurrentBalance()[1];
    $html = $this->displayCurrentBalance($currentBalance);
    $html .= '<br />';
    $html .= $this->addMealPlanButton();
    $html .= $this->showMealPlanTable($transactions);

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$commutePlan = new CommutePlan();
$commutePlan->setLink($link);
$commutePlan->setTitle("Ephraim Becker - Budgeting - Expenses - Commute plan");
$commutePlan->setLocalStyleSheet('css/style.css');
$commutePlan->setLocalScript('js/script.js');
$commutePlan->setHeader('Budgeting - Expenses - Commute plan');
$commutePlan->setUrl($_SERVER['REQUEST_URI']);
$commutePlan->setBody($commutePlan->main());

$commutePlan->html();
