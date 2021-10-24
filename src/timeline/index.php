<?php
declare(strict_types=1);

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');
require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class Timeline extends Base
{
  private $link;
  private $isAdmin;

  function __construct()
  {
    $this->setIsAdmin();
  }

  function setLink($link)
  {
    $this->link = $link;
  }

  function getLink()
  {
    return $this->link;
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
      $html = '<div class="row">
            <ul class="subNav">
              <li><a style="text-decoration: none;" href="addEvent/">+</a></li>
            </ul>
          </div>';
      } else {
        $html = '';
      }

      return $html;
  }

  function fetchEvents($link): mysqli_result
  {
    if($this->getIsAdmin()) {
      $sql = "SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime', DATE_FORMAT(EventDate - INTERVAL EventTimeZoneOffset SECOND, '%Y') AS Year FROM timeline GROUP BY Year ORDER BY EventDate ASC";
    } else {
      $sql = "SELECT *, IFNULL(DATE_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%Y-%m-%d'), EventDate) AS 'LocalDate', IFNULL(TIME_FORMAT(concat(EventDate, ' ', EventTime) - INTERVAL EventTimeZoneOffset SECOND, '%H:%i:%s'), NULL) AS 'LocalTime', DATE_FORMAT(EventDate - INTERVAL EventTimeZoneOffset SECOND, '%Y') AS Year FROM timeline WHERE hide = 0 GROUP BY Year ORDER BY EventDate ASC";
    }
    $sqlResult = mysqli_query($this->getLink(), $sql);

    return $sqlResult;
  }

  function yearView($sqlResult, $year): string
  {
    $html = '<div class="card album-cover" id="album-cover-' . $year . '" onclick="filterTimeline(\'' . $year . '\')">';
    $html .= '<h3>' . $year . '</h3>';
    $html .= '<p>All the events in ' . $year . ' when I was ' . ($year-1996) . ' years old</p>';
    $html .= '</div>';

    return $html;
  }

  function main(): string
  {
    $html = $this->addEvent();
    $html .= '<div id="grid-container">';
    $sqlResult = $this->fetchEvents($this->link);

    while($row = mysqli_fetch_array($sqlResult)) {
      $year = $row['Year'];

      $html .= $this->yearView($sqlResult, $year);
    }

    $html .= '</div>';

    return $html;
  }
}

$config = new Config();
$link = $config->connectToServer();

$timeline = new Timeline();
$timeline->setLink($link);
$timeline->setTitle("Ephraim Becker - Timeline");
$timeline->setLocalStyleSheet('css/style.css');
$timeline->setLocalScript('js/ajax.js');
$timeline->setHeader('Ephraim Becker - Timeline');
$timeline->setUrl($_SERVER['REQUEST_URI']);
$timeline->setBody($timeline->main());

$timeline->html();
?>
