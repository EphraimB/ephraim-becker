<?php
  session_start();

  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;

  if(!isset($_SESSION['username'])) {
    header("location: ../");
  }
  
  $sql = $link->prepare("INSERT INTO thoughts (TimelineId, DateCreated, DateModified, Thought)
  VALUES (?, ?, ?, ?)");
  $sql->bind_param("isss", $id, $now, $now, $thought);

  $id = $_POST['id'];
  $now = date("Y-m-d H:i:s");
  $thought = $_POST['thought'];

  $sql->execute();

  $sql->close();
  $link->close();

  header("location: index.php?id=". $id);
?>
