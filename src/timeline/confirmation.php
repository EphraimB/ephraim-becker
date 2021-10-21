<?php
  session_start();

  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;

  if(!isset($_SESSION['username'])) {
    header("location: ../");
  }

  $id = $_GET['id'];

  $title = "Ephraim Becker - Timeline - Delete?";
  $header = "Timeline - Delete?";
  $localStyleSheet = '<link rel="stylesheet" href="css/style.css" />';
  $localScript = NULL;

  $sql = $link->prepare("SELECT EventTitle FROM timeline WHERE TimelineId=?");
  $sql->bind_param("i", $id);

  $id = $_GET['id'];

  $sql->execute();

  $sqlResult = $sql->get_result();

  while($row = mysqli_fetch_array($sqlResult)){
    $eventTitle = $row['EventTitle'];
  }

  $body = '<h2>Are you sure you want to delete the event named "' . $eventTitle . '"?</h2>

  <div class="row actionButtons">
    <a class="keep" href="index.php">No</a>
    <a class="delete" href="deleteEvent.php?id=' . $id . '">Yes</a>
  </div>';

  $url = $_SERVER['REQUEST_URI'];
  require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
?>
