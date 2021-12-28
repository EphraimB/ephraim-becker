<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class Goals extends Base
{
  private $link;
  private $isAdmin;

  function __construct()
  {

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

  function addGoal(): string
  {
    if($this->getIsAdmin()) {
      $html = '<div class="row">
            <ul class="subNav">
              <li><a style="text-decoration: none;" href="addGoal/">+</a></li>
            </ul>
          </div>';
      } else {
        $html = '';
      }

    return $html;
  }

  function goals(): string
  {
    $sql = "SELECT * FROM goals";
    $sqlResult = mysqli_query($this->getLink(), $sql);

    $body = '
    <table>
      <tr>
        <th>Goal</th>
        <th>How it will be done</th>';

        if($this->getIsAdmin()) {
          $body .= '<th>Actions</th>';
        }

      $body .= '</tr>';

      while($row = mysqli_fetch_array($sqlResult)) {
        $id = $row['GoalId'];
        $goal = $row['Goal'];
        $how = $row['How'];

        $body .= '
        <tr>
          <td>' . $goal . '</td>
          <td>' . $how . '</td>';

          if($this->getIsAdmin()) {
            $body .= '
              <td class="actionButtons">
                <a class="edit" href="editGoal/index.php?id=' . $id . '">Edit</a>
                <a class="delete" href="confirmationGoal.php?id=' . $id . '">Delete</a>
              </td>';
          }
        $body .= '</tr>';
      }

      $body .= '</table>';

    return $body;
  }

  function addGoalLog(): string
  {
    if($this->getIsAdmin()) {
      $html = '<div class="row">
            <ul class="subNav">
              <li><a style="text-decoration: none;" href="addGoalLog/">+</a></li>
            </ul>
          </div>';
      } else {
        $html = '';
      }

    return $html;
  }

  function goalLog(): string
  {
    $sql = "SELECT * FROM GoalLog JOIN goals ON GoalLog.GoalId = goals.GoalId";
    $sqlResult = mysqli_query($this->getLink(), $sql);

    $body = '<h2>Goal log</h2>
    <table>
      <tr>
        <th>Date</th>
        <th>Goal working on</th>
        <th>Log</th>';

        if($this->getIsAdmin()) {
          $body .= '<th>Actions</th>';
        }

      $body .= '</tr>';

      while($row = mysqli_fetch_array($sqlResult)) {
        $id = $row['GoalLogId'];
        $goal = $row['Goal'];
        $log = $row['Log'];
        $dateCreated = $row['DateCreated'];

        $body .= '
        <tr>
          <td>' . $dateCreated . '</td>
          <td>' . $goal . '</td>
          <td>' . $log . '</td>';

          if($this->getIsAdmin()) {
            $body .= '
              <td class="actionButtons">
                <a class="edit" href="editGoalLog/index.php?id=' . $id . '">Edit</a>
                <a class="delete" href="confirmationGoalLog.php?id=' . $id . '">Delete</a>
              </td>';
          }
          $body .= '</tr>';
        }

        $body .= '</table>';

    return $body;
  }

  function main(): string
  {
    $body = $this->addGoal();
    $body .= $this->goals();
    $body .= '<br />';
    $body .= $this->addGoalLog();
    $body .= $this->goalLog();

        // <h2>Goal log</h2>
        // <table>
        //   <tr>
        //     <th>Date</th>
        //     <th>Goal worked on</th>
        //     <th>Log</th>
        //   </tr>
        //   <tr>
        //     <td></td>
        //     <td></td>
        //     <td></td>
        //   </tr>
        // </table>';

      return $body;
  }
}
$config = new Config();
$link = $config->connectToServer();

$goals = new Goals();
$goals->setLink($link);
$goals->setIsAdmin();

$goals->setUrl($_SERVER['REQUEST_URI']);
$goals->setTitle('Ephraim Becker - Everyday Life - Goals');
$goals->setLocalStyleSheet('css/style.css');
$goals->setLocalScript(NULL);
$goals->setHeader('Goals');
$goals->setBody($goals->main());

$goals->html();
?>
