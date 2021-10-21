<?php
  session_start();

  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;
  //date in mm/dd/yyyy format; or it can be in other formats as well
  $birthDate = "07/19/1996";
  //explode the date to get month, day and year
  $birthDate = explode("/", $birthDate);
  //get age from date or birthdate
  $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md")
    ? ((date("Y") - $birthDate[2]) - 1)
    : (date("Y") - $birthDate[2]));


    $title = "Ephraim Becker - All about my autistic life";
    $localStyleSheet = NULL;
    $header = NULL;

    $body = '
    <main>
      <div id="profileCard" itemscope itemtype="https://schema.org/Person">
        <img src="img/ephraim-becker.jpg" itemprop="image" alt="Photo of Ephraim Becker" width="100px" height="100px" />
        <h1 style="font-weight: bold;" itemprop="name">Ephraim Becker</h1>
        <p>
          <span style="font-weight: bold;">Age: </span>
          <span>' . $age . '</span>
        </p>
        <p>
          <span style="font-weight: bold;">Diagnosis: </span>
          <span>Autism, ADHD</span>
        </p>
        <p>
          <span style="font-weight: bold;">Birthdate: </span>
          <span itemprop="birthDate"><time datetime="1996-07-19T12:21">July 19, 1996 @ 12:21PM</time></span>
        </p>
        <p>
          <span style="font-weight: bold;">Religion: </span>
          <span>Modern Orthodox Jewish (My parents are ultra-orthodox Jewish)</span>
        </p>
        <p>
          <span style="font-weight: bold;">Interests: </span>
          <span>Technology, Astronomy, Sci-fi/fantasy movies, Trains</span>
        </p>
        <p>
          <span style="font-weight: bold;">Location: </span>
          <span>Far Rockaway, NY, United States</span>
        </p>
        <div class="socialLinks">
          <a target="_blank" rel="noopener" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.facebook.com/ephraim.becker/"><img src="img/f_logo_RGB-Blue_1024.png" alt="Facebook logo" width="50px" height="auto" /></a>
          <a target="_blank" rel="noopener" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.messenger.com/t/100009677345527/"><img src="img/logo-16@3x.png" alt="Messenger logo" width="50px" height="auto" /></a>
          <a target="_blank" rel="noopener" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.instagram.com/ephraim.becker/"><img src="img/Instagram_Glyph_Gradient_RGB.png" alt="Instagram logo" width="50px" height="auto" /></a>
          <a target="_blank" rel="noopener" style="display: inline; background-color: rgba(0,0,0,0);" href="https://twitter.com/emb180"><img src="img/twitterLogo.png" alt="Twitter logo" width="50px" height="auto"></a>
          <a target="_blank" rel="noopener" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.youtube.com/channel/UCIHxAXYLxYlNaQiv0do0bUg"><img src="img/yt_icon_rgb.png" alt="YouTube logo" width="50px" height="auto" /></a>
          <a target="_blank" rel="noopener" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.linkedin.com/in/ephraim-becker-3263b810b/"><img src="img/LI-In-Bug.png" alt="Linkedin logo" width="50px" height="auto" /></a>
          <a target="_blank" rel="noopener" style="display: inline; background-color: rgba(0,0,0,0);" href="https://github.com/EphraimB"><img src="img/GitHub-Mark-64px.png" alt="GitHub logo" width="50px" height="auto" /></a>
          <a target="_blank" rel="noopener" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.autismforums.com/members/ephraim-becker.16496/"><img style="border: 1px solid black;" src="img/aflogo.png" alt="Autism Forums logo" width="auto" height="50px" /></a>
          <a target="_blank" rel="noopener" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.aspergerexperts.com/profile/60330-ephraim-becker/"><img src="img/aesquare.png" alt="Aspergers Experts logo" width="auto" height="50px" /></a>
        </div>
      </div>';

      $localScript = NULL;

      $url = $_SERVER['REQUEST_URI'];
      require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
 ?>
