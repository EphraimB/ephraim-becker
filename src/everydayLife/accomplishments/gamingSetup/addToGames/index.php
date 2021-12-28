<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class AddToGamesForm extends Base
{
  private $isAdmin;

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

  function main(): string
  {
    $body = '<form action="addToGames.php" method="post">
          <div>
            <div>
              <label for="game">Game:</label>
              <br />
              <input type="text" id="game" name="game" required />
            </div>
            <br />
            <div>
              <label for="platform">Platform:</label>
              <br />
              <input type="text" id="platform" name="platform" required />
            </div>
            <br />
            <div>
              <label for="price">Price:</label>
              <br />
              $<input type="number" step="0.01" id="price" name="price" required />
            </div>
          </div>
          <br />
          <input type="submit" id="submit" value="Add to games" />
          <br />
        </form>';

      return $body;
  }
}
$addToGamesForm = new AddToGamesForm();
$addToGamesForm->setLocalStyleSheet("css/style.css");
$addToGamesForm->setLocalScript(NULL);
$addToGamesForm->setTitle("Ephraim Becker - Everyday Life - Accomplishments - Gaming setup - Add to games");
$addToGamesForm->setHeader("Add to games");
$addToGamesForm->setUrl($_SERVER['REQUEST_URI']);
$addToGamesForm->setBody($addToGamesForm->main());

$addToGamesForm->html();
?>
