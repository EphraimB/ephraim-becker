<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class AddGoalLogForm extends Base
{
  private $isAdmin;
  private $link;

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

  function setLink($link)
  {
    $this->link = $link;
  }

  function getLink()
  {
    return $this->link;
  }

  function main(): string
  {
    $sql = "SELECT GoalId, Goal FROM goals";
    $sqlResult = mysqli_query($this->getLink(), $sql);

    $body = '<form action="addGoalLog.php" method="post">
          <div>
            <div>
              <label for="goalId">Goal:</label>
              <select name="goalId" id="goalId">';

              while($row = mysqli_fetch_array($sqlResult)) {
                $goalId = $row['GoalId'];
                $goal = $row['Goal'];

                $body .= '<option value="' . $goalId . '">' . $goal . '</option>';
              }

              $body .= '</select>
            </div>
            <br />
            <div>
              <label for="log">Log:</label>
              <br />
              <textarea id="log" name="log" rows="6" cols="45" required></textarea>
            </div>
          </div>
          <br />
          <input type="submit" id="submit" value="Add to goal log" />
          <br />
        </form>';

      return $body;
  }
}
$config = new Config();
$link = $config->connectToServer();

$addGoalLogForm = new AddGoalLogForm();
$addGoalLogForm->setLink($link);

$addGoalLogForm->setLocalStyleSheet("css/style.css");
$addGoalLogForm->setLocalScript(NULL);
$addGoalLogForm->setTitle("Ephraim Becker - Everyday Life - Goals - Add goal log");
$addGoalLogForm->setHeader("Add to goal log");
$addGoalLogForm->setUrl($_SERVER['REQUEST_URI']);
$addGoalLogForm->setBody($addGoalLogForm->main());

$addGoalLogForm->html();
?>
