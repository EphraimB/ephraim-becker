<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class ChangeBackgroundImageForm extends Base
{
  private $isAdmin;
  private $id;

  function __construct()
  {
    $this->setIsAdmin();

    if(!$this->getIsAdmin()) {
      header("location: ../");
    }
  }

  function getId(): int
  {
    return $this->id;
  }

  function setId($id): void
  {
    $this->id = $id;
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

  function main(): string
  {
    $html = '
    <form action="changeBackgroundImage.php" method="post" enctype="multipart/form-data">
      <div>
        <div>
          <label for="backgroundImage">Background image:</label>
          <br />
          <input type="file" id="backgroundImage" name="backgroundImage" />
        </div>
        <br />
        <div>
          <label for="imageDescription">Background image description:</label>
          <br />
          <input type="text" id="imageDescription" name="imageDescription" />
        </div>
        <br />
      </div>
      <br />
      <input type="hidden" name="id" value="' . $this->getId() . '" />
      <input type="submit" id="submit" value="Change background image" />
      <br />
    </form>';

    return $html;
  }
}

$changeBackgroundImageForm = new ChangeBackgroundImageForm();
$changeBackgroundImageForm->setId(intval($_GET['id']));

$changeBackgroundImageForm->setLocalStyleSheet("css/style.css");
$changeBackgroundImageForm->setLocalScript(NULL);
$changeBackgroundImageForm->setTitle("Ephraim Becker - Timeline - Change background image");
$changeBackgroundImageForm->setHeader("Timeline - Change background image");
$changeBackgroundImageForm->setUrl($_SERVER['REQUEST_URI']);
$changeBackgroundImageForm->setBody($changeBackgroundImageForm->main());

$changeBackgroundImageForm->html();
?>
