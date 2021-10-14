<?php
  session_start();

  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;

  $title = "Ephraim Becker - Timeline";
  $localStyleSheet = '<link rel="stylesheet" href="css/style.css" />';
  $header = "Ephraim Becker - Timeline";

  if(isset($_SESSION['username'])) {
    $admin = '<li><a href="/adminLogout.php">Logout</a></li>';
  } else {
    $admin = '<li><a href="/adminLogin/">Login</a></li>';
  }

  $body = '<div id="grid-container">';
    $sql = "SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime', DATE_FORMAT(EventDate - INTERVAL EventTimeZoneOffset SECOND, '%Y') AS Year FROM timeline WHERE hide = 0 GROUP BY Year ORDER BY EventDate ASC";
    $sqlResult = mysqli_query($link, $sql);

    while($row = mysqli_fetch_array($sqlResult)) {
      $year = $row['Year'];

    $body .= '<div class="card album-cover" id="album-cover-' . $year . '" onclick="filterTimeline(\'' . $year . '\')">';
      $body .= '<h2>' . $year . '</h2>';
      $body .= '<p>All the events in ' . $year . ' when I was ' . ($year-1996) . ' years old</p>';
    $body .= '</div>';
  };

  $body .= '</div>';

  $localScript = '<script src="js/ajax.js"></script>';

  require("../base.php");
?>
