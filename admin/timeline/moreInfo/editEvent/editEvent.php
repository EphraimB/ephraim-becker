<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;

  $sql = $link->prepare("UPDATE timeline SET MemoryType=?, DateModified=?, EventDate=?, EventTime=?, EndEventDate=?, EventTimeZone=?, EventTimeZoneOffset=?, EventTitle=?, EventDescription=?, EventMedia=?, EventMediaPortrait=?, EventMediaDescription=?, EventYouTubeLink=?, hide=? WHERE TimelineId=?");
  $sql->bind_param('isssssisssissii', $memory, $dateModified, $eventDate, $eventTime, $endEventDate, $eventTimeZone, $eventTimeZoneOffset, $eventTitle, $eventDescription, $eventImage, $eventMediaPortrait, $eventImageDescription, $eventYouTubeLink, $hidden, $id);

  $id = $_POST['id'];

  $dateModified = date("Y-m-d H:i:s");

  $memory = $_POST['memory'];

  if(isset($_POST['allDay'])) {
    $eventTime = NULL;
    $eventDate = $_POST['eventDate'];
  } else {
    $eventTime = date('H:i:s', strtotime($_POST['eventTime']) + intval($_POST['timezoneOffset']));
    $eventDate = date('Y-m-d', strtotime($_POST['eventDate'] . " " . $_POST['eventTime']) + intval($_POST['timezoneOffset']));
  }

  if(!isset($_POST['endEventDateExist'])) {
    $endEventDate = NULL;
  } else {
    $endEventDate = $_POST['endEventDate'];
  }

  $eventTimeZone = $_POST['timezone'];
  $eventTimeZoneOffset = $_POST['timezoneOffset'];

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
    if(is_null($eventImageDescription)) {
      echo "Sorry, no image description inputted";
      $uploadOk = 0;
    }

    $eventImage = strtolower(str_replace(' ', '-', $eventImageDescription . '.jpg'));

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

    $exif = exif_read_data($_FILES["eventImage"]["tmp_name"]);

    // Get new dimensions
    list($width, $height) = getimagesize($_FILES["eventImage"]["tmp_name"]);

    $image = imagecreatefromjpeg($_FILES["eventImage"]["tmp_name"]);

    if(isset( $exif["Orientation"])) {
      if($exif["Orientation"] == 6) {
          // photo needs to be rotated
          $image = imagerotate($image , -90, 0 );

          $newWidth = $height;
          $newHeight = $width;

          $width = $newWidth;
          $height = $newHeight;
      }
    }

    if($width > $height) {
      $new_width = 200;
      $new_height = 113;

      $eventMediaPortrait = 0;
    } else {
      $new_width = 113;
      $new_height = 200;

      $eventMediaPortrait = 1;
    }

    // Resample
    $image_p = imagecreatetruecolor($new_width, $new_height);
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
        header("location: ../");
      } else {
        echo "Sorry, there was an error uploading your file.";
      }
  }
} else {
  $sqlTwo = $link->prepare("SELECT EventMedia, EventMediaDescription, EventMediaPortrait FROM timeline WHERE TimelineId=?");
  $sqlTwo->bind_param("i", $id);

  $id = $_POST['id'];

  $sqlTwo->execute();

  $sqlTwoResult = $sqlTwo->get_result();

  while($row = mysqli_fetch_array($sqlTwoResult)){
    $eventImage = $row['EventMedia'];
    $eventImageDescription = $row['EventMediaDescription'];
    $eventMediaPortrait = $row['EventMediaPortrait'];
  }
}

$sql->execute();

$sql->close();
$link->close();

header("location: ../index.php?id=$id");
?>
