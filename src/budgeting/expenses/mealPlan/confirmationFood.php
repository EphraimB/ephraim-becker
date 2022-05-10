<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class ConfirmationFood extends Base
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

  function showConfirmation(): string
  {
    $sql = $this->getLink()->prepare("SELECT * FROM MealPlan WHERE MealPlanId=?");
    $sql->bind_param("i", $id);

    $id = $this->getId();

    $sql->execute();

    $sqlResult = $sql->get_result();

    while($row = mysqli_fetch_array($sqlResult)){
      $mealItem = $row['MealItem'];
    }

    $html = '<h2>Are you sure you want to delete ' . $mealItem . '?</h2>

    <div class="row actionButtons">
      <a class="keep" href="index.php">No</a>
      <a class="delete" href="deleteFood.php?id=' . $id . '">Yes</a>
    </div>';

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$confirmationFood = new ConfirmationFood();
$confirmationFood->setLink($link);
$confirmationFood->setId(intval($_GET['id']));
$confirmationFood->setTitle("Ephraim Becker - Budgeting - Food - Delete?");
$confirmationFood->setLocalStyleSheet('css/style.css');
$confirmationFood->setLocalScript(NULL);
$confirmationFood->setHeader('Budgeting - Food - Delete?');
$confirmationFood->setUrl($_SERVER['REQUEST_URI']);
$confirmationFood->setBody($confirmationFood->showConfirmation());

$confirmationFood->html();
