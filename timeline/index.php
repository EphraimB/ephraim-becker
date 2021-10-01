<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;

  $title = "Ephraim Becker - Timeline";
  $header = "Ephraim Becker - Timeline";

  $body = '<div id="grid-container">' .
    $sql = "SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime', DATE_FORMAT(EventDate - INTERVAL EventTimeZoneOffset SECOND, '%Y') AS Year FROM timeline WHERE hide = 0 GROUP BY Year ORDER BY EventDate ASC";
    $sqlResult = mysqli_query($link, $sql);

    while($row = mysqli_fetch_array($sqlResult)){
      $year = $row['Year'];

    $body += '<div class="card album-cover" id="album-cover-<?php echo $year ?>" onclick="filterTimeline('.$year.')">
      <h2><?php echo $year ?></h2>
      <p>All the events in <?php echo $year ?> when I was <?php echo $year-1996 ?> years old</p>
    </div>' .

  };
  $body += '</div>';
?>
