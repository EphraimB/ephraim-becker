<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EventConfirmation extends Base
{
  private $isAdmin;
  private $college_id;
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
    $sql = $this->getLink()->prepare("SELECT EventTitle FROM timeline WHERE TimelineId=?");
    $sql->bind_param("i", $id);

    $id = $_GET['id'];

    $sql->execute();

    $sqlResult = $sql->get_result();

    while($row = mysqli_fetch_array($sqlResult)){
      $eventTitle = $row['EventTitle'];
    }

    $body = '<h2>Are you sure you want to delete the event named "' . $eventTitle . '"?</h2>

    <div class="row actionButtons">
      <a class="keep" href="index.php">No</a>
      <a class="delete" href="deleteEvent.php?id=' . $id . '">Yes</a>
    </div>';

    return $body;
  }
}

$config = new Config();
$link = $config->connectToServer();

$eventConfirmation = new EventConfirmation();
$eventConfirmation->setLink($link);

$eventConfirmation->setTitle("Ephraim Becker - Timeline - Delete?");
$eventConfirmation->setLocalStyleSheet("css/style.css");
$eventConfirmation->setLocalScript(NULL);
$eventConfirmation->setHeader("Timeline - Delete?");
$eventConfirmation->setUrl($_SERVER['REQUEST_URI']);
$eventConfirmation->setBody($eventConfirmation->main());

$eventConfirmation->html();
?>
