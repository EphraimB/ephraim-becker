<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditDemographicRowForm extends Base
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
      $id = $row['demographicId'];
      $yeshivish = $row['yeshivish'];
      $neurotypical = $row['neurotypical'];
      $autism = $row['autism'];
      $older = $row['older'];
    }

    $body = '<form action="editDemographicRow.php" method="post">
          <div class="grid-container">
            <div>
              <label for="yeshivishFriends">Yeshivish friends:</label>
              <br />
              <textarea id="yeshivishFriends" name="yeshivishFriends" rows="6" cols="45" required>' . $yeshivish . '</textarea>
            </div>
            <div>
              <label for="neurotypicalFriends">Neurotypical friends:</label>
              <br />
              <textarea id="neurotypicalFriends" name="neurotypicalFriends" rows="6" cols="45" required>' . $neurotypical . '</textarea>
            </div>
            <div>
              <label for="autisticFriends">Autistic friends:</label>
              <br />
              <textarea id="autisticFriends" name="autisticFriends" rows="6" cols="45" required>' . $autism . '</textarea>
            </div>
            <div>
              <label for="olderFriends">Older friends:</label>
              <br />
              <textarea id="olderFriends" name="olderFriends" rows="6" cols="45" required>' . $older . '</textarea>
            </div>
          </div>
          <input type="hidden" id="id" name="id" value="' . $id . '" />
          <br />
          <input type="submit" id="submit" value="Edit demographic row" />
          <br />
        </form>';

      return $body;
  }
}
$config = new Config();
$link = $config->connectToServer();

$editDemographicRowForm = new EditDemographicRowForm();
$editDemographicRowForm->setLink($link);
$editDemographicRowForm->setId(intval($_GET['id']));
$editDemographicRowForm->setQuery("SELECT * FROM demographics WHERE demographicId=?");

$editDemographicRowForm->setLocalStyleSheet("css/style.css");
$editDemographicRowForm->setLocalScript(NULL);
$editDemographicRowForm->setTitle("Ephraim Becker - Everyday Life - Problems - Edit demographic row");
$editDemographicRowForm->setHeader("Everyday Life - Problems - Edit demographic row");
$editDemographicRowForm->setUrl($_SERVER['REQUEST_URI']);
$editDemographicRowForm->setBody($editDemographicRowForm->main());

$editDemographicRowForm->html();
?>
