<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditComfortZoneForm extends Base
{
  private $isAdmin;
  private $id;
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
      $id = $row['ComfortZoneId'];
      $comfortZone = $row['ComfortZone'];
      $reason = $row['Reason'];
    }

    $body = '<form action="editComfortZone.php" method="post">
          <div class="grid-container">
            <div>
              <label for="ComfortZone">Comfort zone:</label>
              <br />
              <textarea id="ComfortZone" name="ComfortZone" rows="6" cols="45" required>' . $comfortZone . '</textarea>
            </div>
            <div>
              <label for="reason">Reason:</label>
              <br />
              <textarea id="reason" name="reason" rows="6" cols="45" required>' . $reason . '</textarea>
            </div>
          </div>
          <input type="hidden" id="id" name="id" value="' . $id . '" />
          <br />
          <input type="submit" id="submit" value="Edit comfort zone" />
          <br />
        </form>';

      return $body;
  }
}
$config = new Config();
$link = $config->connectToServer();

$editComfortZoneForm = new EditComfortZoneForm();
$editComfortZoneForm->setLink($link);
$editComfortZoneForm->setId(intval($_GET['id']));
$editComfortZoneForm->setQuery("SELECT * FROM ComfortZone WHERE ComfortZoneId=?");

$editComfortZoneForm->setLocalStyleSheet("css/style.css");
$editComfortZoneForm->setLocalScript(NULL);
$editComfortZoneForm->setTitle("Ephraim Becker - Everyday Life - Problems - Edit comfort zone");
$editComfortZoneForm->setHeader("Everyday Life - Problems - Edit comfort zone");
$editComfortZoneForm->setUrl($_SERVER['REQUEST_URI']);
$editComfortZoneForm->setBody($editComfortZoneForm->main());

$editComfortZoneForm->html();
?>
