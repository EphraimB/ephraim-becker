<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

  global $link;


  $id = $_POST['id'];

  $sql = $link->prepare("UPDATE timeline SET MemoryType=?, DateModified=?, EventDate=?, EventTime=?, EndEventDate=?, EventTitle=?, EventDescription=?, EventMedia=?, EventMediaDescription=?, EventYouTubeLink=?, hide=? WHERE TimelineId=" . $id);
  $sql->bind_param('isssssssssi', $memory, $dateModified, $eventDate, $eventTime, $endEventDate, $eventTitle, $eventDescription, $eventImage, $eventImageDescription, $eventYouTubeLink, $hidden);


  $dateModified = date("Y-m-d H:i:s");

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

  if($_FILES['eventImage']['size'] > 0) {
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

    $percent = 0.5;

    // Get new dimensions
    list($width, $height) = getimagesize($_FILES["eventImage"]["tmp_name"]);
    $new_width = 200;
    $new_height = 113;

    // Resample
    $image_p = imagecreatetruecolor($new_width, $new_height);
    $image = imagecreatefromjpeg($_FILES["eventImage"]["tmp_name"]);
    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    // Output
    imagejpeg($image_p, $_FILES["eventImage"]["tmp_name"], 100);

    // Allow certain file formats
    if($imageFileType != "jpg") {
      echo "Sorry, only JPG and JPEG files are allowed.";
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
  }
} else {
  $sqlTwo = "SELECT EventMedia, EventMediaDescription FROM timeline WHERE TimelineId=" . $id;

  $sqlTwoResult = mysqli_query($link, $sqlTwo);

  while($row = mysqli_fetch_array($sqlTwoResult)){
    $eventImage = $row['EventMedia'];
    $eventImageDescription = $row['EventMediaDescription'];
  }
}

$sql->execute();

$sql->close();
$link->close();

header("location: ../");
?>
