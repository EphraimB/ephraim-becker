<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditFood
{
  private $isAdmin;
  private $link;
  private $cronTabManager;
  private $id;
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

  function setId($id): void
  {
    $this->id = $id;
  }

  function getId(): int
  {
    return $this->id;
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

  function getCronJobId(): int
  {
    $sql = $this->getLink()->prepare("SELECT CronJobId FROM MealPlan WHERE MealPlanId=?");
    $sql->bind_param('i', $id);

    $id = $this->getId();

    $sql->execute();

    $sqlResult = $sql->get_result();

    while($row = mysqli_fetch_array($sqlResult)) {
      $id = $row['CronJobId'];
    }

    return $id;
  }

  function getCronJobUniqueId(): string
  {
    $sql = $this->getLink()->prepare("SELECT UniqueId FROM CronJobs WHERE CronJobId=?");
    $sql->bind_param('i', $id);

    $id = $this->getCronJobId();

    $sql->execute();

    $sqlResult = $sql->get_result();

    while($row = mysqli_fetch_array($sqlResult)) {
      $uniqueId = $row['UniqueId'];
    }

    return $uniqueId;
  }

  function updateCronJobInDB($command): void
  {
     $sql = $this->getLink()->prepare("UPDATE CronJobs SET Command=?, DateModified=? WHERE CronJobId=?");
     $sql->bind_param('ssi', $command, $dateNow, $cronJobId);

     $dateNow = date("Y-m-d H:i:s");
     $cronJobId = $this->getCronJobId();

     $sql->execute();
  }

  function editFood(): string
  {
     $sql = $this->getLink()->prepare("UPDATE MealPlan SET MealId=?, MealItem=?, MealPrice=?, MealDayId=?, DateModified=? WHERE MealPlanId=?");
     $sql->bind_param('isdisi', $mealId, $mealItem, $mealPrice, $mealDayId, $dateNow, $id);

     $dateNow = date("Y-m-d H:i:s");
     $mealId = $this->getMealId();
     $mealItem = $this->getMealItem();
     $mealPrice = $this->getMealPrice();
     $mealDayId = $this->getMealDayId();
     $id = $this->getId();

     $uniqueId = $this->getCronJobUniqueId();
     $command = '0 ' . $this->getMealHour($mealId) . ' * * ' . $this->getMealDayId() . ' /usr/local/bin/php /home/s8gphl6pjes9/public_html/budgeting/cron/withdrawalCronJob.php withdrawalAmount=' . $mealPrice . ' withdrawalDescription=Meal\ Expenses id=' . $uniqueId;
     $this->updateCronJobInDB($command);

     $sql->execute();

     $crontab = $this->getCronTabManager();
     $crontab->remove_cronjob('/' . $uniqueId . '/');
     $crontab->append_cronjob($command);

     $sql->close();
     $this->getLink()->close();

     header("location: ../");
  }
}
$config = new Config();
$link = $config->connectToServer();
$cronTabManager = $config->connectToCron();

$editFood = new EditFood();
$editFood->setLink($link);
$editFood->setCronTabManager($cronTabManager);
$editFood->setMealId(intval($_POST['mealId']));
$editFood->setMealItem($_POST['mealItem']);
$editFood->setMealPrice(floatval($_POST['mealPrice']));
$editFood->setMealDayId(intval($_POST['mealDayId']));
$editFood->setId(intval($_POST['id']));
$editFood->editFood();
