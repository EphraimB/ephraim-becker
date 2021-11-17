<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class EditCollegeClassForm extends Base
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
    $html = '<form action="addCollegeClass.php" method="post" enctype="multipart/form-data">
            <div>
              <label for="eventDescription">College semester:</label>
              <select>
                <option value="1">Spring 2020</option>
                <option value="2">Fall 2020</option>
                <option value="3">Spring 2021</option>
                <option value="4">Fall 2021</option>
              </select>
            </div>
            <br />
            <div>
              <label for="courseName">Course name:</label>
              <input type="text" id="courseName" name="courseName" />
            </div>
            <br />
            <div>
              <label for="courseCode">Course code:</label>
              <input type="text" id="courseCode" name="courseCode" />
            </div>
            <br />
            <div>
              <label for="credits">Credits:</label>
              <input type="text" id="credits" name="credits" />
            </div>
            <br />
            <div>
              <h3>Class type:</h3>
              <div class="row">
                <div>
                  <input type="radio" id="major" name="class" value="1" required />
                  <label for="major">Major</label>
                </div>
                <div>
                  <input type="radio" id="core" name="class" value="0" />
                  <label for="core">Core requirement</label>
                </div>
              </div>
            </div>
            <br />
            <input type="submit" id="submit" value="Edit class" />
            <br />
          </form>';

        return $html;
    }
}

$config = new Config();
$link = $config->connectToServer();

$editCollegeClassForm = new EditCollegeClassForm();
$college->setLink($link);
$editCollegeClassForm->setIsAdmin();
$editCollegeClassForm->setLocalStyleSheet("css/style.css");
$editCollegeClassForm->setLocalScript(NULL);
$editCollegeClassForm->setTitle("Ephraim Becker - Edit college class");
$editCollegeClassForm->setHeader(NULL);
$editCollegeClassForm->setUrl($_SERVER['REQUEST_URI']);
$editCollegeClassForm->setBody($editCollegeClassForm->main());

$editCollegeClassForm->html();
