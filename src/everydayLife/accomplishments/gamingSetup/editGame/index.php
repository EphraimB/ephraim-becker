<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditGameForm extends Base
{
  private $isAdmin;
  private $link;
  private $id;

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

  function getQuery(): string
  {
    return $this->query;
  }

  function setQuery($query): void
  {
    $this->query = $query;
  }

  function fetchEvent(): mysqli_result
  {
    $sql = $this->getLink()->prepare($this->getQuery());
    $sql->bind_param("i", $id);

    $id = $this->getId();

    $sql->execute();

    $sqlResult = $sql->get_result();

    return $sqlResult;
  }

  function main(): string
  {
    $sqlResult = $this->fetchEvent();

    while($row = mysqli_fetch_array($sqlResult)) {
      $id = $row['ComputerGamesId'];
      $game = $row['Game'];
      $platform = $row['Platform'];
      $price = $row['Price'];
    }

    $body = '<form action="editGame.php" method="post">
          <div>
            <div>
              <label for="game">Game:</label>
              <br />
              <input type="text" id="game" name="game" value="' . $game . '" required />
            </div>
            <br />
            <div>
              <label for="platform">Platform:</label>
              <br />
              <input type="text" id="platform" name="platform" value="' . $platform . '" required />
            </div>
            <br />
            <div>
              <label for="price">Price:</label>
              <br />
              $<input type="number" step="0.01" id="price" name="price" value="' . $price . '" required />
            </div>
          </div>
          <input type="hidden" id="id" name="id" value="' . $id . '" />
          <br />
          <input type="submit" id="submit" value="Edit game" />
          <br />
        </form>';

      return $body;
  }
}
$config = new Config();
$link = $config->connectToServer();

$editGameForm = new EditGameForm();
$editGameForm->setLink($link);
$editGameForm->setId(intval($_GET['id']));
$editGameForm->setQuery("SELECT * FROM ComputerGames WHERE ComputerGamesId=?");

$editGameForm->setLocalStyleSheet("css/style.css");
$editGameForm->setLocalScript(NULL);
$editGameForm->setTitle("Ephraim Becker - Everyday Life - Accomplishments - Gaming setup - Edit game");
$editGameForm->setHeader("Edit game");
$editGameForm->setUrl($_SERVER['REQUEST_URI']);
$editGameForm->setBody($editGameForm->main());

$editGameForm->html();
?>
