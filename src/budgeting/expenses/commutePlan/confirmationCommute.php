<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class ConfirmationCommute extends Base
{
  private $isAdmin;
  private $link;
  private $id;

  function __construct()
  {
    $this->setIsAdmin();

    if(!$this->getIsAdmin()) {
      header("location: ../../");
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

  function showConfirmation(): string
  {
    $html = '<h2>Are you sure you want to delete this commute?</h2>

    <div class="row actionButtons">
      <a class="keep" href="index.php">No</a>
      <a class="delete" href="deleteCommute.php?id=' . $this->getId() . '">Yes</a>
    </div>';

    return $html;
  }
}
$confirmationCommute = new ConfirmationCommute();
$confirmationCommute->setId(intval($_GET['id']));
$confirmationCommute->setTitle("Ephraim Becker - Budgeting - Commute - Delete?");
$confirmationCommute->setLocalStyleSheet('css/style.css');
$confirmationCommute->setLocalScript(NULL);
$confirmationCommute->setHeader('Budgeting - Commute - Delete?');
$confirmationCommute->setUrl($_SERVER['REQUEST_URI']);
$confirmationCommute->setBody($confirmationCommute->showConfirmation());

$confirmationCommute->html();
