<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class AddEvent
{
  private $isAdmin;
  private $link;
  private $eventDate;
  private $eventTime;
  private $endEventDate;
  private $eventTimeZone;
  private $eventTimeZoneOffset;
  private $eventTitle;
  private $eventDescription;
  private $eventYouTubeLink;
  private $hidden;
  private $eventImageDescription;
  private $eventImage;
  private $memory;
  private $eventMediaPortrait;
  private $imageWidth;
  private $imageHeight;

  function __construct()
  {
    $this->setIsAdmin();

    if(!$this->getIsAdmin()) {
      header("location: ../");
    }
  }

  function setIsAdmin(): void
  {
    if(isset($_SESSION['username'])) {
      $this->isAdmin = true;
    } else {
      $this->isAdmin = false;
    }
  }

  function getIsAdmin(): bool
  {
    return $this->isAdmin;
  }

  function setLink($link)
  {
    $this->link = $link;
  }

  function getLink()
  {
    return $this->link;
  }

  function setEventDate($eventDate): void
  {
    $this->setEventTimeZoneOffset($_POST['timezoneOffset']);

    if(isset($_POST['allDay'])) {
      $eventDate = $eventDate;
    } else {
      $eventDate = date('Y-m-d', strtotime($eventDate . " " . $this->getEventTime() + $this->getEventTimeZoneOffset()));
    }

    $this->eventDate = $eventDate;
  }

  function getEventDate(): string
  {
    return $this->eventDate;
  }

  function setEventTime($eventTime): void
  {
    $this->setEventTimeZoneOffset($_POST['timezoneOffset']);

    if(isset($_POST['allDay'])) {
      $eventTime = NULL;
    } else {
      $eventTime = date('H:i:s', strtotime($eventTime) + $this->getEventTimeZoneOffset());
    }

    $this->eventTime = $eventTime;
  }

  function getEventTime()
  {
    return $this->eventTime;
  }

  function setEndEventDate($endEventDate): void
  {
    if(!isset($_POST['endEventDateExist'])) {
      $endEventDate = NULL;
    } else {
      $endEventDate = $_POST['endEventDate'];
    }

    $this->endEventDate = $endEventDate;
  }

  function getEndEventDate()
  {
    return $this->endEventDate;
  }

  function setEventTimeZone($eventTimeZone): void
  {
    $this->eventTimeZone = $eventTimeZone;
  }

  function getEventTimeZone(): string
  {
    return $this->eventTimeZone;
  }

  function setEventTimeZoneOffset($eventTimeZoneOffset): void
  {
    $this->eventTimeZoneOffset = $eventTimeZoneOffset;
  }

  function getEventTimeZoneOffset(): int
  {
    return intval($this->eventTimeZoneOffset);
  }

  function setEventTitle($eventTitle): void
  {
    $this->eventTitle = $eventTitle;
  }

  function getEventTitle(): string
  {
    return $this->eventTitle;
  }

  function setEventDescription($eventDescription): void
  {
    $this->eventDescription = $eventDescription;
  }

  function getEventDescription(): string
  {
    return $this->eventDescription;
  }

  function setEventYouTubeLink($eventYouTubeLink): void
  {
    $this->eventYouTubeLink = $eventYouTubeLink;
  }

  function getEventYouTubeLink(): string
  {
    return $this->eventYouTubeLink;
  }

  function setHidden($hidden): void
  {
    $this->hidden = $hidden;
  }

  function getHidden(): int
  {
    return intval($this->hidden);
  }

  function setEventImageDescription($eventImageDescription): void
  {
    $this->eventImageDescription = $eventImageDescription;
  }

  function getEventImageDescription(): string
  {
    return $this->eventImageDescription;
  }

  function setEventImage($eventImage): void
  {
    if($_FILES['eventImage']['size'] > 0) {
      if(is_null($eventImageDescription)) {
        echo "Sorry, no image description inputted";
        $uploadOk = 0;
      }

      $eventImage = strtolower(str_replace(' ', '-', $this->getEventImageDescription() . '.jpg'));

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

      $this->addImage();

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
      $eventImage = NULL;
    }

    $this->eventImage = $eventImage;
  }

  function getEventImage(): string
  {
    return $this->eventImage;
  }

  function setMemory($memory): void
  {
    $this->memory = $memory;
  }

  function getMemory(): int
  {
    return intval($this->memory);
  }

  function setImageWidth($width): void
  {
    $this->imageWidth = $width;
  }

  function getImageWidth(): int
  {
    return $this->imageWidth;
  }

  function setEventMediaPortrait(): void
  {
    if(isset($exif["Orientation"])) {
      if($exif["Orientation"] == 6) {
          $width = $this->getImageHeight();
          $height = $this->getImageWidth();
      }
    }

    if($this->getImageWidth() > $this->getImageHeight()) {
      $eventMediaPortrait = 0;
    } else {
      $eventMediaPortrait = 1;
    }

    $this->eventMediaPortrait = $eventMediaPortrait;
  }

  function getEventMediaPortrait(): int
  {
    return $this->eventMediaPortrait;
  }

  function addImage()
  {
    if($_FILES['eventImage']['size'] > 0) {
      $image = imagecreatefromjpeg($_FILES["eventImage"]["tmp_name"]);

      list($width, $height) = getimagesize($_FILES["eventImage"]["tmp_name"]);

      $this->setImageWidth($width);
      $this->setImageHeight($height);

      if(isset($exif["Orientation"])) {
        if($exif["Orientation"] == 6) {
            // photo needs to be rotated
            $image = imagerotate($image , -90, 0 );
        }
      }

      if($this->getEventMediaPortrait() == 0) {
        $new_width = 200;
        $new_height = 113;
      } else if($this->getEventMediaPortrait() == 1) {
        $new_width = 113;
        $new_height = 200;
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
      $eventImage = NULL;
    }

    return $eventImage;
  }

  function formatEventDate(): string
  {
    if(isset($_POST['allDay'])) {
      $eventTime = NULL;
      $eventDate = $this->getEventDate();
    } else {
      $eventTime = date('H:i:s', strtotime($this->getEventTime()) + $this->getEventTimeZoneOffset());
      $eventDate = date('Y-m-d', strtotime($this->getEventDate() . " " . $this->getEventTime()) + $this->getEventTimeZoneOffset());
    }

    if(!isset($_POST['endEventDateExist'])) {
      $endEventDate = NULL;
    } else {
      $endEventDate = $_POST['endEventDate'];
    }
  }

  function createEvent(): void
  {
    $sql = $this->getLink()->prepare("INSERT INTO timeline (MemoryType, DateCreated, DateModified, EventDate, EventTime, EndEventDate, EventTimeZone, EventTimeZoneOffset, EventTitle, EventDescription, EventMedia, EventMediaPortrait, EventMediaDescription, EventYouTubeLink, hide)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $sql->bind_param('issssssisssissi', $memory, $dateNow, $dateNow, $eventDate, $eventTime, $endEventDate, $eventTimeZone, $eventTimeZoneOffset, $eventTitle, $eventDescription, $eventImage, $eventMediaPortrait, $eventImageDescription, $eventYouTubeLink, $hidden);

    $dateNow = date("Y-m-d H:i:s");

    $memory = $this->getMemory();
    $eventDate = $this->getEventDate();
    $eventTime = $this->getEventTime();
    $endEventDate = $this->getEndEventDate();
    $eventTimeZone = $this->getEventTimeZone();
    $eventTimeZoneOffset = $this->getEventTimeZoneOffset();
    $eventTitle = $this->getEventTitle();
    $eventDescription = $this->getEventDescription();
    $eventImage = $this->getEventImage();
    $eventMediaPortrait = $this->getEventMediaPortrait();
    $eventImageDescription = $this->getEventImageDescription();
    $eventYouTubeLink = $this->getEventYouTubeLink();
    $hidden = $this->getHidden();

    $sql->execute();

    $sql->close();
    $this->getLink()->close();

    header("location: ../");
  }
}

$config = new Config();
$link = $config->connectToServer();

$addEvent = new AddEvent();
$addEvent->setLink($link);
$addEvent->setMemory($_POST['memory']);
$addEvent->setEventDate($_POST['eventDate']);
$addEvent->setEventTime($_POST['eventTime']);
$addEvent->setEndEventDate($_POST['endEventDate']);
$addEvent->setEventTimezone($_POST['timezone']);
$addEvent->setEventTimezoneOffset($_POST['timezoneOffset']);
$addEvent->setEventTitle($_POST['eventTitle']);
$addEvent->setEventDescription($_POST['eventDescription']);
$addEvent->setEventImageDescription($_POST['eventImageDescription']);
$addEvent->setEventYouTubeLink($_POST['eventYouTubeLink']);
$addEvent->setHidden($_POST['hidden']);
$addEvent->createEvent();

  // $memory = $_POST['memory'];
  //
  // if(isset($_POST['allDay'])) {
  //   $eventTime = NULL;
  //   $eventDate = $_POST['eventDate'];
  // } else {
  //   $eventTime = date('H:i:s', strtotime($_POST['eventTime']) + intval($_POST['timezoneOffset']));
  //   $eventDate = date('Y-m-d', strtotime($_POST['eventDate'] . " " . $_POST['eventTime']) + intval($_POST['timezoneOffset']));
  // }
  //
  // if(!isset($_POST['endEventDateExist'])) {
  //   $endEventDate = NULL;
  // } else {
  //   $endEventDate = $_POST['endEventDate'];
  // }
  //
  // $eventTimeZone = $_POST['timezone'];
  // $eventTimeZoneOffset = $_POST['timezoneOffset'];
  //
  // $eventTitle = $_POST['eventTitle'];
  // $eventDescription = $_POST['eventDescription'];
  //
  // if(empty($_POST['eventImageDescription'])) {
  //   $eventImageDescription = NULL;
  // } else {
  //   $eventImageDescription = $_POST['eventImageDescription'];
  // }
  //
  // if(empty($_POST['eventYouTubeLink'])) {
  //   $eventYouTubeLink = NULL;
  // } else {
  //   $eventYouTubeLink = $_POST['eventYouTubeLink'];
  // }
  //
  // if(empty($_POST['hidden'])) {
  //   $hidden = 0;
  // } else {
  //   $hidden = $_POST['hidden'];
  // }
?>
