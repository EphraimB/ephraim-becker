<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class EditFoodForm extends Base
{
  private $isAdmin;
  private $link;
  private $id;

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

  function displayMoneyOwedForm(): string
  {
    $sqlTwo = $this->getLink()->prepare("SELECT * FROM MealPlan WHERE MealPlanId=?");
    $sqlTwo->bind_param("i", $id);

    $id = $this->getId();

    $sqlTwo->execute();

    $sqlTwoResult = $sqlTwo->get_result();

    while($row = mysqli_fetch_array($sqlTwoResult)){
      $mealId = $row['MealId'];
      $mealItem = $row['MealItem'];
      $mealPrice = $row['MealPrice'];
      $mealDayId = $row['MealDayId'];
    }

    $html = '
      <form action="editFood.php" method="post">
        <div>
          <label for="food">Food:</label>
          <input type="text" name="mealItem" value="' . $mealItem . '" />
        </div>
        <br />
        <div>
          <label for="foodPrice">Food price:</label>
          $<input type="number" step="any" name="mealPrice" value="' . $mealPrice . '" />
        </div>
        <br />
        <p>Day</p>
        <div>
          <select name="mealDayId" id="day">
            <option value="0"';

            if($mealDayId == 0) {
              $html .= ' selected';
            }
            $html .= '>Sunday</option>
            <option value="1"';

            if($mealDayId == 1) {
              $html .= ' selected';
            }
            $html .= '>Monday</option>
            <option value="2"';

            if($mealDayId == 2) {
              $html .= ' selected';
            }
            $html .= '>Tuesday</option>
            <option value="3"';

            if($mealDayId == 3) {
              $html .= ' selected';
            }
            $html .= '>Wednesday</option>
            <option value="4"';

            if($mealDayId == 4) {
              $html .= ' selected';
            }
            $html .= '>Thursday</option>
            <option value="5"';

            if($mealDayId == 5) {
              $html .= ' selected';
            }
            $html .= '>Friday</option>
            <option value="6"';

            if($mealDayId == 6) {
              $html .= ' selected';
            }
            $html .= '>Shabbat</option>
          </select>
        </div>
        <br />
        <p>Meal</p>
        <div>
          <select name="mealId" id="meal">
            <option value="0"';

            if($mealId == 0) {
              $html .= ' selected';
            }
            $html .= '>Breakfast</option>
            <option value="1"';

            if($mealId == 1) {
              $html .= ' selected';
            }
            $html .= '>Lunch</option>
            <option value="2"';

            if($mealId == 2) {
              $html .= ' selected';
            }
            $html .= '>Supper</option>
          </select>
        </div>
        <br />
        <input type="hidden" value="' . $_GET['id'] . '" name="id">
        <input type="submit" value="Edit food" />
      </form>
    ';

    return $html;
  }

  function main(): string
  {
    $html = $this->displayCurrentBalance();
    $html .= $this->displayMoneyOwedForm();

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$editFoodForm = new EditFoodForm();
$editFoodForm->setLink($link);
$editFoodForm->setId(intval($_GET['id']));
$editFoodForm->setTitle("Ephraim Becker - Budgeting - Edit food form");
$editFoodForm->setLocalStyleSheet('css/style.css');
$editFoodForm->setLocalScript('js/script.js');
$editFoodForm->setHeader('Budgeting - Edit food form');
$editFoodForm->setUrl($_SERVER['REQUEST_URI']);
$editFoodForm->setBody($editFoodForm->main());

$editFoodForm->html();
