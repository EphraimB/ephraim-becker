<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

  global $link;


  $sql = $link->prepare("INSERT INTO timeline (MemoryType, DateCreated, DateModified, EventDate, EventTime, EndEventDate, EventTitle, EventDescription, EventMedia, EventMediaDescription, EventYouTubeLink, hide)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $sql->bind_param('issssssssssi', $memory, $dateNow, $dateNow, $eventDate, $eventTime, $endEventDate, $eventTitle, $eventDescription, $eventImage, $eventImageDescription, $eventYouTubeLink, $hidden);


  $dateNow = date("Y-m-d H:i:s");

  $memory = $_POST['memory'];

  $eventDate = $_POST['eventDate'];

  if(isset($_POST['allDay'])) {
    $eventTime = NULL;
  } else {
    $eventTime = $_POST['eventTime'];
  }

  if(!isset($_POST['endEventDateExist'])) {
    $endEventDate = NULL;
  } else {
    $endEventDate = $_POST['endEventDate'];
  }

  $eventTitle = $_POST['eventTitle'];
  $eventDescription = $_POST['eventDescription'];

  if(empty($_POST['eventImage'])) {
    $eventImage = NULL;
  } else {
    $eventImage = $_POST['eventImage'];
  }

  if(empty($_POST['eventImageDescription'])) {
    $eventImageDescription = NULL;
  } else {
    $eventImageDescription = $_POST['eventImageDescription'];
  }

  if(empty($_POST['eventYouTubeLink'])) {
    $eventYouTubeLink = NULL;
  } else {
    $eventYouTubeLink = $_POST['eventYouTubeLink'];
  }

  if(empty($_POST['hidden'])) {
    $hidden = 0;
  } else {
    $hidden = $_POST['hidden'];
  }

  $sql->execute();

  $sql->close();
  $link->close();

  header("location: ../");
?>
