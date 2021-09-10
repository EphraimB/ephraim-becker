<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Ephraim Becker - Admin - Timeline - Add Event</title>
    <link rel="stylesheet" href="../../../css/style.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="icon" href="../../../img/ephraim_becker.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="../../../img/ephraim-becker.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  </head>
  <body>
    <nav>
      <ul>
        <li id="first"><img src="../../../img/ephraim-becker.jpg" alt="Photo of Ephraim Becker" width="70px" height="70px" /></li>
        <li id="hamburger-icon"><a href="#" onclick="toggleNavMenu()">&#9776;</a></li>
        <div id="links">
          <li><a href="../../">Admin</a></li>
          <li class="focus"><a href="../">Timeline</a></li>
        </div>
      </ul>
    </nav>
    <header>
      <h1 style="font-weight: bold;">Admin - Timeline - Add Event</h1>
    </header>
    <main>
      <form action="createEvent.php" method="post" enctype="multipart/form-data">
        <div class="row">
          <div>
            <label for="eventDate">Start event date:</label>
            <br />
            <input type="date" id="eventDate" name="eventDate" required />
          </div>
          <div>
            <label for="eventTime">Event time (optional):</label>
            <br />
            <input type="time" id="eventTime" name="eventTime" />
          </div>
          <div>
            <input type="checkbox" id="allDay" name="allDay" value="allDay">
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
        <input type="submit" value="Submit to timeline" />
      </form>
    </main>
    <script src="../../../js/script.js"></script>
    <script src="js/script.js"></script>
  </body>
</html>
