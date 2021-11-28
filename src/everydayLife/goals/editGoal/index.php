<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditGoalForm extends Base
{
  private $isAdmin;
  private $link;
  private $query;

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

  function setId($id): void
  {
    $this->id = $id;
  }

  function getId(): int
  {
    return $this->id;
  }

  function getQuery(): string
  {
    return $this->query;
  }

  function setQuery($query): void
  {
    $this->query = $query;
  }

  function fetchEvent(): mysqli_result
  {
    $sql = $this->getLink()->prepare($this->getQuery());
    $sql->bind_param("i", $id);

    $id = $this->getId();

    $sql->execute();

    $sqlResult = $sql->get_result();

    return $sqlResult;
  }

  function main(): string
  {
    $sqlResult = $this->fetchEvent();

    while($row = mysqli_fetch_array($sqlResult)) {
      $id = $row['GoalId'];
      $goal = $row['Goal'];
      $how = $row['How'];
    }

    $body = '<form action="editGoal.php" method="post">
          <div class="grid-container">
            <div>
              <label for="goal">Goal:</label>
              <br />
              <textarea id="goal" name="goal" rows="6" cols="45" required>' . $goal . '</textarea>
            </div>
            <div>
              <label for="how">How:</label>
              <br />
              <textarea id="how" name="how" rows="6" cols="45" required>' . $how . '</textarea>
            </div>
          </div>
          <input type="hidden" id="id" name="id" value="' . $id . '" />
          <br />
          <input type="submit" id="submit" value="Edit goal" />
          <br />
        </form>';

      return $body;
  }
}
$config = new Config();
$link = $config->connectToServer();

$editGoalForm = new EditGoalForm();
$editGoalForm->setLink($link);
$editGoalForm->setId(intval($_GET['id']));
$editGoalForm->setQuery("SELECT * FROM goals WHERE GoalId=?");

$editGoalForm->setLocalStyleSheet("css/style.css");
$editGoalForm->setLocalScript(NULL);
$editGoalForm->setTitle("Ephraim Becker - Everyday Life - Goals - Edit goal");
$editGoalForm->setHeader("Edit goal");
$editGoalForm->setUrl($_SERVER['REQUEST_URI']);
$editGoalForm->setBody($editGoalForm->main());

$editGoalForm->html();
?>
