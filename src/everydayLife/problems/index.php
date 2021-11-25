<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class Problems extends Base
{
  private $isAdmin;
  private $link;

  function __construct()
  {

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

  function setLink($link)
  {
    $this->link = $link;
  }

  function getLink()
  {
    return $this->link;
  }

  function addDemographicCompareRow(): string
  {
    if($this->getIsAdmin()) {
      $html = '<div class="row">
            <ul class="subNav">
              <li><a style="text-decoration: none;" href="addDemographicRow/">+</a></li>
            </ul>
          </div>';
      } else {
        $html = '';
      }

      return $html;
  }

  function addComfortZone(): string
  {
    if($this->getIsAdmin()) {
      $html = '<div class="row">
            <ul class="subNav">
              <li><a style="text-decoration: none;" href="addComfortZone/">+</a></li>
            </ul>
          </div>';
      } else {
        $html = '';
      }

    return $html;
  }

  function problemsSummary(): string
  {
    $body = '<p>My biggest problem I have is a hard time making and keeping friends. I want friends only on the autism spectrum so that they would be real friends and can relate to each other. There\'s a difference between the fake friends I had in Darchei and friends on the autism spectrum. It\'s so frustrating trying to make friends alone and even if I make friends, keeping them is another hard thing in itself. I ruined a lot of chances on making friends in Camp Kaylie due to looking at other people\'s medications and acting very immature. What I did immature was upgrading other people\'s phones without permission and doing pranks on people. I no longer do that anymore.</p>
        <p>There were instances that I made friends with a few people in distant places and got along very well and then I texted them stuff and there was no response. This went on for around some time and then I was blocked. This keeps on happening. It looks like I have horrible texting etiquette and don\'t know the appropriate frequency for calling other people. I guess the best way to make and keep friends is in-person. That can really only happen in a synagogue. I\'ve been going around from synagogue to synagogue looking for friends on the autism spectrum and had little success. The problem is that people in Far Rockaway don\'t admit that they have autism. It frustrates me very much! Especially when all my other siblings has friends their own type that are very close and go on trips a lot.</p>
        <p>I\'m also having a lot of nightmares lately about parents of people on the spectrum not wanting me to be friends with their son because they would be embarrased about their child having autism while his siblings would be friends with my siblings hearing all my siblings and their new friends happily talking loudly in the background while I\'m just pacing around depressed. This is impacting my everyday life including work and college.</p>';

    return $body;
  }

  function demographicComparison(): string
  {
    $sql = "SELECT * FROM demographics";
    $sqlResult = mysqli_query($this->getLink(), $sql);

    $body = '
    <table>
      <tr>
        <th>Yeshivish friends</th>
        <th>Neurotypical friends</th>
        <th>Autistic friends</th>
        <th>Older friends</th>';

        if($this->getIsAdmin()) {
          $body .= '<th>Actions</th>';
        }

      $body .= '</tr>';

    while($row = mysqli_fetch_array($sqlResult)) {
      $id = $row['demographicId'];
      $yeshivish = $row['yeshivish'];
      $neurotypical = $row['neurotypical'];
      $autism = $row['autism'];
      $older = $row['older'];

      $body .= '
      <tr>
        <td>' . $yeshivish . '</td>
        <td>' . $neurotypical . '</td>
        <td>' . $autism . '</td>
        <td>' . $older . '</td>';

        if($this->getIsAdmin()) {
          $body .= '
          <td class="actionButtons">
            <a class="edit" href="editDemographicRow/index.php?id=' . $id . '">Edit</a>
            <a class="delete" href="confirmationDemographicRow.php?id=' . $id . '">Delete</a>
          </td>';
        }

      $body .= '</tr>';
    }

    $body .= '
      <tr>
        <td colspan="4">And more...</td>
      </tr>
    </table>';

    return $body;
  }

  function comfortZone(): string
  {
    $sql = "SELECT * FROM ComfortZone";
    $sqlResult = mysqli_query($this->getLink(), $sql);

    $body = '
        <p style="margin-top: 10px;">I also have a very small comfort zone</p>
        <table>
          <tr>
            <th>Comfort zone</th>
            <th>Reason</th>';

            if($this->getIsAdmin()) {
              $body .= '<th>Actions</th>';
            }

          $body .= '</tr>';

    while($row = mysqli_fetch_array($sqlResult)) {
      $id = $row['ComfortZoneId'];
      $comfortZone = $row['ComfortZone'];
      $reason = $row['Reason'];

      $body .= '
      <tr>
        <td>' . $comfortZone . '</td>
        <td>' . $reason . '</td>
      ';

      if($this->getIsAdmin()) {
        $body .= '
          <td class="actionButtons">
            <a class="edit" href="editComfortZone/index.php?id=' . $id . '">Edit</a>
            <a class="delete" href="confirmationComfortZone.php?id=' . $id . '">Delete</a>
          </td>';
      }

      $body .= '</tr>';
    }

    $body .= '
    </table>

    <p style="margin-top: 10px;">I also have sensory issues with flashing lights and noises which can be a problem when going on family trips. It would be nice to make friends with other people on the spectrum and go on special autistic-friendly trips with them.</p>
    <p>That\'s my autism problems. My ADHD problems are being very hyperactive and a hard time focusing (I\'m on medication for it now).</p>';

    return $body;
  }

  function main(): string
  {
    $body = $this->problemsSummary();
    $body .= $this->addDemographicCompareRow();
    $body .= $this->demographicComparison();
    $body .= '<br />';
    $body .= $this->addComfortZone();
    $body .= $this->comfortZone();

    return $body;
  }
}
$config = new Config();
$link = $config->connectToServer();

$problems = new Problems();
$problems->setLink($link);
$problems->setIsAdmin();

$problems->setUrl($_SERVER['REQUEST_URI']);
$problems->setTitle('Ephraim Becker - Everyday Life - Problems');
$problems->setLocalStyleSheet('css/style.css');
$problems->setLocalScript(NULL);
$problems->setHeader('Problems');
$problems->setBody($problems->main());

$problems->html();
?>
