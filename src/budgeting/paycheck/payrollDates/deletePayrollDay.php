<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class DeletePayrollDay
{
  private $isAdmin;
  private $link;
  private $id;

  function __construct()
  {
    $this->setIsAdmin();

    if(!$this->getIsAdmin()) {
      header("location: ../../");
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

  function setId($id): void
  {
    $this->id = $id;
  }

  function getId(): int
  {
    return $this->id;
  }

  function deletePayrollDay(): void
  {
    $sql = $this->getLink()->prepare("DELETE FROM payrollDates WHERE payrollDates_id=?");
    $sql->bind_param('i', $id);

    $id = $this->getId();

    $sql->execute();

    $sql->close();

    header("location: index.php");
  }
}
$config = new Config();
$link = $config->connectToServer();

$deletePayrollDay = new DeletePayrollDay();
$deletePayrollDay->setLink($link);
$deletePayrollDay->setId(intval($_GET['id']));

$deletePayrollDay->deletePayrollDay();
