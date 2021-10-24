<?php
declare(strict_types=1);

session_start();

global $link;

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class Timeline extends Base
{
  private $isAdmin;

  function __construct()
  {
    $this->setIsAdmin();
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

  function addEvent(): string
  {
    if($this->getIsAdmin() == true) {
      $html .= '<div class="row">
            <ul class="subNav">
              <li><a style="text-decoration: none;" href="addEvent/">+</a></li>
            </ul>
          </div>';
      }
  }

  $body .= '<div id="grid-container">';
  if(isset($_SESSION['username'])) {
    $sql = "SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime', DATE_FORMAT(EventDate - INTERVAL EventTimeZoneOffset SECOND, '%Y') AS Year FROM timeline GROUP BY Year ORDER BY EventDate ASC";
  } else {
    $sql = "SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime', DATE_FORMAT(EventDate - INTERVAL EventTimeZoneOffset SECOND, '%Y') AS Year FROM timeline WHERE hide = 0 GROUP BY Year ORDER BY EventDate ASC";
  }
    $sqlResult = mysqli_query($link, $sql);

    while($row = mysqli_fetch_array($sqlResult)) {
      $year = $row['Year'];

    $body .= '<div class="card album-cover" id="album-cover-' . $year . '" onclick="filterTimeline(\'' . $year . '\')">';
      $body .= '<h3>' . $year . '</h3>';
      $body .= '<p>All the events in ' . $year . ' when I was ' . ($year-1996) . ' years old</p>';
    $body .= '</div>';
  };

  $body .= '</div>';


  $timeline = new Timeline();
  $index->setTitle("Ephraim Becker - Timeline");
  $index->setLocalStyleSheet('css/style.css');
  $index->setLocalScript('js/ajax.js');
  $index->setHeader('Ephraim Becker - Timeline');
  $index->setUrl($_SERVER['REQUEST_URI']);
  $index->setBody($index->main());

  $index->html();
?>
