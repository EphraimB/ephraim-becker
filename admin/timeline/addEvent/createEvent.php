<?php
  define('__ROOT__', dirname(dirname(__FILE__)));
  require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

  global $link;


  $sql = $link->prepare("INSERT INTO timeline (MemoryType, DateCreated, DateModified, EventDate, EventTime, EventTitle, EventDescription, EventMedia, EventMediaDescription, EventYouTubeLink)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $sql->bind_param('isssssssss', $memory, date("Y-m-d"), date("Y-m-d"), $eventDate, $eventTime, $eventTitle, $eventDescription, $eventImage, $eventImageDescription, $eventYouTubeLink);

  $memory = intval($_POST['memory']);

  $eventDate = $_POST['eventDate'];

  if(isset($_POST['allDay'])) {
    $eventTime = NULL;
  } else {
    $eventTime = $_POST['eventTime'];
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

  $sql->execute();

  echo "New record created successfully";

  $sql->close();
  $link->close();
?>
