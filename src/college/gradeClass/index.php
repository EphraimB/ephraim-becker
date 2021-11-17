<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class GradeClassForm extends Base
{
  private $isAdmin;

  function __construct()
  {

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
              <input type="hidden" name="id" id="id" value="' . $_GET['id'] . '" />
              <br />
              <input type="submit" id="submit" value="Grade class" />
              <br />
            </form>';

      return $html;
  }
}

$gradeClassForm = new GradeClassForm();
$gradeClassForm->setIsAdmin();
$gradeClassForm->setLocalStyleSheet("css/style.css");
$gradeClassForm->setLocalScript(NULL);
$gradeClassForm->setTitle("Ephraim Becker - Grade class");
$gradeClassForm->setHeader(NULL);
$gradeClassForm->setUrl($_SERVER['REQUEST_URI']);
$gradeClassForm->setBody($gradeClassForm->main());

$gradeClassForm->html();
