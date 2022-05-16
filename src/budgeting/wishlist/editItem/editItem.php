<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditItem
{
  private $isAdmin;
  private $link;
  private $item;
  private $url;
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

  function setItem($item): void
  {
    $this->item = $item;
  }

  function getItem(): string
  {
    return $this->item;
  }

  function setUrl($url): void
  {
    $this->url = $url;
  }

  function getUrl(): string
  {
    return $this->url;
  }

  function setPrice($price): void
  {
    $this->price = $price;
  }

  function getPrice(): float
  {
    return $this->price;
  }

  function setId($id): void
  {
    $this->id = $id;
  }

  function getId(): int
  {
    return $this->id;
  }

  function editItem(): void
  {
    $sql = $this->getLink()->prepare("UPDATE WantToBuy SET Item=?, Link=?, Price=?, DateModified=? WHERE WantToBuyId=?");
    $sql->bind_param('ssdsi', $item, $url, $price, $now, $id);

    $now = date("Y-m-d H:i:s");
    $item = $this->getItem();
    $url = $this->getUrl();
    $price = $this->getPrice();
    $id = $this->getId();

    $sql->execute();

    $sql->close();

    header("location: ../");
  }
}
$config = new Config();
$link = $config->connectToServer();

$editItem = new EditItem();
$editItem->setLink($link);
$editItem->setItem($_POST['item']);
$editItem->setUrl($_POST['link']);
$editItem->setPrice(floatval($_POST['price']));
$editItem->setId(intval($_POST['id']));

$editItem->editItem();
