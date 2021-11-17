<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditClass
{
  private $isAdmin;
  private $link;

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

  function setLink($link)
  {
    $this->link = $link;
  }

  function getLink()
  {
    return $this->link;
  }

  function editClass(): void
  {
    $sql = $this->getLink()->prepare("UPDATE course SET courseCodeName = ?, CourseName = ?, courseType = ?, credits = ? WHERE course_id = ?");
    $sql->bind_param("ssiii", $courseCodeName, $courseName, $courseType, $credits, $courseId);

    $courseId = $_POST['courseId'];

    $courseCodeName = $_POST['courseCode'];
    $courseName = $_POST['courseName'];
    $courseType = $_POST['classType'];
    $credits = $_POST['credits'];

    $sql->execute();

    $sqlTwo = $this->getLink()->prepare("UPDATE college SET semester_id = ? WHERE college_id = ?");
    $sqlTwo->bind_param("ii", $semesterId, $collegeId);

    $semesterId = $_POST['semesterId'];
    $collegeId = $_POST['collegeId'];

    $sqlTwo->execute();

    header("location: ../");
  }
}

$config = new Config();
$link = $config->connectToServer();

$editClass = new EditClass();
$editClass->setLink($link);
$editClass->setIsAdmin();

if(!$editClass->getIsAdmin()) {
  header("location: ../");
}

$editClass->editClass();
