<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class AddFood
{
  private $isAdmin;
  private $link;
  private $cronTabManager;
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

  function setCronTabManager($cronTabManager)
  {
    $this->cronTabManager = $cronTabManager;
  }

  function getCronTabManager()
  {
    return $this->cronTabManager;
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

  function getMealHour($mealId): int
  {
    $mealHour = 0;

    if($mealId == 0) {
      $mealHour = 8;
    } else if($mealId == 1) {
      $mealHour = 12;
    }
    else if($mealId == 2) {
     $mealHour = 17;
   }

   return $mealHour;
  }

  function addCronJobToDB($uniqueId, $command): int
  {
    $sql = $this->getLink()->prepare("INSERT INTO CronJobs (UniqueId, Command, DateCreated, DateModified)
     VALUES (?, ?, ?, ?)");
     $sql->bind_param('ssss', $uniqueId, $command, $dateNow, $dateNow);

     $dateNow = date("Y-m-d H:i:s");

     $sql->execute();

     $sqlTwo = "SELECT LAST_INSERT_ID() AS id";
     $sqlTwoResult = mysqli_query($this->getLink(), $sqlTwo);

     while($row = mysqli_fetch_array($sqlTwoResult)) {
       $id = intval($row['id']);
     }

     return $id;
  }

  function addFood(): string
  {
    $sql = $this->getLink()->prepare("INSERT INTO MealPlan (CronJobId, MealId, MealItem, MealPrice, MealDayId, DateCreated, DateModified)
     VALUES (?, ?, ?, ?, ?, ?, ?)");
     $sql->bind_param('iisdiss', $cronJobId, $mealId, $mealItem, $mealPrice, $mealDayId, $dateNow, $dateNow);

     $dateNow = date("Y-m-d H:i:s");
     $mealId = $this->getMealId();
     $mealItem = $this->getMealItem();
     $mealPrice = $this->getMealPrice();
     $mealDayId = $this->getMealDayId();

     $uniqueId = uniqid();
     $command = '0 ' . $this->getMealHour($mealId) . ' * * ' . $this->getMealDayId() . ' /usr/local/bin/php /home/s8gphl6pjes9/public_html/budgeting/cron/withdrawalCronJob.php withdrawalAmount=' . $mealPrice . ' withdrawalDescription=Meal\ Expenses id=' . $uniqueId;
     $cronJobId = $this->addCronJobToDB($uniqueId, $command);

     $sql->execute();

     $crontab = $this->getCronTabManager();
     $crontab->append_cronjob($command);

     $sql->close();
     $this->getLink()->close();

     header("location: ../");
  }
}
$config = new Config();
$link = $config->connectToServer();
$cronTabManager = $config->connectToCron();

$addFood = new AddFood();
$addFood->setLink($link);
$addFood->setCronTabManager($cronTabManager);
$addFood->setMealId(intval($_POST['mealId']));
$addFood->setMealItem($_POST['mealItem']);
$addFood->setMealPrice(floatval($_POST['mealPrice']));
$addFood->setMealDayId(intval($_POST['mealDayId']));
$addFood->addFood();
