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

  function setLink($link)
  {
    $this->link = $link;
  }

  function getLink()
  {
    return $this->link;
  }

  function main(): string
  {
    $sql = $this->getLink()->prepare("SELECT * FROM college WHERE college_id = ?");
    $sql->bind_param("i", $collegeId);

    $collegeId = $_GET['id'];

    $sql->execute();

    $sqlResult = $sql->get_result();

    while($row = mysqli_fetch_array($sqlResult)) {
      $courseId = $row['course_id'];
      $semesterId = $row['semester_id'];
    }

    $sqlTwo = $this->getLink()->prepare("SELECT * FROM course WHERE course_id = ?");
    $sqlTwo->bind_param("i", $courseId);

    $sqlTwo->execute();

    $sqlTwoResult = $sqlTwo->get_result();

    while($row = mysqli_fetch_array($sqlTwoResult)) {
      $courseCodeName = $row['courseCodeName'];
      $courseName = $row['CourseName'];
      $courseType = $row['courseType'];
      $credits = $row['credits'];
    }

    $html = '<form action="addCollegeClass.php" method="post">
            <div>
              <label for="eventDescription">College semester:</label>
              <select>
                <option value="1" ';

                if($semesterId == 1) {
                  $html .= "selected";
                }

                $html .= '>Spring 2020</option>
                <option value="2" ';

                if($semesterId == 2) {
                  $html .= "checked";
                }

                $html .= '>Fall 2020</option>
                <option value="3" ';

                if($semesterId == 3) {
                  $html .= "selected";
                }

                $html .= '>Spring 2021</option>
                <option value="4" ';

                if($semesterId == 4) {
                  $html .= "selected";
                }

                $html .= '>Fall 2021</option>
              </select>
            </div>
            <br />
            <div>
              <label for="courseName">Course name:</label>
              <input type="text" id="courseName" name="courseName" value="' . $courseName . '" />
            </div>
            <br />
            <div>
              <label for="courseCode">Course code:</label>
              <input type="text" id="courseCode" name="courseCode" value="' . $courseCodeName . '" />
            </div>
            <br />
            <div>
              <label for="credits">Credits:</label>
              <input type="text" id="credits" name="credits" value="' . $credits . '" />
            </div>
            <br />
            <div>
              <h3>Class type:</h3>
              <div class="row">
                <div>
                  <input type="radio" id="major" name="class" value="1" ';

                  if($courseType == 1) {
                    $html .= "checked ";
                  }

                  $html .= 'required />
                  <label for="major">Major</label>
                </div>
                <div>
                  <input type="radio" id="core" name="class" value="0" ';

                  if($courseType == 0) {
                    $html .= "checked ";
                  }

                  $html .= ' />
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
$editCollegeClassForm->setLink($link);
$editCollegeClassForm->setLocalStyleSheet("css/style.css");
$editCollegeClassForm->setLocalScript(NULL);
$editCollegeClassForm->setTitle("Ephraim Becker - Edit college class");
$editCollegeClassForm->setHeader(NULL);
$editCollegeClassForm->setUrl($_SERVER['REQUEST_URI']);
$editCollegeClassForm->setBody($editCollegeClassForm->main());

$editCollegeClassForm->html();
