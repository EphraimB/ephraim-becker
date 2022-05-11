<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class AddCommuteForm extends Base
{
  private $isAdmin;
  private $link;

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

  function displayCurrentBalance(): string
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

    $html = '<h2>Current balance: $' . $currentBalance . '</h2>';

    return $html;
  }

  function addFoodForm(): string
  {
    $html = '
      <form action="addCommute.php" method="post">
        <p>Zone</p>
        <div>
          <select name="zoneId" id="zoneId">
            <option value="0">NYC Subway</option>
            <option value="1">Zone 1 LIRR</option>
            <option value="2">Riverdale area - Metro North</option>
            <option value="3">Zone 3 LIRR</option>
            <option value="4">Zone 4 LIRR</option>
            <option value="5">White Plains area - Metro North</option>
            <option value="7">Zone 7 LIRR</option>
          </select>
        </div>
        <br />
        <div>
          <label for"peakId">Peak</label>
          <input type="checkbox" id="peakId" name="peakId" value="1">
        </div>
        <p>Day</p>
        <div>
          <select name="commuteDayId" id="day">
            <option value="0">Sunday</option>
            <option value="1">Monday</option>
            <option value="2">Tuesday</option>
            <option value="3">Wednesday</option>
            <option value="4">Thursday</option>
            <option value="5">Friday</option>
            <option value="6">Shabbat</option>
          </select>
        </div>
        <br />
        <p>Commute period</p>
        <div>
          <select name="commutePeriodId" id="commutePeriod">
            <option value="0">Morning</option>
            <option value="1">Afternoon</option>
            <option value="2">Evening</option>
          </select>
        </div>
        <br />
        <input type="hidden" name="price" id="price" />
        <input type="submit" value="Add commute" />
      </form>
    ';

    return $html;
  }

  function main(): string
  {
    $html = $this->displayCurrentBalance();
    $html .= $this->addFoodForm();

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$addCommuteForm = new AddCommuteForm();
$addCommuteForm->setLink($link);
$addCommuteForm->setTitle("Ephraim Becker - Budgeting - Add commute form");
$addCommuteForm->setLocalStyleSheet('css/style.css');
$addCommuteForm->setLocalScript('js/script.js');
$addCommuteForm->setHeader('Budgeting - Add commute form');
$addCommuteForm->setUrl($_SERVER['REQUEST_URI']);
$addCommuteForm->setBody($addCommuteForm->main());

$addCommuteForm->html();
