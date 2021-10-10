<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;

  $title = "Ephraim Becker - Admin - Timeline";
  $header = "Admin - Timeline";
  $localStyleSheet = '<link rel="stylesheet" href="css/style.css" />';
  $localScript = '<script src="js/ajax.js"></script>';

  $body = '<div class="row">
        <ul class="subNav">
          <li><a style="text-decoration: none;" href="addEvent/">+</a></li>
        </ul>
      </div>
      <div id="grid-container">';
  $sql = "SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime', DATE_FORMAT(EventDate - INTERVAL EventTimeZoneOffset SECOND, '%Y') AS Year FROM timeline GROUP BY Year ORDER BY EventDate ASC";
  $sqlResult = mysqli_query($link, $sql);

  while($row = mysqli_fetch_array($sqlResult)){
    $year = $row['Year'];

    $body .= '<div class="card album-cover" id="album-cover-<?php echo $year ?>" onclick="filterTimeline(' . $year . ')">
          <h2>' . $year . '</h2>
          <p>All the events in ' . $year . ' when I was ' . $year-1996 . ' years old</p>
        </div>';

      }

   $body .= '</div>';

   require("../base.php");
?>
