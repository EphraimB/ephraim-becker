<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

  global $link;

  $id = $_GET['id'];

  $sql = "DELETE FROM timeline WHERE TimelineId = $id";

  if ($link->query($sql) === TRUE) {
    header("location: index.php");
  } else {
    echo "Error updating record: " . $link->error;
  }

  $link->close();
?>
