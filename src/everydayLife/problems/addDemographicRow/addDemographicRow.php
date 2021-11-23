<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class AddDemographicRow
{
  private $isAdmin;
  private $link;
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

  function addDemographicRow(): void
  {
    $sql = $this->getLink()->prepare("INSERT INTO demographics (yeshivish, neurotypical, autism, older, dateCreated, dateModified) VALUES (?, ?, ?, ?, ?, ?)");
    $sql->bind_param('ssssss', $yeshivish, $neurotypical, $autism, $older, $dateNow, $dateNow);

    $yeshivish = $this->getYeshivish();
    $neurotypical = $this->getNeurotypical();
    $autism = $this->getAutism();
    $older = $this->getOlder();

    $dateNow = date("Y-m-d H:i:s");

    $sql->execute();

    header("location: ../");
  }
}

$config = new Config();
$link = $config->connectToServer();

$addDemographicRow = new AddDemographicRow();
$addDemographicRow->setLink($link);
$addDemographicRow->setYeshivish($_POST['yeshivishFriends']);
$addDemographicRow->setNeurotypical($_POST['neurotypicalFriends']);
$addDemographicRow->setAutism($_POST['autisticFriends']);
$addDemographicRow->setOlder($_POST['olderFriends']);

$addDemographicRow->addDemographicRow();
?>
