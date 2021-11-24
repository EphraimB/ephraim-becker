<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class DemographicRowConfirmation extends Base
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
    $body = '<h2>Are you sure you want to delete this demographic row?</h2>
        <div class="row actionButtons">
          <a class="keep" href="index.php">No</a>
          <a class="delete" href="deleteDemographicRow.php?id=' . $this->getId() . '">Yes</a>
        </div>';

    return $body;
  }
}
$demographicRowConfirmation = new DemographicRowConfirmation();
$demographicRowConfirmation->setId(intval($_GET['id']));

$demographicRowConfirmation->setTitle("Ephraim Becker - Everyday life - Problems - Delete demographic row?");
$demographicRowConfirmation->setLocalStyleSheet("css/style.css");
$demographicRowConfirmation->setLocalScript(NULL);
$demographicRowConfirmation->setHeader("Problems - Delete demographic row?");
$demographicRowConfirmation->setUrl($_SERVER['REQUEST_URI']);
$demographicRowConfirmation->setBody($demographicRowConfirmation->main());

$demographicRowConfirmation->html();
?>
