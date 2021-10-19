<?php
  $title = "Ephraim Becker - Projects";
  $header = "Projects";
  $localStyleSheet = '<link rel="stylesheet" href="css/style.css">';
  $localScript = NULL;

  $body = '<div class="grid-container">
          <div class="card">
            <a target="_blank" href="virtualFriend/">
              <h2>Virtual Friend</h2>
              <p>A Virtual Friend project I built in 2016 and needs to be updated.</p>
              <p>Click to view</p>
            </a>
          </div>
          <div class="card">
            <a href="files/wizardc.zip">
              <h2>Wizardc7</h2>
              <p>A Harry Potter currency converter I made in Darchei.</p>
              <p>Click to download onto calculator</p>
            </a>
          </div>
          <div class="card">
            <a href="files/Kosherizer-master.zip">
              <h2>Kosherizer chrome extention</h2>
              <p>A Google Chrome extention that gets rid of all the curse words.</p>
              <p>Click to download</p>
            </a>
          </div>
          <div class="card">
            <h2>Star Trek beaming video</h2>
            <p>I video composited a beaming video from my bedroom to my pschcology office with a green screen.</p>
            <iframe width="280" height="auto" src="https://www.youtube.com/embed/6ktNUS7dt0M" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          </div>
        </div>';

  $url = $_SERVER['REQUEST_URI'];
  require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
?>
