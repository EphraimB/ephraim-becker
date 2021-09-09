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

  $eventImage = basename($_FILES["eventImage"]["name"]);

  $target_dir = '../../../timeline/img/';
  $target_file = $target_dir . $eventImage;
  $uploadOk = 1;
  $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

  // Check if image file is a actual image or fake image
  if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["eventImage"]["tmp_name"]);
    if($check !== false) {
      $uploadOk = 1;
    } else {
      $uploadOk = 0;
    }
  }

  // Check if file already exists
  if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
  }

  // Check file size
  if ($_FILES["eventImage"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
  }

  // Allow certain file formats
  if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
  && $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
  }

  // Check if $uploadOk is set to 0 by an error
  if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
  // if everything is ok, try to upload file
  } else {
    if (move_uploaded_file($_FILES["eventImage"]["tmp_name"], $target_file)) {
      $eventImage = basename($_FILES["eventImage"]["name"]);
      header("location: ../");
    } else {
      echo "Sorry, there was an error uploading your file.";
    }


$sql->execute();

$sql->close();
$link->close();
?>
