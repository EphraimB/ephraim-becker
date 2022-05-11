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

  function addCommuteButton(): string
  {
    $html = '
    <div class="row">
        <ul class="subNav">
          <li><a style="text-decoration: none;" href="addCommute/">+</a></li>
        </ul>
      </div>';

    return $html;
  }

  // function fetchLIRRData(): string
  // {
  //   $url = " https://api-endpoint.mta.info/Dataservice/mtagtfsfeeds/lirr%2Fgtfs-lirr";
  //
  //   // $data = [
  //   //
  //   // ];
  //
  //   $curl = curl_init($url);
  //
  //   // 1. Set the CURLOPT_RETURNTRANSFER option to true
  //   curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  //   // 2. Set the CURLOPT_POST option to true for POST request
  //   curl_setopt($curl, CURLOPT_POST, true);
  //   // 3. Set the request data as JSON using json_encode function
  //   // curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($data));
  //   // 4. Set custom headers for RapidAPI Auth and Content-Type header
  //   curl_setopt($curl, CURLOPT_HTTPHEADER, [
  //     'X-RapidAPI-Host: https://api-endpoint.mta.info',
  //     'X-RapidAPI-Key: yRw2Up22em4ygQmhAvbww3wIOZTQ0dQtFDge1U89',
  //     'Content-Type: application/json'
  //   ]);
  //
  //   // Execute cURL request with all previous settings
  //   $response = curl_exec($curl);
  //
  //   // Close cURL session
  //   curl_close($curl);
  //
  //   return $response . PHP_EOL;
  // }

  function showCommute($transactions): string
  {
      $html = '';
      $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Shabbat'];
      $index = 0;
      $commutePlan = array();

      $sqlTwo = "SELECT * FROM CommutePlan";
      $sqlTwoResult = mysqli_query($this->getLink(), $sqlTwo);

      $html = '<table id="commute-plan-table">
      <tr>
        <th></th>
        <th>Morning</th>
        <th>Afternoon</th>
        <th>Evening</th>
      </tr>';

      while($row = mysqli_fetch_array($sqlTwoResult)) {
        $id = $row['CommutePlanId'];
        $commuteDayId = $row['CommuteDayId'];
        $commutePeriodId = $row['CommutePeriodId'];
        $peakId = $row['PeakId'];
        $zoneOfTransportation = $row['zoneOfTransportation'];
        $price = $row['Price'];

        array_push($commutePlan, array(
          "id" => $id,
          "commuteDayId" => $commuteDayId,
          "commutePeriodId" => $commutePeriodId,
          "peakId" => $peakId,
          "zoneOfTransportation" => $zoneOfTransportation,
          "price" => $price
        ));
      }

      for($i = 0; $i < count($days); $i++) {
          $html .= '
            <tr>
              <td>' . $days[$i] . '</td>
              <td class="fixed-width">';
              for($j = 0; $j < count($commutePlan); $j++) {
                if($i == $commutePlan[$j]["commuteDayId"] && $commutePlan[$j]["commutePeriodId"] == 0) {
                  $html .= '<div class="foodItem" onclick="showActionButtons(this)">
                    <a class="edit" href="editCommute/index.php?id=' . $commutePlan[$j]['id'] . '">Edit</a>
                    Zone' . $commutePlan[$j]["zoneOfTransportation"] . ' - $' . $commutePlan[$j]["price"] . '
                    <a class="delete" href="confirmationCommute.php?id=' . $commutePlan[$j]['id'] . '">Delete</a>
                  </div>';
            }
          }

          $html .= '</td>
          <td class="fixed-width">';

          for($j = 0; $j < count($commutePlan); $j++) {
            if($i == $commutePlan[$j]["commuteDayId"] && $commutePlan[$j]["commutePeriodId"] == 1) {
              $html .= '<div class="foodItem" onclick="showActionButtons(this)">
                <a class="edit" href="editCommute/index.php?id=' . $commutePlan[$j]['id'] . '">Edit</a>
                  Zone' . $commutePlan[$j]["zoneOfTransportation"] . ' - $' . $commutePlan[$j]["price"] . '
                <a class="delete" href="confirmationCommute.php?id=' . $commutePlan[$j]['id'] . '">Delete</a>
              </div>';
            }
          }

        $html .= '</td>
        <td class="fixed-width">';

        for($j = 0; $j < count($commutePlan); $j++) {
          if($i == $commutePlan[$j]["commuteDayId"] && $commutePlan[$j]["commutePeriodId"] == 2) {
            $html .= '<div class="foodItem" onclick="showActionButtons(this)">
            <a class="edit" href="editCommute/index.php?id=' . $commutePlan[$j]['id'] . '">Edit</a>
            Zone' . $commutePlan[$j]["zoneOfTransportation"] . ' - $' . $commutePlan[$j]["price"] . '
            <a class="delete" href="confirmationCommute.php?id=' . $commutePlan[$j]['id'] . '">Delete</a>
          </div>';
        }
      }

      $html .= '</td>';
      $html .= '</tr>';
    }

    $html .= '</table>';

    return $html;
  }

  function main(): string
  {
    $transactions = $this->getCurrentBalance()[0];
    $currentBalance = $this->getCurrentBalance()[1];
    $html = $this->displayCurrentBalance($currentBalance);
    $html .= '<br />';
    // $html .= $this->fetchLIRRData();
    $html .= $this->addCommuteButton();
    $html .= $this->showCommute($transactions);

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
