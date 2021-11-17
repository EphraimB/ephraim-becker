<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class RemoveClass
{
  private $isAdmin;
  private $college_id;
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

  function setCollegeId(): void
  {
    $this->college_id = $_GET['id'];
  }

  function getCollegeId(): int
  {
    return intval($this->college_id);
  }

  function removeClass(): void
  {
    $sql = $this->getLink()->prepare("SELECT course_id FROM college WHERE college_id = ?");
    $sql->bind_param("i", $collegeId);

    $collegeId = $this->getCollegeId();

    $sql->execute();

    $sqlResult = $sql->get_result();

    while($row = mysqli_fetch_array($sqlResult)) {
      $courseId = $row['course_id'];
    }

    $sqlTwo = $this->getLink()->prepare("DELETE FROM course WHERE course_id = ?");
    $sqlTwo->bind_param("i", $courseId);

    $sqlTwo->execute();

    $sqlThree = $this->getLink()->prepare("DELETE FROM college WHERE college_id = ?");
    $sqlThree->bind_param("i", $collegeId);

    $sqlThree->execute();

    header("location: index.php");
  }
}

$config = new Config();
$link = $config->connectToServer();

$removeClass = new RemoveClass();
$removeClass->setLink($link);
$removeClass->setIsAdmin();

if(!$removeClass->getIsAdmin()) {
  header("location: index.php");
}

$removeClass->setCollegeId();

$removeClass->removeClass();
