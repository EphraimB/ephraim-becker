<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class GoalLogConfirmation extends Base
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
    $body = '<h2>Are you sure you want to delete this goal log?</h2>
        <div class="row actionButtons">
          <a class="keep" href="index.php">No</a>
          <a class="delete" href="deleteGoalLog.php?id=' . $this->getId() . '">Yes</a>
        </div>';

    return $body;
  }
}
$goalLogConfirmation = new GoalLogConfirmation();
$goalLogConfirmation->setId(intval($_GET['id']));

$goalLogConfirmation->setTitle("Ephraim Becker - Everyday life - Goals - Delete this goal log?");
$goalLogConfirmation->setLocalStyleSheet("css/style.css");
$goalLogConfirmation->setLocalScript(NULL);
$goalLogConfirmation->setHeader("Goals - Delete this goal log?");
$goalLogConfirmation->setUrl($_SERVER['REQUEST_URI']);
$goalLogConfirmation->setBody($goalLogConfirmation->main());

$goalLogConfirmation->html();
?>
