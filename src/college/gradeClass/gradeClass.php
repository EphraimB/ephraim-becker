<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class GradeClass
{
  private $isAdmin;
  private $course_id;
  private $link;
  private $grade;

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

  function setGrade($grade): void
  {
    $this->grade = $grade;
  }

  function getGrade(): string
  {
    return $this->grade;
  }

  function setCourseId($id): void
  {
    $this->course_id = $id;
  }

  function getCourseId(): int
  {
    return $this->course_id;
  }

  function gradeClass(): void
  {
    $sql = $this->getLink()->prepare("UPDATE course SET grade = ? WHERE course_id = ?");
    $sql->bind_param("si", $grade, $courseId);

    $grade = $this->getGrade();
    $courseId = $this->getCourseId();

    $sql->execute();

    header("location: ../");
  }
}

$config = new Config();
$link = $config->connectToServer();

$gradeClass = new GradeClass();
$gradeClass->setLink($link);

$gradeClass->setCourseId(intval($_POST['id']));
$gradeClass->setGrade($_POST['grade']);

$gradeClass->gradeClass();
?>
