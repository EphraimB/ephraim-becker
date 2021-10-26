<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class AddCollegeInfoForm extends Base
{
  private $isAdmin;

  function __construct()
  {
    $this->setIsAdmin();

    if($this->getIsAdmin()) {
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
}
