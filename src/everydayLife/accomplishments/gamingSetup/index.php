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

    $body = '
    <table>
      <tr>
        <th>Component</th>
        <th>Original model</th>
        <th>Current model</th>
        <th>Original model price</th>
        <th>Current model price</th>';

        if($this->getIsAdmin()) {
          $body .= '<th>Actions</th>';
        }

      $body .= '</tr>';

      while($row = mysqli_fetch_array($sqlResult)) {
        $id = $row['GamingSetupId'];
        $component = $row['Component'];
        $originalModel = $row['OriginalModel'];
        $currentModel = $row['CurrentModel'];
        $originalModelPrice = $row['OriginalModelPrice'];
        $currentModelPrice = $row['CurrentModelPrice'];

        $body .= '
        <tr>
          <td>' . $component . '</td>
          <td>' . $originalModel . '</td>
          <td>' . $currentModel . '</td>
          <td>$' . $originalModelPrice . '</td>
          <td>$' . $currentModelPrice . '</td>
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

    $body .= '</table>';

    //   </tr>
    //   <tr>
    //     <td>CPU</td>
    //     <td>Intel Core i9-9900K 3.6 GHz 8-Core Processor</td>
    //     <td>$484.99</td>
    //   </tr>
    //   <tr>
    //     <td>CPU Cooler</td>
    //     <td>Noctua NH-U12A 60.09 CFM CPU Cooler</td>
    //     <td>$99.90</td>
    //   </tr>
    //   <tr>
    //     <td>Motherboard</td>
    //     <td>Gigabyte Z390 AORUS ULTRA ATX LGA1151 Motherboard</td>
    //     <td>$244.99</td>
    //   </tr>
    //   <tr>
    //     <td>Memory</td>
    //     <td>Corsair Dominator Platinum 64 GB (4 x 16 GB) DDR4-3200 CL16 Memory</td>
    //     <td>$479.99</td>
    //   </tr>
    //   <tr>
    //     <td>Storage</td>
    //     <td>Samsung 970 EVO Plus 2 TB M.2-2280 NVME Solid State Drive</td>
    //     <td>$497.99</td>
    //   </tr>
    //   <tr>
    //     <td>Video Card</td>
    //     <td>NVIDIA GeForce RTX 2080 Ti 11 GB Founders Edition Video Card</td>
    //     <td>$1199.00</td>
    //   </tr>
    //   <tr>
    //     <td>Case</td>
    //     <td>Corsair 750D ATX Full Tower Case</td>
    //     <td>$158.23</td>
    //   </tr>
    //   <tr>
    //     <td>Power Supply</td>
    //     <td>Corsair RMx (2018) 750 W 80+ Gold Certified Fully Modular ATX Power Supply</td>
    //     <td>$109.99</td>
    //   </tr>
    //   <tr>
    //     <td>Operating System</td>
    //     <td>Microsoft Windows 10 Pro 64-bit</td>
    //     <td>$199.99</td>
    //   </tr>
    //   <tr>
    //     <td>VR headset</td>
    //     <td>Oculus Quest 2 (upgraded from original Quest (gave to my friend))</td>
    //     <td>$399</td>
    //   </tr>
    //   <tr>
    //     <td colspan="3"><span style="font-weight: bold;">Total:</span> $3874.07</td>
    //   </tr>
    // </table>';

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
    $body = '
    <table style="margin-top: 10px;">
      <tr>
        <th>Game</th>
        <th>Price</th>
      </tr>
      <tr>
        <td>The Holy City (Quest via Oculus Link)</td>
        <td>$19.99</td>
      </tr>
      <tr>
        <td>Flight Simulator 2020 (PC, Quest via Oculus Link)</td>
        <td>$59.99</td>
      </tr>
      <tr>
        <td>Universe Sandbox (PC, Quest via Oculus Link)</td>
        <td>$29.99</td>
      </tr>
      <tr>
        <td>Google Earth VR (PC, Quest via Oculus Link)</td>
        <td>Free</td>
      </tr>
      <tr>
        <td>Train Simulator (PC)</td>
        <td>$29.99</td>
      </tr>
      <tr>
        <td>Job Simulator (Quest)</td>
        <td>$19.99</td>
      </tr>
      <tr>
        <td>Star Trek: Bridge Crew (Quest)</td>
        <td>$29.99</td>
      </tr>
      <tr>
        <td>Vader Immortal: Episode I (Quest)</td>
        <td>$10.88</td>
      </tr>
      <tr>
        <td>Vader Immortal: Episode II (Quest)</td>
        <td>$10.88</td>
      </tr>
      <tr>
        <td>Vader Immortal: Episode III (Quest)</td>
        <td>$10.88</td>
      </tr>
      <tr>
        <td>Real VR Fishing (Quest)</td>
        <td>$21.76</td>
      </tr>
      <tr>
        <td colspan="2"><span style="font-weight: bold;">Total:</span> $244.34</td>
      </tr>
    </table>';

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
