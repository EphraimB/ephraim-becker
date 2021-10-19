<?php
  session_start();

  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;

  if(!isset($_SESSION['username'])) {
    header("location: ../");
  }

  $id = $_GET['id'];

  $title = "Ephraim Becker - Admin - Timeline - Delete thought?";
  $localStyleSheet = '<link rel="stylesheet" href="../css/style.css" />';
  $header = "Admin - Timeline - Delete thought?";

  $body = '<h2>Are you sure you want to delete this thought"?</h2>';

  $sqlTwo = $link->prepare("SELECT TimelineId FROM thoughts WHERE ThoughtId=? LIMIT 1");

  $sqlTwo->bind_param("i", $id);

  $sqlTwo->execute();

  $sqlTwoResult = $sqlTwo->get_result();

  while($row = mysqli_fetch_array($sqlTwoResult)) {
    $timelineId = $row['TimelineId'];
  }

  $sqlTwo->close();
  $link->close();

  $body .= '<div class="row actionButtons">
        <a class="keep" href="index.php?id=' . $timelineId . '">No</a>
        <a class="delete" href="deleteThought.php?id=' . $id . '">Yes</a>
      </div>';

  require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
?>
