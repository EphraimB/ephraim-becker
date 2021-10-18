<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;


  $sql = $link->prepare("DELETE FROM timeline WHERE TimelineId=?");
  $sql->bind_param("i", $id);

  $id = $_GET['id'];

  $sql->execute();

  $sql->close();
  $link->close();

  header("location: index.php");
?>
