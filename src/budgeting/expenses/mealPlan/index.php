<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class MealPlan extends Base
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

  function showMealPlanTable($transactions): string
  {
      $index = 0;
      $mealPlan = array();

      $sqlTwo = "SELECT * FROM MealPlan";
      $sqlTwoResult = mysqli_query($this->getLink(), $sqlTwo);

      $html = '<table>
      <tr>
        <th></th>
        <th>Breakfast</th>
        <th>Lunch</th>
        <th>Supper</th>
        <th>Actions</th>
      </tr>';

      while($row = mysqli_fetch_array($sqlTwoResult)) {
        $id = $row['MealPlanId'];
        $mealId = $row['MealId'];
        $mealItem = $row['MealItem'];
        $mealPrice = $row['MealPrice'];
        $mealDayId = $row['MealDayId'];

        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Shabbat'];

        array_push($mealPlan, array(
          "id" => $id,
          "mealId" => $mealId,
          "mealItem" => $mealItem,
          "mealPrice" => $mealPrice,
          "mealDayId" => $mealDayId
        ));
    }

    for($i = 0; $i < count($days); $i++) {
        $html .= '
          <tr>
            <td>' . $days[$i] . '</td>
            <td>';
            for($j = 0; $j < count($mealPlan); $j++) {
              if($i == $mealPlan[$j]["mealDayId"] && $mealPlan[$j]["mealId"] == 0) {
                $html .= '<p>' . $mealPlan[$j]["mealItem"] . ' - $' . $mealPlan[$j]["mealPrice"] . '</p>';
              }
            }

            $html .= '</td>
            <td>';

            for($j = 0; $j < count($mealPlan); $j++) {
              if($i == $mealPlan[$j]["mealDayId"] && $mealPlan[$j]["mealId"] == 1) {
                $html .= '<p>' . $mealPlan[$j]["mealItem"] . ' - $' . $mealPlan[$j]["mealPrice"] . '</p>';
              }
            }

            $html .= '</td>
            <td>';

            for($j = 0; $j < count($mealPlan); $j++) {
              if($i == $mealPlan[$j]["mealDayId"] && $mealPlan[$j]["mealId"] == 2) {
                $html .= '<p>' . $mealPlan[$j]["mealItem"] . ' - $' . $mealPlan[$j]["mealPrice"] . '</p>';
              }
            }

            $html .= '</td>';

            $html .= '<td class="actionButtons">
              <a class="edit" href="editDay/index.php?mealDayId=' . $i . '">Edit</a>
              <a class="delete" href="confirmationDay/index.php?mealDayId=' . $i . '">Delete</a>
            </td>';

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
    $html .= $this->addMealPlanButton();
    $html .= $this->showMealPlanTable($transactions);

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$mealPlan = new MealPlan();
$mealPlan->setLink($link);
$mealPlan->setTitle("Ephraim Becker - Budgeting - Expenses - Meal plan");
$mealPlan->setLocalStyleSheet('css/style.css');
$mealPlan->setLocalScript(NULL);
$mealPlan->setHeader('Budgeting - Expenses - Meal plan');
$mealPlan->setUrl($_SERVER['REQUEST_URI']);
$mealPlan->setBody($mealPlan->main());

$mealPlan->html();
