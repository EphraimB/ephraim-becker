<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class AddClass
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

  function addClass(): void
  {
    $sql = $this->getLink()->prepare("INSERT INTO course (courseCodeName, CourseName, courseType, credits) VALUES (?, ?, ?, ?)");
    $sql->bind_param("ssii", $courseCodeName, $courseName, $courseType, $credits);

    $courseCodeName = $_POST['courseCode'];
    $courseName = $_POST['courseName'];
    $courseType = $_POST['classType'];
    $credits = $_POST['credits'];

    $sql->execute();

    $last_id = $this->getLink()->insert_id;

    $sqlTwo = $this->getLink()->prepare("INSERT INTO college (course_id, semester_id) VALUES (?, ?)");
    $sqlTwo->bind_param("ii", $courseId, $semesterId);

    $courseId = $last_id;
    $semesterId = $_POST['semester'];

    $sqlTwo->execute();

    header("location: ../");
  }
}

$config = new Config();
$link = $config->connectToServer();

$addClass = new AddClass();
$addClass->setLink($link);
$addClass->setIsAdmin();

if(!$addClass->getIsAdmin()) {
  header("location: ../");
}

$addClass->addClass();
