<?php
declare(strict_types=1);

session_start();

class Logout
{
  private $url;

  function __construct()
  {

  }

  function setUrl($url): void
  {
    $this->url = $url;
  }

  function getUrl(): string
  {
    return $this->url;
  }

  function logout(): void
  {
    unset($_SESSION['username']);

    header("location: " . $this->getUrl());
  }
}

$logout = new Logout();
$logout->setUrl($_GET['fromUrl']);
$logout->logout();
