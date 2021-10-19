<?php
  session_start();

  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;

  if(!isset($_SESSION['username'])) {
    header("location: ../");
  }

  $sql = $link->prepare("UPDATE timeline SET hide = 1 WHERE TimelineId=?");
  $sql->bind_param("i", $id);

  $id = $_GET['id'];

  $sql->execute();

  $sql->close();
  $link->close();

  $year = $_GET['year'];
  $month = $_GET['month'];
  $day = $_GET['day'];

  header("location: index.php#" . $year . "-" . $month . "-" . $day);
?>
