<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class College extends Base
{
  private $isAdmin;
  private $link;
  private $degree;
  private $currentSemester;
  private $major;
  private $collegeName;
  private $totalCredits;
  private $query;


  function __construct()
  {
    $this->setQuery('SELECT college.college_id, semester.semester_id, course.course_id, CourseName AS "course", CourseType, Credits, grade, semester, if(grade = "F", "Fail", if(grade = "I", "Incomplete", if(ISNULL(grade), "Ongoing", if(grade = "W", "Withdrawn", "Pass")))) AS "status" FROM college JOIN course ON college.course_id = course.course_id JOIN semester ON college.semester_id = semester.semester_id ORDER BY semester_id, CourseType DESC');
  }

  function setLink($link)
  {
    $this->link = $link;
  }

  function getLink()
  {
    return $this->link;
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

  function setQuery($query): void
  {
    $this->query = $query;
  }

  function getQuery(): string
  {
    return $this->query;
  }

  function getTotalCredits(): string
  {
    return $this->totalCredits;
  }

  function setTotalCredits(): void
  {
    $sql = "SELECT SUM(credits) AS 'totalCredits' FROM college JOIN course ON college.course_id = course.course_id JOIN semester ON college.semester_id = semester.semester_id WHERE grade < 'F'";
    $sqlResult = mysqli_query($this->getLink(), $sql);

    while($row = mysqli_fetch_array($sqlResult)) {
      $totalCredits = $row['totalCredits'];
    }

    $this->totalCredits = $totalCredits;
  }

  function setDegree($degree): void
  {
    $this->degree = $degree;
  }

  function getDegree(): string
  {
    return $this->degree;
  }

  function setMajor($major): void
  {
    $this->major = $major;
  }

  function getMajor(): string
  {
    return $this->major;
  }

  function setCurrentSemester($currentSemester): void
  {
    $this->currentSemester = $currentSemester;
  }

  function getCurrentSemester(): string
  {
    return $this->currentSemester;
  }

  function setCollegeName($collegeName): void
  {
    $this->collegeName = $collegeName;
  }

  function getCollegeName(): string
  {
    return $this->collegeName;
  }

  function getColorCode($status): array
  {
    if($status == "Pass") {
      return array("green", "white");
    } else if($status == "Incomplete") {
      return array("yellow", "black");
    } else if($status == "Fail") {
      return array("red", "white");
    } else if($status == "Withdrawn") {
      return array("red", "white");
    } else {
      return array("white", "black");
    }
  }

  function fetchQuery(): mysqli_result
  {
    $sql = $this->getQuery();
    $sqlResult = mysqli_query($this->getLink(), $sql);

    return $sqlResult;
  }

  function generateCollegeInformation(): string
  {
    $html = '<caption>';
    $html .= $this->getCollegeName();
    $html .= ' - ';
    $html .= $this->getDegree();
    $html .= ' in ';
    $html .= $this->getMajor();
    $html .= '</caption>';

    return $html;
  }

  function createHeaderRow(): string
  {
    $html = '
        <tr>
          <th>Semester</th>
          <th>Course</th>
          <th>Credits</th>
          <th>Grade</th>';

    if($this->getIsAdmin()) {
      $html .= '<th>Actions</th>';
    }

    $html .= '</tr>';

    return $html;
  }

  function addCollegeInformation(): string
  {
    if($this->getIsAdmin() == true) {
      $html = '<div class="row">
            <ul class="subNav">
              <li><a href="addCollegeClass/">+</a></li>
            </ul>
          </div>';
      } else {
        $html = '';
      }

      return $html;
  }

  function main(): string
  {
    $html = '<table>';
    $html .= $this->addCollegeInformation();
    $html .= $this->generateCollegeInformation();
    $html .= $this->createHeaderRow();
    $sqlResult = $this->fetchQuery();

    while($row = mysqli_fetch_array($sqlResult)) {
      $college_id = $row['college_id'];
      $course_id = $row['course_id'];
      $course = $row['course'];
      $credits = $row['Credits'];
      $grade = $row['grade'];
      $semester = $row['semester'];
      $status = $row['status'];

      $colorCode = $this->getColorCode($status);

      $html .= '<tr style="background-color: ' . $colorCode[0] . '; color: ' . $colorCode[1] . '">';

      $html .= '<td>' . $semester . '</td>
      <td>' . $course . '</td>
      <td>' . $credits . '</td>
      <td>' . $grade . '</td>';

      if($this->getIsAdmin()) {
        $html .= '<td>
          <a class="edit" href="editClass/index.php?id=' . $course_id . '">Edit</a>
          <a class="remove" href="removeClass.php?id=' . $college_id . '">Remove</a>
          <a class="grade" href="gradeClass/index.php?id=' . $course_id . '">Grade</a>
        </td>';
      }

      $html .= '</tr>';
    }

    $html .= '<tr>
          <td colspan="4"><span style="font-weight: bold;">Total completed credits: </span>' . $this->getTotalCredits() . '/120</td>
        </tr>
      </table>

      <div style="margin-top: 10px;">
        <label for="MajorProgress">Major progress:</label>
        <progress id="MajorProgress" value="17" max="56">30%</progress>
      </div>

      <div>
        <label for="CoreProgress">Core progress:</label>
        <progress id="CoreProgress" value="9" max="24">37.5%</progress>
      </div>

      <div>
        <label for="DegreeProgress">Degree progress:</label>
        <progress id="DegreeProgress" value="26" max="120">21.7%</progress>
      </div>';

    return $html;
  }
}

$config = new Config();
$link = $config->connectToServer();

$college = new College();
$college->setLink($link);
$college->setIsAdmin();
$college->setTotalCredits();
$college->setCollegeName("Landers College for men");
$college->setDegree("BS");
$college->setMajor("Computer Science");
$college->setTitle("Ephraim Becker - College");
$college->setLocalStyleSheet("css/style.css");
$college->setLocalScript(NULL);
$college->setHeader("College Life");
$college->setUrl($_SERVER['REQUEST_URI']);
$college->setBody($college->main());

$college->html();
?>
