<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class ComponentConfirmation extends Base
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
    $body = '<h2>Are you sure you want to delete this component?</h2>
        <div class="row actionButtons">
          <a class="keep" href="index.php">No</a>
          <a class="delete" href="deleteComponent.php?id=' . $this->getId() . '">Yes</a>
        </div>';

    return $body;
  }
}
$componentConfirmation = new ComponentConfirmation();
$componentConfirmation->setId(intval($_GET['id']));

$componentConfirmation->setTitle("Ephraim Becker - Everyday Life - Accomplishments - Gaming setup - Delete component?");
$componentConfirmation->setLocalStyleSheet("css/style.css");
$componentConfirmation->setLocalScript(NULL);
$componentConfirmation->setHeader("Gaming setup - Delete this component?");
$componentConfirmation->setUrl($_SERVER['REQUEST_URI']);
$componentConfirmation->setBody($componentConfirmation->main());

$componentConfirmation->html();
?>
