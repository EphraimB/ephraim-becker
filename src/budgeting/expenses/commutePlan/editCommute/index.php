<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class EditCommuteForm extends Base
{
  private $isAdmin;
  private $link;
  private $id;

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

  function setId($id): void
  {
    $this->id = $id;
  }

  function getId(): int
  {
    return $this->id;
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

  function editCommuteForm(): string
  {
    $sqlTwo = $this->getLink()->prepare("SELECT * FROM CommutePlan WHERE CommutePlanId=?");
    $sqlTwo->bind_param("i", $id);

    $id = $this->getId();

    $sqlTwo->execute();

    $sqlTwoResult = $sqlTwo->get_result();

    while($row = mysqli_fetch_array($sqlTwoResult)){
      $commuteDayId = $row['CommuteDayId'];
      $commutePeriodId = $row['CommutePeriodId'];
      $peakId = $row['PeakId'];
      $zoneOfTransportation = $row['ZoneOfTransportation'];
    }

    $html = '
      <form action="editCommute.php" method="post">
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
          <input type="checkbox" id="peakId" name="peakId" value="1"';

          if($peakId == 1) {
            $html .= ' checked';
          }

          $html .= '>
        </div>
        <p>Day</p>
        <div>
          <select name="mealDayId" id="day">
            <option value="0"';

            if($commuteDayId == 0) {
              $html .= ' selected';
            }
            $html .= '>Sunday</option>
            <option value="1"';

            if($commuteDayId == 1) {
              $html .= ' selected';
            }
            $html .= '>Monday</option>
            <option value="2"';

            if($commuteDayId == 2) {
              $html .= ' selected';
            }
            $html .= '>Tuesday</option>
            <option value="3"';

            if($commuteDayId == 3) {
              $html .= ' selected';
            }
            $html .= '>Wednesday</option>
            <option value="4"';

            if($commuteDayId == 4) {
              $html .= ' selected';
            }
            $html .= '>Thursday</option>
            <option value="5"';

            if($commuteDayId == 5) {
              $html .= ' selected';
            }
            $html .= '>Friday</option>
            <option value="6"';

            if($commuteDayId == 6) {
              $html .= ' selected';
            }
            $html .= '>Shabbat</option>
          </select>
        </div>
        <br />
        <p>Commute period</p>
        <div>
          <select name="commutePeriodId" id="commutePeriod">
            <option value="0"';

            if($commutePeriodId == 0) {
              $html .= ' checked';
            }
            $html .= '>Morning</option>
            <option value="1"';

            if($commutePeriodId == 1) {
              $html .= ' checked';
            }
            $html .= '>Afternoon</option>
            <option value="2"';

            if($commutePeriodId == 2) {
              $html .= ' checked';
            }
            $html .= '>Evening</option>
          </select>
        </div>
        <br />
        <input type="hidden" name="price" id="price" />
        <input type="submit" value="Edit commute" />
      </form>
    ';

    return $html;
  }

  function main(): string
  {
    $html = $this->displayCurrentBalance();
    $html .= $this->editCommuteForm();

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$editCommuteForm = new EditCommuteForm();
$editCommuteForm->setLink($link);
$editCommuteForm->setTitle("Ephraim Becker - Budgeting - Edit commute form");
$editCommuteForm->setLocalStyleSheet('css/style.css');
$editCommuteForm->setLocalScript('js/script.js');
$editCommuteForm->setHeader('Budgeting - Edit commute form');
$editCommuteForm->setUrl($_SERVER['REQUEST_URI']);
$editCommuteForm->setId(intval($_GET['id']));
$editCommuteForm->setBody($editCommuteForm->main());

$editCommuteForm->html();
