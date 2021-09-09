<?php
  require_once('/home/s8gphl6pjes9/config.php');

  global $link;

  $id = $_GET['id'];

  $sql = "UPDATE timeline SET hide = 0 WHERE TimelineId = $id";

  if ($link->query($sql) === TRUE) {
    header("location: index.php");
  } else {
    echo "Error updating record: " . $link->error;
  }

  $link->close();
?>
