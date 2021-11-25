<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class ComfortZoneConfirmation extends Base
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
    $body = '<h2>Are you sure you want to delete this comfort zone?</h2>
        <div class="row actionButtons">
          <a class="keep" href="index.php">No</a>
          <a class="delete" href="deleteComfortZone.php?id=' . $this->getId() . '">Yes</a>
        </div>';

    return $body;
  }
}
$comfortZoneConfirmation = new ComfortZoneConfirmation();
$comfortZoneConfirmation->setId(intval($_GET['id']));

$comfortZoneConfirmation->setTitle("Ephraim Becker - Everyday life - Problems - Delete comfort zone?");
$comfortZoneConfirmation->setLocalStyleSheet("css/style.css");
$comfortZoneConfirmation->setLocalScript(NULL);
$comfortZoneConfirmation->setHeader("Problems - Delete this comfort zone?");
$comfortZoneConfirmation->setUrl($_SERVER['REQUEST_URI']);
$comfortZoneConfirmation->setBody($comfortZoneConfirmation->main());

$comfortZoneConfirmation->html();
?>
