<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class AddCollegeInfoForm extends Base
{
  private $isAdmin;

  function __construct()
  {
    $this->setIsAdmin();

    if($this->getIsAdmin()) {
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
}

function main(): string
{
  $html = '<form action="addCollegeInformation.php" method="post" enctype="multipart/form-data">
          <label for="eventDescription">College semester:</label>
          <br />
          <textarea id="eventDescription" name="eventDescription" rows="6" cols="50" required></textarea>
        </div>
        <br />
        <div>
          <label for="eventImage">Image:</label>
          <br />
          <input id="eventImage" name="eventImage" type="file" />
        </div>
        <br />
        <div>
          <label for="eventImageDescription">Image description:</label>
          <br />
          <input id="eventImageDescription" name="eventImageDescription" type="text" />
        </div>
        <br />
        <div>
          <label for="eventImage">YouTube link:</label>
          <br />
          <input id="eventYouTubeLink" name="eventYouTubeLink" type="text" />
        </div>
        <div>
          <h3>Event memory type:</h3>
          <div class="row">
            <div class="remembered-memory">
              <input type="radio" id="remembered" name="memory" value="0" required />
              <label for="remembered">Remembered memory</label>
            </div>
            <div class="diary-memory">
              <input type="radio" id="diary" name="memory" value="1" />
              <label for="diary">Diary memory</label>
            </div>
            <div class="hidden-memory remembered-memory">
              <input type="checkbox" id="hidden" name="hidden" value="1" />
              <label for="hidden">Hidden memory</label>
            </div>
          </div>
        </div>
        <br />
        <input type="submit" id="submit" value="Submit to timeline" disabled="disabled" />
        <br />
      </form>';
}
