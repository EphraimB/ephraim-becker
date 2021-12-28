<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class AddGoalForm extends Base
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
    $body = '<form action="addGoal.php" method="post">
          <div class="grid-container">
            <div>
              <label for="goal">Goal:</label>
              <br />
              <textarea id="goal" name="goal" rows="6" cols="45" required></textarea>
            </div>
            <div>
              <label for="how">How:</label>
              <br />
              <textarea id="how" name="how" rows="6" cols="45" required></textarea>
            </div>
          </div>
          <br />
          <input type="submit" id="submit" value="Add goal" />
          <br />
        </form>';

      return $body;
  }
}
$addGoalForm = new AddGoalForm();
$addGoalForm->setLocalStyleSheet("css/style.css");
$addGoalForm->setLocalScript(NULL);
$addGoalForm->setTitle("Ephraim Becker - Everyday Life - Goals - Add goal");
$addGoalForm->setHeader("Add goal");
$addGoalForm->setUrl($_SERVER['REQUEST_URI']);
$addGoalForm->setBody($addGoalForm->main());

$addGoalForm->html();
?>
