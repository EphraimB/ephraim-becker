<?php

declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class Resources extends Base
{
  function __construct()
  {

  }

  function main(): string
  {
    $body = '
        <table>
          <tr>
            <th>Resource</th>
            <th>What it helps with</th>
          </tr>
          <tr>
            <td><a target="_blank" rel="noopener" href="https://www.aspergerexperts.com/">Aspergers Experts</a></td>
            <td>Understanding the autistic individual and the autistic individual understanding themselves and also has courses for help</td>
          </tr>
          <tr>
            <td><a target="_blank" rel="noopener" href="https://www.autismforums.com/">Autism Forums</a></td>
            <td>A forum for autistic individuals to express themselves. Good for understanding things from an autistic individual\'s perspective.</td>
          </tr>
        </table>';

      return $body;
    }
}

$resources = new Resources();
$resources->setUrl($_SERVER['REQUEST_URI']);
$resources->setTitle('Ephraim Becker - Resources');
$resources->setLocalStyleSheet(NULL);
$resources->setLocalScript(NULL);
$resources->setHeader('Resources');
$resources->setBody($resources->main());

$resources->html();
?>
