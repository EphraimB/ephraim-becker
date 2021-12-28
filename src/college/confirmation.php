<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class Confirmation extends Base
{
  private $isAdmin;
  private $college_id;

  function __construct()
  {
    $this->setIsAdmin();

    if(!$this->getIsAdmin()) {
      header("location: index.php");
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

  function setCollegeId($id): void
  {
    $this->college_id = $id;
  }

  function getCollegeId(): int
  {
    return $this->college_id;
  }

  function main(): string
  {
    $html = '<h2>Are you sure you want to delete this class?</h2>

    <div class="row actionButtons">
      <a class="keep" href="index.php">No</a>
      <a class="delete" href="removeClass.php?id=' . $this->getCollegeId() . '">Yes</a>
    </div>';

    return $html;
  }
}

$confirmation = new Confirmation();

$confirmation->setCollegeId(intval($_GET['id']));

$confirmation->setTitle("Ephraim Becker - College - Confirmation");
$confirmation->setLocalStyleSheet("css/style.css");
$confirmation->setLocalScript(NULL);
$confirmation->setHeader("College - Delete class?");
$confirmation->setUrl($_SERVER['REQUEST_URI']);
$confirmation->setBody($confirmation->main());

$confirmation->html();
?>
