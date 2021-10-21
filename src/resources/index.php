<?php
  $title = "Ephraim Becker - Resources";
  $header = "Resources";
  $localStyleSheet = NULL;
  $localScript = NULL;

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

  $url = $_SERVER['REQUEST_URI'];
  require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
?>
