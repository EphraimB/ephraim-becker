<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditCommute
{
  private $isAdmin;
  private $link;
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

     $sql->execute();

     $sql->close();
     $this->getLink()->close();

     header("location: ../");
  }
}
$config = new Config();
$link = $config->connectToServer();

$editCommute = new EditCommute();
$editCommute->setLink($link);
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
