<?php
  session_start();

  unset($_SESSION['username']);

  $url = $_GET['fromUrl'];

  header("location: " . $url);
?>
