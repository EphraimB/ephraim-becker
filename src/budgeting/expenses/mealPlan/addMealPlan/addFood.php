<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

require_once('../../../cron/ssh2_crontab_manager.php');

class AddFood extends Ssh2_crontab_manager
{
  private $isAdmin;
  private $link;
  private $mealId;
  private $mealItem;
  private $mealPrice;
  private $mealDayId;

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

  function setMealId($mealId): void
  {
    $this->mealId = $mealId;
  }

  function getMealId(): int
  {
    return $this->mealId;
  }

  function setMealItem($mealItem): void
  {
    $this->mealItem = $mealItem;
  }

  function getMealItem(): string
  {
    return $this->mealItem;
  }

  function setMealPrice($mealPrice): void
  {
    $this->mealPrice = $mealPrice;
  }

  function getMealPrice(): float
  {
    return $this->mealPrice;
  }

  function setMealDayId($mealDayId): void
  {
    $this->mealDayId = $mealDayId;
  }

  function getMealDayId(): int
  {
    return $this->mealDayId;
  }

  function addFood(): string
  {
    $sql = $this->getLink()->prepare("INSERT INTO MealPlan (MealId, MealItem, MealPrice, MealDayId, DateCreated, DateModified)
     VALUES (?, ?, ?, ?, ?, ?)");
     $sql->bind_param('isdiss', $mealId, $mealItem, $mealPrice, $mealDayId, $dateNow, $dateNow);

     $dateNow = date("Y-m-d H:i:s");
     $mealId = $this->getMealId();
     $mealItem = $this->getMealItem();
     $mealPrice = $this->getMealPrice();
     $mealDayId = $this->getMealDayId();

     $sql->execute();

     $sql->close();
     $this->getLink()->close();

     $crontab = new Ssh2_crontab_manager('173.201.184.58', 22, 'EphraimB', 'Beckboy25');
     $crontab->append_cronjob('30 8 * * 6 home/path/to/command/the_command.sh');

     header("location: ../");
  }
}
$config = new Config();
$link = $config->connectToServer();

$addFood = new AddFood();
$addFood->setLink($link);
$addFood->setMealId(intval($_POST['mealId']));
$addFood->setMealItem($_POST['mealItem']);
$addFood->setMealPrice(floatval($_POST['mealPrice']));
$addFood->setMealDayId(intval($_POST['mealDayId']));
$addFood->addFood();
