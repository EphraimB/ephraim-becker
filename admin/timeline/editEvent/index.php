<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

  global $link;
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Ephraim Becker - Admin - Timeline - Edit Event</title>
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
      <h1 style="font-weight: bold;">Admin - Timeline - Edit Event</h1>
    </header>
    <main>
      <form action="editEvent.php" method="post" enctype="multipart/form-data">
        <div class="row">
          <?php
          $sql = "SELECT * FROM timeline WHERE TimelineId=" . $_GET['id'];
          $sqlResult = mysqli_query($link, $sql);

          while($row = mysqli_fetch_array($sqlResult)){
            $hide = $row['hide'];
            $timelineId = $row['TimelineId'];
            $eventDate = $row['EventDate'];
            $eventTime = $row['EventTime'];

            $endEventDate = $row['EndEventDate'];

            $eventTitle = $row['EventTitle'];
            $eventDescription = $row['EventDescription'];
            $memoryType = $row['MemoryType'];

            $eventYouTubeLink = $row['EventYouTubeLink'];

            $eventMedia = $row['EventMedia'];
            $eventMediaDescription = $row['EventMediaDescription'];
          }
          ?>
          <div>
            <label for="eventDate">Start event date:</label>
            <br />
            <input type="date" id="eventDate" name="eventDate" value="<?php echo $eventDate ?>" required />
          </div>
          <div>
            <label for="eventTime">Event time (optional):</label>
            <br />
            <input type="time" id="eventTime" name="eventTime" value="<?php echo $eventTime ?>" />
          </div>
          <div>
            <input type="checkbox" id="allDay" name="allDay" value="allDay" <?php if(is_null($eventTime)) { echo "checked"; } ?>>
            <label for="allDay">allDay?</label>
          </div>
        </div>
        <br />
        <div class="row">
          <div>
            <label for="endEventDate">End event date:</label>
            <br />
            <input type="date" id="endEventDate" name="endEventDate" value="<?php echo $endEventDate ?>" />
          </div>
          <div>
            <input type="checkbox" id="endEventDateExist" name="endEventDateExist" value="endEventDateExist" <?php if(!is_null($endEventDate)) { echo "checked"; } ?>>
            <label for="endEventDateExist">End event date exist?</label>
          </div>
        </div>
        <br />
        <div>
          <label for="eventTitle">Event title:</label />
          <br />
          <input type="text" id="eventTitle" name="eventTitle" value="<?php echo $eventTitle ?>" required />
        </div>
        <br />
        <div>
          <label for="eventDescription">Event description:</label>
          <br />
          <textarea id="eventDescription" name="eventDescription" rows="6" cols="50" required><?php echo $eventDescription ?></textarea>
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
          <input id="eventYouTubeLink" name="eventYouTubeLink" type="text" value="<?php echo $eventYouTubeLink ?>" />
        </div>
        <div>
          <h3>Event memory type:</h3>
          <div class="row">
            <div class="remembered-memory">
              <input type="radio" id="remembered" name="memory" value="0" <?php if($memoryType == 0){ echo "checked"; } ?> required />
              <label for="remembered">Remembered memory</label>
            </div>
            <div class="diary-memory">
              <input type="radio" id="diary" name="memory" value="1" <?php if($memoryType == 1){ echo "checked"; } ?> />
              <label for="diary">Diary memory</label>
            </div>
            <div class="hidden-memory remembered-memory">
              <input type="checkbox" id="hidden" name="hidden" value="1" <?php if($hide == 1) { echo "checked"; } ?> />
              <label for="hidden">Hidden memory</label>
            </div>
          </div>
        </div>
        <input type="hidden" name="id" value="<?php echo $timelineId ?>">
        <br />
        <input type="submit" value="Submit to timeline" />
      </form>
    </main>
    <script src="../../../js/script.js"></script>
    <script src="js/script.js"></script>
  </body>
</html>
