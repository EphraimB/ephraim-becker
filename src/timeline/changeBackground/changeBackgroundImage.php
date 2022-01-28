<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class ChangeBackgroundImage
{
  private $isAdmin;
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

  function changeBackgroundImage(): void
  {


    header("location: ../");
  }
}

$config = new Config();
$link = $config->connectToServer();

$changeBackgroundImage = new ChangeBackgroundImage();
$changeBackgroundImage->setLink($link);

$changeBackgroundImage->ChangeBackgroundImage();
?>
