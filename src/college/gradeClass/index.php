<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class GradeClass extends Base
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
                <select>
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
              <br />
              <input type="submit" id="submit" value="Grade class" />
              <br />
            </form>';

      return $html;
  }
}

$gradeClass = new GradeClass();
$gradeClass->setIsAdmin();
$gradeClass->setLocalStyleSheet("css/style.css");
$gradeClass->setLocalScript(NULL);
$gradeClass->setTitle("Ephraim Becker - Grade class");
$gradeClass->setHeader(NULL);
$gradeClass->setUrl($_SERVER['REQUEST_URI']);
$gradeClass->setBody($gradeClass->main());

$gradeClass->html();
