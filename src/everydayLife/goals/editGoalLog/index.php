<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditGoalLogForm extends Base
{
  private $isAdmin;
  private $link;
  private $id;
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
    $sql = "SELECT GoalId, Goal FROM goals";
    $sqlResult = mysqli_query($this->getLink(), $sql);

    $sqlTwoResult = $this->fetchEvent();

    while($row = mysqli_fetch_array($sqlTwoResult)) {
      $id = $row['GoalLogId'];
      $thisGoalId = $row['GoalId'];
      $log = $row['Log'];
    }

    $body = '<form action="editGoalLog.php" method="post">
          <div>
            <div>
              <label for="goalId">Goal:</label>
              <select name="goalId" id="goalId">';

              while($rowTwo = mysqli_fetch_array($sqlResult)) {
                $goalId = $rowTwo['GoalId'];
                $goal = $rowTwo['Goal'];

                $body .= '<option value="' . $goalId . '"';

                if($thisGoalId == $goalId) {
                  $body .= 'selected';
                }
                $body .= '>' . $goal . '</option>';
              }

              $body .= '</select>
            </div>
            <br />
            <div>
              <label for="log">Log:</label>
              <br />
              <textarea id="log" name="log" rows="6" cols="45" required>' . $log . '</textarea>
            </div>
          </div>
          <input type="hidden" id="id" name="id" value="' . $id . '" />
          <br />
          <input type="submit" id="submit" value="Edit goal log" />
          <br />
        </form>';

      return $body;
  }
}
$config = new Config();
$link = $config->connectToServer();

$editGoalLogForm = new EditGoalLogForm();
$editGoalLogForm->setLink($link);
$editGoalLogForm->setId(intval($_GET['id']));
$editGoalLogForm->setQuery("SELECT * FROM GoalLog WHERE GoalLogId = ?");

$editGoalLogForm->setLocalStyleSheet("css/style.css");
$editGoalLogForm->setLocalScript(NULL);
$editGoalLogForm->setTitle("Ephraim Becker - Everyday Life - Goals - Edit goal log");
$editGoalLogForm->setHeader("Edit goal log");
$editGoalLogForm->setUrl($_SERVER['REQUEST_URI']);
$editGoalLogForm->setBody($editGoalLogForm->main());

$editGoalLogForm->html();
?>
