<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class AddToGames
{
  private $isAdmin;
  private $link;
  private $game;
  private $platform;
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

  function setGame($game): void
  {
    $this->game = $game;
  }

  function getGame(): string
  {
    return $this->game;
  }

  function setPlatform($platform): void
  {
    $this->platform = $platform;
  }

  function getPlatform(): string
  {
    return $this->platform;
  }

  function setPrice($price): void
  {
    $this->price = $price;
  }

  function getPrice(): float
  {
    return $this->price;
  }

  function addToGames(): void
  {
    $sql = $this->getLink()->prepare("INSERT INTO ComputerGames (Game, Platform, Price, DateCreated, DateModified) VALUES (?, ?, ?, ?, ?)");
    $sql->bind_param('ssdss', $game, $platform, $price, $dateNow, $dateNow);

    $game = $this->getGame();
    $platform = $this->getPlatform();
    $price = $this->getPrice();

    $dateNow = date("Y-m-d H:i:s");

    $sql->execute();

    header("location: ../");
  }
}

$config = new Config();
$link = $config->connectToServer();

$addToGames = new AddToGames();
$addToGames->setLink($link);
$addToGames->setGame($_POST['game']);
$addToGames->setPlatform($_POST['platform']);
$addToGames->setPrice(floatval($_POST['price']));

$addToGames->addToGames();
?>
