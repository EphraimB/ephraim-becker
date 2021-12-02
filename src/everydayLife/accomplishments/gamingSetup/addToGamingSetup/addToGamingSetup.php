<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class AddToGamingSetup
{
  private $isAdmin;
  private $link;
  private $component;
  private $originalModel;
  private $originalModelPrice;

  function __construct()
  {
    $this->setIsAdmin();

    if(!$this->getIsAdmin()) {
      header("location: ../");
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

  function setComponent($component): void
  {
    $this->component = $component;
  }

  function getComponent(): string
  {
    return $this->component;
  }

  function setOriginalModel($originalModel): void
  {
    $this->originalModel = $originalModel;
  }

  function getOriginalModel(): string
  {
    return $this->originalModel;
  }

  function setOriginalModelPrice($originalModelPrice): void
  {
    $this->originalModelPrice = $originalModelPrice;
  }

  function getOriginalModelPrice(): float
  {
    return $this->originalModelPrice;
  }

  function addToGamingSetup(): void
  {
    $sql = $this->getLink()->prepare("INSERT INTO GamingSetup (Component, OriginalModel, OriginalModelPrice, DateCreated, DateModified) VALUES (?, ?, ?, ?, ?)");
    $sql->bind_param('ssdss', $component, $originalModel, $originalModelPrice, $dateNow, $dateNow);

    $component = $this->getComponent();
    $originalModel = $this->getOriginalModel();
    $originalModelPrice = $this->getOriginalModelPrice();

    $dateNow = date("Y-m-d H:i:s");

    $sql->execute();

    header("location: ../");
  }
}

$config = new Config();
$link = $config->connectToServer();

$addToGamingSetup = new AddToGamingSetup();
$addToGamingSetup->setLink($link);
$addToGamingSetup->setComponent($_POST['component']);
$addToGamingSetup->setOriginalModel($_POST['originalModel']);
$addToGamingSetup->setOriginalModelPrice(floatval($_POST['originalModelPrice']));

$addToGamingSetup->addToGamingSetup();
?>
