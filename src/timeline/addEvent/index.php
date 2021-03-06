<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class AddEventForm extends Base
{
  private $isAdmin;

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

  function main(): string
  {
    $body = '<form action="createEvent.php" method="post" enctype="multipart/form-data">
          <div class="row">
            <div>
              <label for="eventDate">Start event date:</label>
              <br />
              <input type="date" id="eventDate" name="eventDate" required />
            </div>
            <div>
              <label for="eventTime">Event time (optional):</label>
              <br />
              <input type="time" id="eventTime" name="eventTime" disabled />
              <input type="hidden" id="timezone" name="timezone" />
              <input type="hidden" id="timezoneOffset" name="timezoneOffset" />
            </div>
            <div>
              <input type="checkbox" id="allDay" name="allDay" value="allDay" checked>
              <label for="allDay">allDay?</label>
            </div>
          </div>
          <br />
          <div class="row">
            <div>
              <label for="endEventDate">End event date:</label>
              <br />
              <input type="date" id="endEventDate" name="endEventDate" disabled />
            </div>
            <div>
              <input type="checkbox" id="endEventDateExist" name="endEventDateExist" value="endEventDateExist">
              <label for="endEventDateExist">End event date exist?</label>
            </div>
          </div>
          <br />
          <div>
            <label for="eventTitle">Event title:</label />
            <br />
            <input type="text" id="eventTitle" name="eventTitle" required />
          </div>
          <br />
          <div>
            <label for="eventDescription">Event description:</label>
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

      return $body;
  }
}

$addEventForm = new AddEventForm();
$addEventForm->setLocalStyleSheet("css/style.css");
$addEventForm->setLocalScript("js/script.js");
$addEventForm->setTitle("Ephraim Becker - Timeline - Add Event");
$addEventForm->setHeader("Timeline - Add Event");
$addEventForm->setUrl($_SERVER['REQUEST_URI']);
$addEventForm->setBody($addEventForm->main());

$addEventForm->html();
?>
