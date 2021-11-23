<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class GradeClassForm extends Base
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

  function setId($id): void
  {
    $this->id = $id;
  }

  function getId(): int
  {
    return $this->id;
  }

  function main(): string
  {
    $html = '<form action="gradeClass.php" method="post">
              <div>
                <label">Grade:</label>
                <select name="grade" id="grade">
                  <option value="A+">A+</option>
                  <option value="A">A</option>
                  <option value="A-">A-</option>
                  <option value="B+">B+</option>
                  <option value="B">B</option>
                  <option value="B-">B-</option>
                  <option value="C+">C+</option>
                  <option value="C-">C-</option>
                  <option value="D+">D+</option>
                  <option value="D">D</option>
                  <option value="F">F</option>
                  <option value="W">W</option>
                </select>
              </div>
              <input type="hidden" name="id" id="id" value="' . $this->getId() . '" />
              <br />
              <input type="submit" id="submit" value="Grade class" />
              <br />
            </form>';

      return $html;
  }
}

$gradeClassForm = new GradeClassForm();
$gradeClassForm->setId(intval($_GET['id']));
$gradeClassForm->setLocalStyleSheet("css/style.css");
$gradeClassForm->setLocalScript(NULL);
$gradeClassForm->setTitle("Ephraim Becker - Grade class");
$gradeClassForm->setHeader(NULL);
$gradeClassForm->setUrl($_SERVER['REQUEST_URI']);
$gradeClassForm->setBody($gradeClassForm->main());

$gradeClassForm->html();
?>
