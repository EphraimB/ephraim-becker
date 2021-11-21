<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class EditEvent
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
  private $id;
  private $year;
  private $month;
  private $day;

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

  function setYear($year): void
  {
    $this->year = $year;
  }

  function getYear(): int
  {
    return $this->year;
  }

  function setMonth($month): void
  {
    $this->month = $month;
  }

  function getMonth(): int
  {
    return $this->month;
  }

  function setDay($day): void
  {
    $this->day = $day;
  }

  function getDay(): int
  {
    return $this->day;
  }

  function setId($id): void
  {
    $this->id = $id;
  }

  function getId(): int
  {
    return $this->id;
  }

  function setEventDate($eventDate): void
  {
    $this->eventDate = $eventDate;
  }

  function getEventDate(): string
  {
    return $this->eventDate;
  }

  function setEventTime($eventTime): void
  {
    $this->eventTime = $eventTime;
  }

  function getEventTime()
  {
    return $this->eventTime;
  }

  function setEndEventDate($endEventDate): void
  {
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
    return $this->eventTimeZoneOffset;
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

  function getEventYouTubeLink()
  {
    return $this->eventYouTubeLink;
  }

  function setHidden($hidden): void
  {
    $this->hidden = $hidden;
  }

  function getHidden(): int
  {
    return $this->hidden;
  }

  function setEventImageDescription($eventImageDescription): void
  {
    $this->eventImageDescription = $eventImageDescription;
  }

  function getEventImageDescription()
  {
    return $this->eventImageDescription;
  }

  function setEventImage($eventImage): void
  {
    $this->eventImage = $eventImage;
  }

  function getEventImage()
  {
    return $this->eventImage;
  }

  function setMemory($memory): void
  {
    $this->memory = $memory;
  }

  function getMemory(): int
  {
    return $this->memory;
  }

  function setImageWidth($width): void
  {
    $this->imageWidth = $width;
  }

  function getImageWidth(): int
  {
    return $this->imageWidth;
  }

  function setImageHeight($height): void
  {
    $this->imageHeight = $height;
  }

  function getImageHeight(): int
  {
    return $this->imageHeight;
  }

  function setEventMediaPortrait($eventMediaPortrait): void
  {
    $this->eventMediaPortrait = $eventMediaPortrait;
  }

  function getEventMediaPortrait()
  {
    return $this->eventMediaPortrait;
  }

  function addImage(): void
  {
    if($_FILES['eventImage']['size'] > 0) {
      if(is_null($this->getEventImageDescription())) {
        echo "Sorry, no image description inputted";
        $uploadOk = 0;
      }

      $eventImage = strtolower(str_replace(' ', '-', $this->getEventImageDescription() . '.jpg'));

      $target_dir = '../img/';
      $target_file = $target_dir . $eventImage;
      $uploadOk = 1;
      $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

      // Check if file already exists
      if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
      }

      $exif = exif_read_data($_FILES["eventImage"]["tmp_name"]);

      list($width, $height) = getimagesize($_FILES["eventImage"]["tmp_name"]);

      $image = imagecreatefromjpeg($_FILES["eventImage"]["tmp_name"]);

      $this->setImageWidth($width);
      $this->setImageHeight($height);

      if(isset($exif["Orientation"])) {
        if($exif["Orientation"] == 6) {
          $image = imagerotate($image , -90, 0 );
          $width = $this->getImageHeight();
          $height = $this->getImageWidth();
        }
      }

      if($this->getImageWidth() > $this->getImageHeight()) {
        $eventMediaPortrait = 0;
      } else {
        $eventMediaPortrait = 1;
      }

      $this->setEventMediaPortrait($eventMediaPortrait);

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
        if (!move_uploaded_file($_FILES["eventImage"]["tmp_name"], $target_file)) {
          echo "Sorry, there was an error uploading your file.";
        }
      }
    } else {
      $sqlTwo = $this->getLink()->prepare("SELECT EventMedia, EventMediaDescription, EventMediaPortrait FROM timeline WHERE TimelineId=?");
      $sqlTwo->bind_param("i", $id);

      $id = $this->getId();

      $sqlTwo->execute();

      $sqlTwoResult = $sqlTwo->get_result();

      while($row = mysqli_fetch_array($sqlTwoResult)) {
        $eventImage = $row['EventMedia'];
        $this->setEventImageDescription($row['EventMediaDescription']);
        $this->setEventMediaPortrait(intval($row['EventMediaPortrait']));
      }
    }

    $this->setEventImage($eventImage);
  }

  function removeEvent(): void
  {
    $sql = $this->getLink()->prepare("UPDATE timeline SET MemoryType=?, DateModified=?, EventDate=?, EventTime=?, EndEventDate=?, EventTimeZone=?, EventTimeZoneOffset=?, EventTitle=?, EventDescription=?, EventMedia=?, EventMediaPortrait=?, EventMediaDescription=?, EventYouTubeLink=?, hide=? WHERE TimelineId=?");
    $sql->bind_param('isssssisssissii', $memory, $dateModified, $eventDate, $eventTime, $endEventDate, $eventTimeZone, $eventTimeZoneOffset, $eventTitle, $eventDescription, $eventImage, $eventMediaPortrait, $eventImageDescription, $eventYouTubeLink, $hidden, $id);

    $id = $this->getId();
    $dateModified = date("Y-m-d H:i:s");
    $memory = $this->getMemory();
    $eventDate = $this->getEventDate();
    $eventTime = $this->getEventTime();
    $endEventDate = $this->getEndEventDate();
    $eventTimezone = $this->getEventTimeZone();
    $eventTimeZoneOffset = $this->getEventTimeZoneOffset();
    $eventTitle = $this->getEventTitle();
    $eventDescription = $this->getEventDescription();

    $this->addImage();

    $eventImage = $this->getEventImage();
    $eventMediaPortrait = $this->getEventMediaPortrait();
    $eventImageDescription = $this->getEventImageDescription();
    $eventYouTubeLink = $this->getEventYouTubeLink();
    $hidden = $this->getHidden();

    $sql->execute();

    $sql->close();
    $this->getLink()->close();

    if($this->getYear() == 0) {
      header("location: ../moreInfo/index.php?id=" . $this->getId());
    } else {
      header("location: ../index.php?year=" . $this->getYear() . "&month=" . $this->getMonth() . "&day=" . $this->getDay());
    }
  }
}
$config = new Config();
$link = $config->connectToServer();

