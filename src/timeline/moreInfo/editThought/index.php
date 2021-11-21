<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditThoughtForm extends Base
{
  private $isAdmin;
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

  function setLink($link): void
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
    $body = '<form action="editThought.php" method="post">
          <div class="row">';

            $sql = $this->getLink()->prepare("SELECT * FROM thoughts WHERE ThoughtId=?");
            $sql->bind_param("i", $id);

            $id = $this->getId();

            $sql->execute();

            $sqlResult = $sql->get_result();

            while($row = mysqli_fetch_array($sqlResult)){
              $hide = $row['hide'];
              $thoughtId = $row['ThoughtId'];
              $thought = $row['Thought'];
            }

      $body .= '<div>
            <label for="eventDescription">Thought:</label>
            <br />
            <textarea id="thought" name="thought" rows="6" cols="45" required>' . $thought . '</textarea>
          </div>
          <br />
          <div>
            <h3>Event memory type:</h3>
            <div class="row">
              <div class="hidden-memory remembered-memory">
                <input type="checkbox" id="hidden" name="hidden" value="1"';
                if($hide == 1) {
                  $body .= "checked";
                }
                $body .= ' />
                <label for="hidden">Hidden thought</label>
              </div>
            </div>
          </div>
          <input type="hidden" name="id" value="' . $thoughtId . '" />
          <br />
          <input type="submit" id="submit" value="Edit thought" />
          <br />
        </form>';

    return $body;
  }
}
$config = new Config();
$link = $config->connectToServer();

$editThoughtForm = new EditThoughtForm();
$editThoughtForm->setLink($link);
$editThoughtForm->setId($_GET['id']);

$editThoughtForm->setLocalStyleSheet("css/style.css");
$editThoughtForm->setLocalScript("js/script.js");
$editThoughtForm->setTitle("Ephraim Becker - Timeline - Edit Thought");
$editThoughtForm->setHeader("Timeline - Edit Thought");
$editThoughtForm->setUrl($_SERVER['REQUEST_URI']);
$editThoughtForm->setBody($editThoughtForm->main());

$editThoughtForm->html();
?>
