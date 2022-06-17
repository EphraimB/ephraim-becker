<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class Login
{
  private $link;
  private $url;
  private $query;

  function __construct()
  {
    $this->setQuery("SELECT * FROM admins WHERE username=?");
  }

  function setLink($link): void
  {
    $this->link = $link;
  }

  function getLink()
  {
    return $this->link;
  }

  function setQuery($query): void
  {
    $this->query = $query;
  }

  function getQuery(): string
  {
    return $this->query;
  }

  function setUrl($url): void
  {
    $this->url = $url;
  }

  function getUrl(): string
  {
    return $this->url;
  }

  function fetchFromDatabase(): mysqli_result
  {
    $sql = $this->getLink()->prepare($this->getQuery());
    $sql->bind_param("s", $username);

    $username = $_POST['username'];

    $sql->execute();

    $sqlResult = $sql->get_result();

    return $sqlResult;
  }

  function login(): void
  {
    $sqlResult = $this->fetchFromDatabase();
    $loggedIn = false;

    while($row = mysqli_fetch_array($sqlResult)) {
      $password = $row['password'];
    }

    if(password_verify($_POST['password'], $password)) {
      $_SESSION['username'] = $_POST['username'];

      $loggedIn = true;
    }

    header("location: " . $this->getUrl() . '?loggedIn=' . $loggedIn);
  }
}

$config = new Config();
$link = $config->connectToServer();

$login = new Login();
$login->setLink($link);
$login->setUrl($_POST['url']);
$login->login();
?>
