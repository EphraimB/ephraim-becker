<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class Goals extends Base
{

  function __construct()
  {

  }

  function main(): string
  {
      $body = '<ol style="text-align: left;">
          <li>Get out of defense mode (hopefully with some course in <a target="_blank" rel="noopener" href="https://www.aspergerexperts.com/">Aspergers Experts</a>)</li>
          <li>Become better at socializing</li>
          <li>Expressing myself more</li>
          <li>Mindfulness</li>
          <li>Get out of the house and be independent</li>
          <li>Being more professional in the workplace</li>
        </ol>

        <h2>Goal log</h2>
        <table>
          <tr>
            <th>Date</th>
            <th>Goal worked on</th>
            <th>Log</th>
          </tr>
          <tr>
            <td></td>
            <td></td>
            <td></td>
          </tr>
        </table>';

      return $body;
    }
}

$goals = new Goals();
$goals->setUrl($_SERVER['REQUEST_URI']);
$goals->setTitle('Ephraim Becker - Everyday Life - Goals');
$goals->setLocalStyleSheet(NULL);
$goals->setLocalScript(NULL);
$goals->setHeader('Goals');
$goals->setBody($goals->main());

$goals->html();
?>
