<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class Confirmation extends Base
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
    $sql = $this->getLink()->prepare("SELECT * FROM expenses WHERE ExpenseId=?");
    $sql->bind_param("i", $id);

    $id = $this->getId();

    $sql->execute();

    $sqlResult = $sql->get_result();

    while($row = mysqli_fetch_array($sqlResult)){
      $title = $row['ExpenseTitle'];
    }

    $html = '<h2>Are you sure you want to delete the event named "' . $title . '"?</h2>

    <div class="row actionButtons">
      <a class="keep" href="index.php">No</a>
      <a class="delete" href="deleteExpense.php?id=' . $id . '">Yes</a>
    </div>';

    return $html;
  }
}
$config = new Config();
$link = $config->connectToServer();

$confirmation = new Confirmation();
$confirmation->setLink($link);
$confirmation->setId(intval($_GET['id']));
$confirmation->setTitle("Ephraim Becker - Budgeting - Expenses - Delete?");
$confirmation->setLocalStyleSheet('css/style.css');
$confirmation->setLocalScript(NULL);
$confirmation->setHeader('Budgeting - Expenses - Delete?');
$confirmation->setUrl($_SERVER['REQUEST_URI']);
$confirmation->setBody($confirmation->showConfirmation());

$confirmation->html();
