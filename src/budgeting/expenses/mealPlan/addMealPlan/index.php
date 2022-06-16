<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class AddFoodForm extends Base
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
      <form action="addFood.php" method="post">
        <div>
          <label for="food">Food:</label>
          <input type="text" name="mealItem" />
        </div>
        <br />
        <div>
          <label for="foodPrice">Food price:</label>
          $<input type="number" step="any" name="mealPrice" />
        </div>
        <br />
        <p>Day</p>
        <div>
          <select name="mealDayId" id="day">
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
        <p>Meal</p>
        <div>
          <select name="mealId" id="meal">
            <option value="0">Breakfast</option>
            <option value="1">Lunch</option>
            <option value="2">Supper</option>
          </select>
        </div>
        <br />
        <input type="submit" value="Add food" />
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

$addFoodForm = new AddFoodForm();
$addFoodForm->setLink($link);
$addFoodForm->setTitle("Ephraim Becker - Budgeting - Add food form");
$addFoodForm->setLocalStyleSheet('css/style.css');
$addFoodForm->setLocalScript(NULL);
$addFoodForm->setHeader('Budgeting - Add food form');
$addFoodForm->setUrl($_SERVER['REQUEST_URI']);
$addFoodForm->setBody($addFoodForm->main());

$addFoodForm->html();
