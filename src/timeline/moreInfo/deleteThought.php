<?php
  session_start();

  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;


  if(!isset($_SESSION['username'])) {
    header("location: ../");
  }
  
  $sqlTwo = $link->prepare("SELECT TimelineId FROM thoughts WHERE ThoughtId=? LIMIT 1");

  $sqlTwo->bind_param("i", $id);

  $id = $_GET['id'];

  $sqlTwo->execute();

  $sqlTwoResult = $sqlTwo->get_result();

  while($row = mysqli_fetch_array($sqlTwoResult)) {
    $timelineId = $row['TimelineId'];
  }

  $sqlTwo->close();

  $sql = $link->prepare("DELETE FROM thoughts WHERE ThoughtId=?");
  $sql->bind_param("i", $id);

  $sql->execute();

  $sql->close();
  $link->close();

  header("location: index.php?id=". $timelineId);
?>
