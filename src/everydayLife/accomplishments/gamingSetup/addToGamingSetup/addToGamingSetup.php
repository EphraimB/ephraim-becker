<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class AddToGamingSetup
{
  private $isAdmin;
  private $link;
  private $component;
  private $model;
  private $price;

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

  function setModel($model): void
  {
    $this->model = $model;
  }

  function getModel(): string
  {
    return $this->model;
  }

  function setPrice($price): void
  {
    $this->price = $price;
  }

  function getPrice(): float
  {
    return $this->price;
  }

  function addToGamingSetup(): void
  {
    $sql = $this->getLink()->prepare("INSERT INTO GamingSetup (Component, Model, Price, DateCreated, DateModified) VALUES (?, ?, ?, ?, ?)");
    $sql->bind_param('ssdss', $component, $model, $price, $dateNow, $dateNow);

    $component = $this->getComponent();
    $model = $this->getModel();
    $price = $this->getPrice();

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
$addToGamingSetup->setModel($_POST['model']);
$addToGamingSetup->setPrice(floatval($_POST['price']));

$addToGamingSetup->addToGamingSetup();
?>
