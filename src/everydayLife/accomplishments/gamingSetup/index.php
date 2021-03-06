<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

class GamingSetup extends Base
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

  function pictures(): string
  {
    $body = '
    <div>
      <img src="img/gamingSetup.jpeg" alt="gaming setup" width="256px" height="auto" />
      <img src="img/computer.jpeg" alt="computer" width="143px" height="auto" />
    </div>';

    return $body;
  }

  function addToGamingSetup(): string
  {
    if($this->getIsAdmin()) {
      $html = '<div class="row">
            <ul class="subNav">
              <li><a style="text-decoration: none;" href="addToGamingSetup/">+</a></li>
            </ul>
          </div>';
      } else {
        $html = '';
      }

    return $html;
  }

  function gamingSetup(): string
  {
    $sql = "SELECT * FROM GamingSetup";
    $sqlResult = mysqli_query($this->getLink(), $sql);

    $sqlTwo = "SELECT SUM(Price) AS TotalPrice FROM GamingSetup";
    $sqlTwoResult = mysqli_query($this->getLink(), $sqlTwo);

    while($rowTwo = mysqli_fetch_array($sqlTwoResult)) {
      $totalPrice = $rowTwo['TotalPrice'];
    }

    $body = '
    <table>
      <tr>
        <th>Component</th>
        <th>Model</th>
        <th>Price</th>';

        if($this->getIsAdmin()) {
          $body .= '<th>Actions</th>';
        }

      $body .= '</tr>';

      while($row = mysqli_fetch_array($sqlResult)) {
        $id = $row['GamingSetupId'];
        $component = $row['Component'];
        $model = $row['Model'];
        $price = $row['Price'];

        $body .= '
        <tr>
          <td>' . $component . '</td>
          <td>' . $model . '</td>
          <td>$' . $price . '</td>
        ';

      if($this->getIsAdmin()) {
        $body .= '
          <td class="actionButtons">
            <a class="edit" href="editComponent/index.php?id=' . $id . '">Edit</a>
            <a class="delete" href="confirmationComponent.php?id=' . $id . '">Delete</a>
          </td>';
      }

      $body .= '</tr>';
    }

    $body .= '<tr>
       <td colspan="3"><span style="font-weight: bold;">Total:</span> $' . $totalPrice . '</td>
     </tr>';

    $body .= '</table>';

    return $body;
  }

  function addToGames(): string
  {
    if($this->getIsAdmin()) {
      $html = '<div class="row">
            <ul class="subNav">
              <li><a style="text-decoration: none;" href="addToGames/">+</a></li>
            </ul>
          </div>';
      } else {
        $html = '';
      }

    return $html;
  }

  function games(): string
  {
    $sql = "SELECT * FROM ComputerGames";
    $sqlResult = mysqli_query($this->getLink(), $sql);

    $sqlTwo = "SELECT SUM(Price) AS TotalPrice FROM ComputerGames";
    $sqlTwoResult = mysqli_query($this->getLink(), $sqlTwo);

    while($rowTwo = mysqli_fetch_array($sqlTwoResult)) {
      $totalPrice = $rowTwo['TotalPrice'];
    }

    $body = '
    <table style="margin-top: 10px;">
      <tr>
        <th>Game</th>
        <th>Platform</th>
        <th>Price</th>';

      if($this->getIsAdmin()) {
        $body .= '<th>Actions</th>';
      }

    $body .= '</tr>';

    while($row = mysqli_fetch_array($sqlResult)) {
      $id = $row['ComputerGamesId'];
      $game = $row['Game'];
      $platform = $row['Platform'];
      $price = $row['Price'];

      $body .= '
      <tr>
        <td>' . $game . '</td>
        <td>' . $platform . '</td>
        <td>$' . $price . '</td>
      ';

    if($this->getIsAdmin()) {
      $body .= '
        <td class="actionButtons">
          <a class="edit" href="editGame/index.php?id=' . $id . '">Edit</a>
          <a class="delete" href="confirmationGame.php?id=' . $id . '">Delete</a>
        </td>';
      }

      $body .= '</tr>';
    }

    $body .= '<tr>
       <td colspan="3"><span style="font-weight: bold;">Total:</span> $' . $totalPrice . '</td>
     </tr>';

    $body .= '</table>';

    return $body;
  }

  function main(): string
  {
    $body = $this->pictures();
    $body .= $this->addToGamingSetup();
    $body .= $this->gamingSetup();
    $body .= '<br />';
    $body .= $this->addToGames();
    $body .= $this->games();

    return $body;
  }
}
$config = new Config();
$link = $config->connectToServer();

$gamingSetup = new GamingSetup();
$gamingSetup->setLink($link);
$gamingSetup->setIsAdmin();

$gamingSetup->setUrl($_SERVER['REQUEST_URI']);
$gamingSetup->setTitle('Ephraim Becker - Everyday Life - Accomplishments - Gaming setup');
$gamingSetup->setLocalStyleSheet('css/style.css');
$gamingSetup->setLocalScript(NULL);
$gamingSetup->setHeader('Gaming setup');
$gamingSetup->setBody($gamingSetup->main());

$gamingSetup->html();
?>
