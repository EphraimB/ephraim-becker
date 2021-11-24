<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class AddDemographicRowForm extends Base
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
    $body = '<form action="addDemographicRow.php" method="post">
          <div class="grid-container">
            <div>
              <label for="yeshivishFriends">Yeshivish friends:</label>
              <br />
              <textarea id="yeshivishFriends" name="yeshivishFriends" rows="6" cols="45" required></textarea>
            </div>
            <div>
              <label for="neurotypicalFriends">Neurotypical friends:</label>
              <br />
              <textarea id="neurotypicalFriends" name="neurotypicalFriends" rows="6" cols="45" required></textarea>
            </div>
            <div>
              <label for="autisticFriends">Autistic friends:</label>
              <br />
              <textarea id="autisticFriends" name="autisticFriends" rows="6" cols="45" required></textarea>
            </div>
            <div>
              <label for="olderFriends">Older friends:</label>
              <br />
              <textarea id="olderFriends" name="olderFriends" rows="6" cols="45" required></textarea>
            </div>
          </div>
          <br />
          <input type="submit" id="submit" value="Add demographic row" />
          <br />
        </form>';

      return $body;
  }
}
$addDemographicRowForm = new AddDemographicRowForm();
$addDemographicRowForm->setLocalStyleSheet("css/style.css");
$addDemographicRowForm->setLocalScript(NULL);
$addDemographicRowForm->setTitle("Ephraim Becker - Everyday Life - Problems - Add demographic row");
$addDemographicRowForm->setHeader("Everyday Life - Problems - Add demographic row");
$addDemographicRowForm->setUrl($_SERVER['REQUEST_URI']);
$addDemographicRowForm->setBody($addDemographicRowForm->main());

$addDemographicRowForm->html();
?>
