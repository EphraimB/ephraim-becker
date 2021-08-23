<?php
  //date in mm/dd/yyyy format; or it can be in other formats as well
  $birthDate = "07/19/1996";
  //explode the date to get month, day and year
  $birthDate = explode("/", $birthDate);
  //get age from date or birthdate
  $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md")
    ? ((date("Y") - $birthDate[2]) - 1)
    : (date("Y") - $birthDate[2]));

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Ephraim Becker - All about my autistic life</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="canonical" href="https://www.ephraimbecker.com/" />
    <link rel="icon" href="img/ephraim_becker.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="img/ephraim-becker.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Hi! My name is Ephraim Becker and this is a website about my life and how people can learn from it." />
    <meta name="keywords" content="Ephraim Becker, autism, aspergers, ADHD" />
  </head>
  <body>
    <nav>
      <ul>
        <li id="first"><img src="img/ephraim-becker.jpg" alt="Photo of Ephraim Becker" width="70px" height="70px" /></li>
        <li id="hamburger-icon"><a href="#" onclick="toggleNavMenu()">&#9776;</a></li>
        <div id="links">
          <li class="focus"><a href="#">Home</a></li>
          <li><a href="timeline/">Timeline</a></li>
          <div id="dropdown">
            <li><a href="#" onclick="toggleNavSubmenu()">Daily Life &emsp; &#x25BC;</a></li>
            <div id="dropdown-content">
              <li><a href="everydayLife/">Everyday Life</a></li>
              <li><a href="college/">College Life</a></li>
            </div>
          </div>
          <li><a href="projects/">Projects</a></li>
          <li><a href="resources/">Resources</a></li>
          <li><a href="about/">About</a></li>
        </div>
      </ul>
    </nav>
    <main>
      <div id="profileCard" itemscope itemtype="https://schema.org/Person" />
        <img src="img/ephraim-becker.jpg" itemprop="image" alt="Photo of Ephraim Becker" width="100px" height="100px" />
        <h1 style="font-weight: bold;" itemprop="name">Ephraim Becker</h1>
        <p>
          <span style="font-weight: bold;">Age: </span>
          <span><?php echo $age; ?></span>
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
        <div>
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
      </div>
    </main>
    <script src="js/script.js"></script>
  </body>
</html>
