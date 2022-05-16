<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class AddToWishlist
{
  private $isAdmin;
  private $link;
  private $item;
  private $url;
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

  function addToWishlist(): void
  {
    $sql = $this->getLink()->prepare("INSERT INTO WantToBuy (Item, Link, Price, DateCreated, DateModified)
    VALUES (?, ?, ?, ?, ?)");
    $sql->bind_param('ssdss', $item, $url, $price, $now, $now);

    $now = date("Y-m-d H:i:s");
    $item = $this->getItem();
    $url = $this->getUrl();
    $price = $this->getPrice();

    $sql->execute();

    $sql->close();

    header("location: ../");
  }
}
$config = new Config();
$link = $config->connectToServer();

$addToWishlist = new AddToWishlist();
$addToWishlist->setLink($link);
$addToWishlist->setItem($_POST['item']);
$addToWishlist->setUrl($_POST['link']);
$addToWishlist->setPrice(floatval($_POST['price']));

$addToWishlist->addToWishlist();
