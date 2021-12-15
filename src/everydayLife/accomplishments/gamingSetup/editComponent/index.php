<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditComponentForm extends Base
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
      $id = $row['GamingSetupId'];
      $component = $row['Component'];
      $model = $row['Model'];
      $price = $row['Price'];
    }

    $body = '<form action="editComponent.php" method="post">
          <div>
            <div>
              <label for="component">Component:</label>
              <br />
              <input type="text" id="component" name="component" value="' . $component . '" required />
            </div>
            <br />
            <div>
              <label for="model">Model:</label>
              <br />
              <input type="text" id="model" name="model" value="' . $model . '" required />
            </div>
            <br />
            <div>
              <label for="price">Price:</label>
              <br />
              $<input type="number" step="0.01" id="price" name="price" value="' . $price . '" required />
            </div>
          </div>
          <input type="hidden" id="id" name="id" value="' . $id . '" />
          <br />
          <input type="submit" id="submit" value="Edit component" />
          <br />
        </form>';

      return $body;
  }
}
$config = new Config();
$link = $config->connectToServer();

$editComponentForm = new EditComponentForm();
$editComponentForm->setLink($link);
$editComponentForm->setId(intval($_GET['id']));
$editComponentForm->setQuery("SELECT * FROM GamingSetup WHERE GamingSetupId=?");

$editComponentForm->setLocalStyleSheet("css/style.css");
$editComponentForm->setLocalScript(NULL);
$editComponentForm->setTitle("Ephraim Becker - Everyday Life - Accomplishments - Gaming setup - Edit component");
$editComponentForm->setHeader("Edit component");
$editComponentForm->setUrl($_SERVER['REQUEST_URI']);
$editComponentForm->setBody($editComponentForm->main());

$editComponentForm->html();
?>
