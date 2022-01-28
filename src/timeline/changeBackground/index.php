<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class ChangeBackgroundImageForm extends Base
{
  private $isAdmin;

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

  function main(): string
  {
    $html = '
    <form action="changeBackgroundImage.php" method="post" enctype="multipart/form-data">
      <div class="row">
        <div>
          <label for="backgroundImage">Background image:</label>
          <br />
          <input id="backgroundImage" name="backgroundImage" type="file" />
        </div>
        <br />
      </div>
      <br />
      <input type="submit" id="submit" value="Change background image" />
      <br />
    </form>';

    return $html;
  }
}

$changeBackgroundImageForm = new ChangeBackgroundImageForm();
$changeBackgroundImageForm->setLocalStyleSheet("css/style.css");
$changeBackgroundImageForm->setLocalScript(NULL);
$changeBackgroundImageForm->setTitle("Ephraim Becker - Timeline - Change background image");
$changeBackgroundImageForm->setHeader("Timeline - Change background image");
$changeBackgroundImageForm->setUrl($_SERVER['REQUEST_URI']);
$changeBackgroundImageForm->setBody($changeBackgroundImageForm->main());

$changeBackgroundImageForm->html();
?>
