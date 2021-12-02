<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class AddToGamingSetupForm extends Base
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
    $body = '<form action="addToGamingSetup.php" method="post">
          <div>
            <div>
              <label for="component">Component:</label>
              <br />
              <input type="text" id="component" name="component" required />
            </div>
            <br />
            <div>
              <label for="originalModel">Model:</label>
              <br />
              <input type="text" id="originalModel" name="originalModel" required />
            </div>
            <br />
            <div>
              <label for="originalModelPrice">Price:</label>
              <br />
              $<input type="number" step="0.01" id="originalModelPrice" name="originalModelPrice" required />
            </div>
          </div>
          <br />
          <input type="submit" id="submit" value="Add to gaming setup" />
          <br />
        </form>';

      return $body;
  }
}
$addToGamingSetupForm = new AddToGamingSetupForm();
$addToGamingSetupForm->setLocalStyleSheet("css/style.css");
$addToGamingSetupForm->setLocalScript(NULL);
$addToGamingSetupForm->setTitle("Ephraim Becker - Everyday Life - Accomplishments - Gaming setup - Add to gaming setup");
$addToGamingSetupForm->setHeader("Add to gaming setup");
$addToGamingSetupForm->setUrl($_SERVER['REQUEST_URI']);
$addToGamingSetupForm->setBody($addToGamingSetupForm->main());

$addToGamingSetupForm->html();
?>
