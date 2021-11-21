<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class ThoughtConfirmation extends Base
{
  private $isAdmin;
  private $college_id;
  private $link;
  private $id;

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
    return intval($this->id);
  }

  function main(): string
  {
    $body = '<h2>Are you sure you want to delete this thought"?</h2>';

    $sql = $this->getLink()->prepare("SELECT TimelineId FROM thoughts WHERE ThoughtId=? LIMIT 1");

    $sql->bind_param("i", $id);

    $id = $this->getId();

    $sql->execute();

    $sqlResult = $sql->get_result();

    while($row = mysqli_fetch_array($sqlResult)) {
      $timelineId = $row['TimelineId'];
    }

    $body .= '<div class="row actionButtons">
          <a class="keep" href="index.php?id=' . $timelineId . '">No</a>
          <a class="delete" href="deleteThought.php?id=' . $this->getId() . '">Yes</a>
        </div>';

    return $body;
  }
}
$config = new Config();
$link = $config->connectToServer();

$thoughtConfirmation = new ThoughtConfirmation();
$thoughtConfirmation->setLink($link);
$thoughtConfirmation->setId($_GET['id']);

$thoughtConfirmation->setTitle("Ephraim Becker - Timeline - Delete thought?");
$thoughtConfirmation->setLocalStyleSheet("../css/style.css");
$thoughtConfirmation->setLocalScript(NULL);
$thoughtConfirmation->setHeader("Timeline - Delete thought?");
$thoughtConfirmation->setUrl($_SERVER['REQUEST_URI']);
$thoughtConfirmation->setBody($thoughtConfirmation->main());

$thoughtConfirmation->html();
?>
