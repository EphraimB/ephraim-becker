<?php
  $title = "Ephraim Becker - Everyday Life";
  $header = "Everyday Life";
  $localStyleSheet = '<link rel="stylesheet" href="css/style.css" />';
  $localScript = NULL;

  $body = '<p>It\'s pretty much the same thing everyday. I have a hard time making real friends while my siblings are having an easy and fun time with their friends. There are some accomplishments though like building my own computer (but that\'s not a social accomplishment).</p>
      <div class="grid-container">
          <div style="background-color: red;" class="card">
            <a href="problems/">
              <h2>Problems I\'m having</h2>
              <p>This link will show all my problems that I\'m having.</p>
              <p>Click to view</p>
            </a>
          </div>
          <div style="background-color: green;" class="card">
            <a href="accomplishments/">
              <h2>Accomplishments</h2>
              <p>This link will show all my accomplishments that I\'m having.</p>
              <p>Click to view</p>
            </a>
          </div>
          <div style="background-color: yellow;" class="card">
            <a style="color: black;" href="goals/">
              <h2>Goals</h2>
              <p>This link will show all my goals.</p>
              <p>Click to view</p>
            </a>
          </div>
        </div>';

  require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
?>
