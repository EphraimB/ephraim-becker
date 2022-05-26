<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class AddCommute
{
  private $isAdmin;
  private $link;
  private $cronTabManager;
  private $commuteDayId;
  private $commutePeriodId;
  private $peakId;
  private $zoneOfTransportation;
  private $price;

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

  function addCommute(): string
  {
    $sql = $this->getLink()->prepare("INSERT INTO CommutePlan (CommuteDayId, CommutePeriodId, PeakId, ZoneOfTransportation, Price, DateCreated, DateModified)
     VALUES (?, ?, ?, ?, ?, ?, ?)");
     $sql->bind_param('iiiidss', $commuteDayId, $commutePeriodId, $peakId, $zoneOfTransportation, $price, $dateNow, $dateNow);

     $dateNow = date("Y-m-d H:i:s");
     $commuteDayId = $this->getCommuteDayId();
     $commutePeriodId = $this->getCommutePeriodId();
     $peakId = $this->getPeakId();
     $zoneOfTransportation = $this->getZoneOfTransportation();
     $price = $this->getPrice();

     $sql->execute();

     $crontab = $this->getCronTabManager();
     $crontab->append_cronjob('0 12 * * ' . $commuteDayId . '  /usr/local/bin/php /home/s8gphl6pjes9/public_html/budgeting/cron/withdrawalCronJob.php withdrawalAmount=' . $price . ' withdrawalDescription=Commute%20Expenses');

     $sql->close();
     $this->getLink()->close();

     header("location: ../");
  }
}
$config = new Config();
$link = $config->connectToServer();
$cronTabManager = $config->connectToCron();

$addCommute = new AddCommute();
$addCommute->setLink($link);
$addCommute->setCronTabManager($cronTabManager);
$addCommute->setCommuteDayId(intval($_POST['commuteDayId']));
$addCommute->setCommutePeriodId(intval($_POST['commutePeriodId']));

if(isset($_POST['peakId'])) {
  $addCommute->setPeakId(intval($_POST['peakId']));
} else {
  $addCommute->setPeakId(0);
}

$addCommute->setZoneOfTransportation(intval($_POST['zoneId']));
$addCommute->setPrice(floatval($_POST['price']));
$addCommute->addCommute();
