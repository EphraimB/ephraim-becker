<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

  global $link;


  $sql = $link->prepare("UPDATE timeline SET hide = 0 WHERE TimelineId=?");
  $sql->bind_param("i", $id);

  $id = $_GET['id'];

  $sql->execute();

  $sql->close();
  $link->close();

  header("location: index.php");
?>
