<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditDemographicRow
{
  private $isAdmin;
  private $link;
  private $id;
  private $yeshivish;
  private $neurotypical;
  private $autism;
  private $older;

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

  function setId($id): void
  {
    $this->id = $id;
  }

  function getId(): int
  {
    return $this->id;
  }

  function setYeshivish($yeshivish): void
  {
    $this->yeshivish = $yeshivish;
  }

  function getYeshivish(): string
  {
    return $this->yeshivish;
  }

  function setNeurotypical($neurotypical): void
  {
    $this->neurotypical = $neurotypical;
  }

  function getNeurotypical(): string
  {
    return $this->neurotypical;
  }

  function setAutism($autism): void
  {
    $this->autism = $autism;
  }

  function getAutism(): string
  {
    return $this->autism;
  }

  function setOlder($older): void
  {
    $this->older = $older;
  }

  function getOlder(): string
  {
    return $this->older;
  }

  function editDemographicRow(): void
  {
    $sql = $this->getLink()->prepare("UPDATE demographics SET yeshivish = ?, neurotypical = ?, autism = ?, older = ?, dateModified = ? WHERE demographicId = ?");
    $sql->bind_param('sssssi', $yeshivish, $neurotypical, $autism, $older, $dateNow, $id);

    $yeshivish = $this->getYeshivish();
    $neurotypical = $this->getNeurotypical();
    $autism = $this->getAutism();
    $older = $this->getOlder();

    $dateNow = date("Y-m-d H:i:s");

    $id = $this->getId();

    $sql->execute();

    header("location: ../");
  }
}

$config = new Config();
$link = $config->connectToServer();

$editDemographicRow = new EditDemographicRow();
$editDemographicRow->setLink($link);
$editDemographicRow->setId(intval($_POST['id']));
$editDemographicRow->setYeshivish($_POST['yeshivishFriends']);
$editDemographicRow->setNeurotypical($_POST['neurotypicalFriends']);
$editDemographicRow->setAutism($_POST['autisticFriends']);
$editDemographicRow->setOlder($_POST['olderFriends']);

$editDemographicRow->editDemographicRow();
?>
