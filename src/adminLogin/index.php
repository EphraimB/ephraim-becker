<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class LoginForm extends Base
{
  private $url;
  private $link;
  private $isAdmin;

  function __construct()
  {

  }

  function setLink($link): void
  {
    $this->link = $link;
  }

  function getLink(): string
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

  function getIsAdmin(): string
  {
    return $this->isAdmin;
  }

  function setUrl($url): void
  {
    $this->url = $url;
  }

  function getUrl(): string
  {
    return $this->url;
  }

  function main(): string
  {
    $body = '<form action="login.php" method="post">
                <div class="row">
                  <label style="font-weight: bold;" for="username">User Name: </label>
                  <input type="text" id="username" name="username" />
                </div>
                <br />
                <div class="row">
                  <label style="font-weight: bold;" for="password">Password: </label>
                  <input type="password" id="password" name="password" />
                </div>
                <input type="hidden" name="url" id="url" value="' . $this->getUrl() . '" />
                <br />
                <input type="submit" name="loginButton" id="submitButton" value="Login" />
              </form>';

    return $body;
  }
}

$config = new Config();
$link = $config->connectToServer();

$loginForm = new LoginForm();
$loginForm->setLink($link);
$loginForm->setIsAdmin();
$loginForm->setTitle("Ephraim Becker - Admin login");
$loginForm->setLocalStyleSheet('css/style.css');
$loginForm->setLocalScript(NULL);
$loginForm->setHeader('Admin login');
$loginForm->setUrl($_GET['fromUrl']);
$loginForm->setBody($loginForm->main());

$loginForm->html();
?>