$editEvent = new EditEvent();
$editEvent->setLink($link);
$editEvent->setId(intval($_POST['id']));
$editEvent->setYear(intval($_POST['year']));
$editEvent->setMonth(intval($_POST['month']));
$editEvent->setDay(intval($_POST['day']));
$editEvent->setMemory(intval($_POST['memory']));
$editEvent->setEventTimezone($_POST['timezone']);
$editEvent->setEventTimezoneOffset(intval($_POST['timezoneOffset']));

if(isset($_POST['allDay'])) {
  $eventTime = NULL;
  $eventDate = $_POST['eventDate'];
} else {
  $eventTime = date('H:i:s', strtotime($_POST['eventTime']) + $editEvent->getEventTimeZoneOffset());
  $eventDate = date('Y-m-d', strtotime($_POST['eventDate'] . " " . $_POST['eventTime']) + $editEvent->getEventTimeZoneOffset());
}
$editEvent->setEventDate($eventDate);
$editEvent->setEventTime($eventTime);

if(!isset($_POST['endEventDateExist'])) {
  $endEventDate = NULL;
} else {
  $endEventDate = $_POST['endEventDate'];
}
$editEvent->setEndEventDate($endEventDate);

$editEvent->setEventTitle($_POST['eventTitle']);
$editEvent->setEventDescription($_POST['eventDescription']);

if(empty($_POST['eventImageDescription'])) {
  $eventImageDescription = NULL;
} else {
  $eventImageDescription = $_POST['eventImageDescription'];
}
$editEvent->setEventImageDescription($eventImageDescription);

if(empty($_POST['eventYouTubeLink'])) {
  $eventYouTubeLink = NULL;
} else {
  $eventYouTubeLink = $_POST['eventYouTubeLink'];
}
$editEvent->setEventYouTubeLink($eventYouTubeLink);

if(empty($_POST['hidden'])) {
  $hidden = 0;
} else {
  $hidden = $_POST['hidden'];
}
$editEvent->setHidden(intval($hidden));

$editEvent->removeEvent();
?>
