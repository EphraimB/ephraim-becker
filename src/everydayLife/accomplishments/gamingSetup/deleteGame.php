<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class DeleteGame
{
  private $link;
  private $isAdmin;
  private $id;

  function __construct()
  {
    $this->setIsAdmin();

    if(!$this->getIsAdmin()) {
      header("location: index.php");
    }
  }

  function setLink($link)
  {
    $this->link = $link;
  }

  function getLink()
  {
    return $this->link;
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

  function setId($id): void
  {
    $this->id = $id;
  }

  function getId(): int
  {
    return $this->id;
  }

  function deleteGame(): void
  {
    $sql = $this->getLink()->prepare("DELETE FROM ComputerGames WHERE ComputerGamesId=?");
    $sql->bind_param("i", $id);

    $id = $this->getId();

    $sql->execute();

    $sql->close();
    $this->getLink()->close();

    header("location: index.php");
  }
}
$config = new Config();
$link = $config->connectToServer();

$deleteGame = new DeleteGame();
$deleteGame->setLink($link);
$deleteGame->setId(intval($_GET['id']));
$deleteGame->deleteGame();
?>
