<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditGame
{
  private $isAdmin;
  private $link;
  private $id;
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

  function setId($id): void
  {
    $this->id = $id;
  }

  function getId(): int
  {
    return $this->id;
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

  function editGame(): void
  {
    $sql = $this->getLink()->prepare("UPDATE ComputerGames SET Game = ?, Platform = ?, Price = ?, DateModified = ? WHERE ComputerGamesId = ?");
    $sql->bind_param('ssdsi', $game, $platform, $price, $dateNow, $id);

    $game = $this->getGame();
    $platform = $this->getPlatform();
    $price = $this->getPrice();

    $dateNow = date("Y-m-d H:i:s");

    $id = $this->getId();

    $sql->execute();

    header("location: ../");
  }
}

$config = new Config();
$link = $config->connectToServer();

$editGame = new EditGame();
$editGame->setLink($link);
$editGame->setId(intval($_POST['id']));
$editGame->setGame($_POST['game']);
$editGame->setPlatform($_POST['platform']);
$editGame->setPrice(floatval($_POST['price']));

$editGame->editGame();
?>
