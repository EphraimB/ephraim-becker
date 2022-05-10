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
    $html = '';

    if($transactions > 0) {
      $sqlTwo = "SELECT * FROM MealPlan";
      $sqlTwoResult = mysqli_query($this->getLink(), $sqlTwo);

        while($row = mysqli_fetch_array($sqlTwoResult)) {
          $id = $row['MealPlanId'];
          $mealId = $row['MealId'];
          $mealItem = $row['MealItem'];
          $mealPrice = $row['MealPrice'];
          $mealDate = $row['MealDate'];

          $html .= '
          <div class="list">
            <div class="row">
              <p style="font-weight: bold;">' . $mealItem . '&nbsp;</p>
              <br />
              <p>$' . $mealPrice . '&nbsp;</p>';

              if(!is_null($end)) {
                $html .= '<p>-' . $end . '&nbsp;</p>';
              }

              switch ($mealId) {
                case 0:
                  $html .= '<p>Breakfast</p>';
                  break;
                case 1:
                  $html .= '<p>Lunch</p>';
                  break;
                case 2:
                  $html .= '<p>Supper</p>';
                  break;
                default:
                  $html .= '<p></p>';
                }

          $html .= '</div>
            <ul class="row actionButtons">
              <li><a class="edit" href="editFood/index.php?id=' . $id . '">Edit Food</a></li>
              <li><a class="delete" href="confirmation.php?id=' . $id . '">Delete Food</a></li>
            </ul>
          </div>';
        }
    }

    if($html == '') {
      $html .= '<p>No meal plans :(</p>';
    }

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
