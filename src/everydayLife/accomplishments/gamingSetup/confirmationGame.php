<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class GameConfirmation extends Base
{
  private $isAdmin;
  private $id;

  function __construct()
  {
    $this->setIsAdmin();

    if(!$this->getIsAdmin()) {
      header("location: index.php");
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

  function setId($id): void
  {
    $this->id = $id;
  }

  function getId(): int
  {
    return $this->id;
  }

  function main(): string
  {
    $body = '<h2>Are you sure you want to delete this game?</h2>
        <div class="row actionButtons">
          <a class="keep" href="index.php">No</a>
          <a class="delete" href="deleteGame.php?id=' . $this->getId() . '">Yes</a>
        </div>';

    return $body;
  }
}
$gameConfirmation = new GameConfirmation();
$gameConfirmation->setId(intval($_GET['id']));

$gameConfirmation->setTitle("Ephraim Becker - Everyday Life - Accomplishments - Gaming setup - Delete game?");
$gameConfirmation->setLocalStyleSheet("css/style.css");
$gameConfirmation->setLocalScript(NULL);
$gameConfirmation->setHeader("Gaming setup - Delete this game?");
$gameConfirmation->setUrl($_SERVER['REQUEST_URI']);
$gameConfirmation->setBody($gameConfirmation->main());

$gameConfirmation->html();
?>
