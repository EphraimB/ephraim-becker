<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditComponent
{
  private $isAdmin;
  private $link;
  private $id;
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

  function setId($id): void
  {
    $this->id = $id;
  }

  function getId(): int
  {
    return $this->id;
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

  function editComponent(): void
  {
    $sql = $this->getLink()->prepare("UPDATE GamingSetup SET Component = ?, Model = ?, Price = ?, dateModified = ? WHERE GamingSetupId = ?");
    $sql->bind_param('ssdsi', $component, $model, $price, $dateNow, $id);

    $component = $this->getComponent();
    $model = $this->getModel();
    $price = $this->getPrice();

    $dateNow = date("Y-m-d H:i:s");

    $id = $this->getId();

    $sql->execute();

    header("location: ../");
  }
}

$config = new Config();
$link = $config->connectToServer();

$editComponent = new EditComponent();
$editComponent->setLink($link);
$editComponent->setId(intval($_POST['id']));
$editComponent->setComponent($_POST['component']);
$editComponent->setModel($_POST['model']);
$editComponent->setPrice(floatval($_POST['price']));

$editComponent->editComponent();
?>
