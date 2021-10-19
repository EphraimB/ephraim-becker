<?php
  session_start();

  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;

  if(!isset($_SESSION['username'])) {
    header("location: ../");
  }

  $sql = $link->prepare("UPDATE timeline SET hide = 0 WHERE TimelineId=?");
  $sql->bind_param("i", $id);

  $id = $_GET['id'];

  $sql->execute();

  $sql->close();
  $link->close();

  header("location: index.php?id=". $id);
?>
