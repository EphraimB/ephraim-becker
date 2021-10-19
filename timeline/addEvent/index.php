<?php
  session_start();

  if(!isset($_SESSION['username'])) {
    header("location: ../");
  }

  $title = "Ephraim Becker - Admin - Timeline - Add Event";
  $header = "Admin - Timeline - Add Event";
  $localStyleSheet = '<link rel="stylesheet" href="css/style.css" />';
  $localScript = '<script src="js/script.js"></script>';

  if(isset($_SESSION['username'])) {
    $admin = '<li><a href="/adminLogout.php">Logout</a></li>';
  } else {
    $admin = '<li><a href="/adminLogin/">Login</a></li>';
  }

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

  require("../../base.php");
?>
