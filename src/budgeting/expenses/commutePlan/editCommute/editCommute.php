<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditCommute
{
  private $isAdmin;
  private $link;
  private $cronTabManager;
  private $commuteDayId;
  private $commutePeriodId;
  private $peakId;
  private $zoneOfTransportation;
  private $price;
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

  function setCommuteDayId($commuteDayId): void
  {
    $this->commuteDayId = $commuteDayId;
  }

  function getCommuteDayId(): int
  {
    return $this->commuteDayId;
  }

  function setCommutePeriodId($commutePeriodId): void
  {
    $this->commutePeriodId = $commutePeriodId;
  }

  function getCommutePeriodId(): int
  {
    return $this->commutePeriodId;
  }

  function setPeakId($peakId): void
  {
    $this->peakId = $peakId;
  }

  function getPeakId(): int
  {
    return $this->peakId;
  }

  function setZoneOfTransportation($zoneOfTransportation): void
  {
    $this->zoneOfTransportation = $zoneOfTransportation;
  }

  function getZoneOfTransportation(): int
  {
    return $this->zoneOfTransportation;
  }

  function setPrice($price): void
  {
    $this->price = $price;
  }

  function getPrice(): float
  {
    return $this->price;
  }

  function getCommuteHour($commutePeriodId): int
  {
    $commuteHour = 0;

    if($commutePeriodId == 0) {
      $commuteHour = 8;
    } else if($commutePeriodId == 1) {
      $commuteHour = 12;
    } else if($commutePeriodId == 2) {
      $commuteHour = 5;
   }

   return $commuteHour;
  }

  function getCronJobId(): int
  {
    $sql = $this->getLink()->prepare("SELECT CronJobId FROM CommutePlan WHERE CommutePlanId=?");
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

  function editCommute(): string
  {
     $sql = $this->getLink()->prepare("UPDATE CommutePlan SET CommuteDayId=?, CommutePeriodId=?, PeakId=?, ZoneOfTransportation=?, Price=?, DateModified=? WHERE CommutePlanId=?");
     $sql->bind_param('iiiidsi', $commuteDayId, $commutePeriodId, $peakId, $zoneOfTransportation, $price, $dateNow, $id);

     $dateNow = date("Y-m-d H:i:s");
     $commuteDayId = $this->getCommuteDayId();
     $commutePeriodId = $this->getCommutePeriodId();
     $peakId = $this->getPeakId();
     $zoneOfTransportation = $this->getZoneOfTransportation();
     $price = $this->getPrice();

     $id = $this->getId();

     $uniqueId = $this->getCronJobUniqueId();
     $command = '0 ' . $this->getCommuteHour($commutePeriodId) . ' * * ' . $commuteDayId . '  /usr/local/bin/php /home/s8gphl6pjes9/public_html/budgeting/cron/withdrawalCronJob.php withdrawalAmount=' . $price . ' withdrawalDescription=Commute\ Expenses id=' . $uniqueId;
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

$editCommute = new EditCommute();
$editCommute->setLink($link);
$editCommute->setCronTabManager($cronTabManager);
$editCommute->setCommuteDayId(intval($_POST['commuteDayId']));
$editCommute->setCommutePeriodId(intval($_POST['commutePeriodId']));

if(isset($_POST['peakId'])) {
  $editCommute->setPeakId(intval($_POST['peakId']));
} else {
  $editCommute->setPeakId(0);
}

$editCommute->setZoneOfTransportation(intval($_POST['zoneId']));
$editCommute->setPrice(floatval($_POST['price']));
$editCommute->setId(intval($_POST['id']));
$editCommute->editCommute();
