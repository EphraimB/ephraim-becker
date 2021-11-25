<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class AddComfortZoneForm extends Base
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
    $body = '<form action="addComfortZone.php" method="post">
          <div class="grid-container">
            <div>
              <label for="ComfortZone">Comfort zone:</label>
              <br />
              <textarea id="ComfortZone" name="ComfortZone" rows="6" cols="45" required></textarea>
            </div>
            <div>
              <label for="reason">Reason:</label>
              <br />
              <textarea id="reason" name="reason" rows="6" cols="45" required></textarea>
            </div>
          </div>
          <br />
          <input type="submit" id="submit" value="Add comfort zone" />
          <br />
        </form>';

      return $body;
  }
}
$addComfortZoneForm = new AddComfortZoneForm();
$addComfortZoneForm->setLocalStyleSheet("css/style.css");
$addComfortZoneForm->setLocalScript(NULL);
$addComfortZoneForm->setTitle("Ephraim Becker - Everyday Life - Problems - Add comfort zone");
$addComfortZoneForm->setHeader("Everyday Life - Problems - Add comfort zone");
$addComfortZoneForm->setUrl($_SERVER['REQUEST_URI']);
$addComfortZoneForm->setBody($addComfortZoneForm->main());

$addComfortZoneForm->html();
?>
