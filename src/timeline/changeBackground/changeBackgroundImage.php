<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class ChangeBackgroundImage
{
  private $isAdmin;
  private $link;
  private $id;
  private $backgroundImage;
  private $imageDescription;
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

  function getId(): int
  {
    return $this->id;
  }

  function setId($id): void
  {
    $this->id = $id;
  }

  function setBackgroundImage($backgroundImage): void
  {
    $this->backgroundImage = $backgroundImage;
  }

  function getBackgroundImage()
  {
    return $this->backgroundImage;
  }

  function setImageDescription($imageDescription): void
  {
    $this->imageDescription = $imageDescription;
  }

  function getImageDescription(): string
  {
    return $this->imageDescription;
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

  function changeBackgroundImage(): void
  {
    if($_FILES['backgroundImage']['size'] > 0) {
      $sql = $this->getLink()->prepare("SELECT * FROM backgroundImage WHERE year=?");
      $sql->bind_param("i", $id);

      $id = $this->getId();

      $sql->execute();

      $sqlResult = $sql->get_result();

      if($sqlResult->num_rows > 0) {
        $sqlTwo = $this->getLink()->prepare("UPDATE backgroundImage SET DateModified=?, backgroundImage=?, ImageDescription=? WHERE year=?");
        $sqlTwo->bind_param("sssi", $dateModified, $backgroundImage, $imageDescription, $id);
      } else {
        $sqlTwo = $this->getLink()->prepare("INSERT INTO backgroundImage (DateCreated, DateModified, backgroundImage, ImageDescription, year) VALUES (?, ?, ?, ?, ?)");
        $sqlTwo->bind_param("ssssi", $dateCreated, $dateModified, $backgroundImage, $imageDescription, $id);
      }

      $dateCreated = date("Y-m-d H:i:s");
      $dateModified = date("Y-m-d H:i:s");

      if(is_null($this->getImageDescription())) {
        echo "Sorry, no image description inputted";
        $uploadOk = 0;
      } else {
        $imageDescription = $this->getImageDescription();
      }

      $backgroundImage = strtolower(str_replace(' ', '-', $this->getImageDescription() . '.jpg'));

      $target_dir = '../../img/eventYear/';
      $target_file = $target_dir . $backgroundImage;
      $uploadOk = 1;
      $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

      // Check if file already exists
      if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
      }

      $exif = exif_read_data($_FILES["backgroundImage"]["tmp_name"]);

      list($width, $height) = getimagesize($_FILES["backgroundImage"]["tmp_name"]);

      $image = imagecreatefromjpeg($_FILES["backgroundImage"]["tmp_name"]);

      $this->setImageWidth($width);
      $this->setImageHeight($height);

      if(isset($exif["Orientation"])) {
        if($exif["Orientation"] == 6) {
          $image = imagerotate($image , -90, 0 );
          $width = $this->getImageHeight();
          $height = $this->getImageWidth();
        }
      }

      $new_width = 268;
      $new_height = 160;

      // Resample
      $image_p = imagecreatetruecolor($new_width, $new_height);
      imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

      // Output
      imagejpeg($image_p, $_FILES["backgroundImage"]["tmp_name"], 100);

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
        if (!move_uploaded_file($_FILES["backgroundImage"]["tmp_name"], $target_file)) {
          echo "Sorry, there was an error uploading your file.";
        } else {
          $sqlTwo->execute();
        }
      }
    }

    header("location: ../");
  }
}

$config = new Config();
$link = $config->connectToServer();

$changeBackgroundImage = new ChangeBackgroundImage();
$changeBackgroundImage->setLink($link);
$changeBackgroundImage->setId(intval($_POST['id']));
$changeBackgroundImage->setImageDescription($_POST['imageDescription']);

$changeBackgroundImage->ChangeBackgroundImage();
?>
