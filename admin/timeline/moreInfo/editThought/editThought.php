<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;

  $sql = $link->prepare("UPDATE thoughts SET DateModified=?, Thought=?, hide=? WHERE ThoughtId=?");
  $sql->bind_param('ssii', $dateModified, $thought, $hidden, $id);

  $id = $_POST['id'];

  $dateModified = date("Y-m-d H:i:s");
  $thought = $_POST['thought'];

  if(empty($_POST['hidden'])) {
    $hidden = 0;
  } else {
    $hidden = $_POST['hidden'];
  }


  $sql->execute();

  $sql->close();

  $sqlTwo = $link->prepare("SELECT TimelineId FROM thoughts WHERE ThoughtId=? LIMIT 1");

  $sqlTwo->bind_param("i", $id);

  $sqlTwo->execute();

  $sqlTwoResult = $sqlTwo->get_result();

  while($row = mysqli_fetch_array($sqlTwoResult)) {
    $timelineId = $row['TimelineId'];
  }

  $sqlTwo->close();
  $link->close();

  header("location: ../index.php?id=". $timelineId);
?>
