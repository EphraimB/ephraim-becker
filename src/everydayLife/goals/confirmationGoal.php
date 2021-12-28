<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class GoalConfirmation extends Base
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
    $body = '<h2>Are you sure you want to delete this goal?</h2>
        <div class="row actionButtons">
          <a class="keep" href="index.php">No</a>
          <a class="delete" href="deleteGoal.php?id=' . $this->getId() . '">Yes</a>
        </div>';

    return $body;
  }
}
$goalConfirmation = new GoalConfirmation();
$goalConfirmation->setId(intval($_GET['id']));

$goalConfirmation->setTitle("Ephraim Becker - Everyday life - Goals - Delete goal?");
$goalConfirmation->setLocalStyleSheet("css/style.css");
$goalConfirmation->setLocalScript(NULL);
$goalConfirmation->setHeader("Goals - Delete this goal?");
$goalConfirmation->setUrl($_SERVER['REQUEST_URI']);
$goalConfirmation->setBody($goalConfirmation->main());

$goalConfirmation->html();
?>
