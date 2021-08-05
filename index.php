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
    <title>Ephraim Becker</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/ephraim_becker.ico" type="image/x-icon" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  </head>
  <body>
    <nav>
      <ul>
        <li id="first"><img src="img/ephraim-becker.jpg" width="122px" height="auto"></li>
        <li id="hamburger-icon"><a href="#" onclick="toggleNavMenu()">&#9776;</a></li>
        <div id="links">
          <li><a href="index.php">> Home <</a></li>
          <li><a href="timeline.php">Timeline</a></li>
          <li><a href="everydayLife/">Everyday Life</a></li>
          <li><a href="college.php">College Life</a></li>
          <li><a href="projects/">Projects</a></li>
          <li><a href="about.php">About</a></li>
        </div>
      </ul>
    </nav>
    <main>
      <div id="profileCard">
        <img src="img/ephraim-becker.jpg" width="100px" height="auto">
        <h3 style="font-weight: bold;">Ephraim Becker</h3>
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
          <span>July 19, 1996 @ 12:21PM</span>
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
          <a target="_blank" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.facebook.com/ephraim.becker/"><img src="img/f_logo_RGB-Blue_1024.png" width="50px" height="auto"></a>
          <a target="_blank" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.messenger.com/t/100009677345527/"><img src="img/logo-16@3x.png"  width="50px" height="auto"></a>
          <a target="_blank" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.instagram.com/ephraim.becker/"><img src="img/Instagram_Glyph_Gradient_RGB.png"  width="50px" height="auto"></a>
          <a target="_blank" style="display: inline; background-color: rgba(0,0,0,0);" href="https://twitter.com/emb180"><img src="img/twitterLogo.png"  width="50px" height="auto"></a>
          <a target="_blank" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.youtube.com/channel/UCIHxAXYLxYlNaQiv0do0bUg"><img src="img/yt_icon_rgb.png"  width="50px" height="auto"></a>
          <a target="_blank" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.linkedin.com/in/ephraim-becker-3263b810b/"><img src="img/LI-In-Bug.png"  width="50px" height="auto"></a>
          <a target="_blank" style="display: inline; background-color: rgba(0,0,0,0);" href="https://github.com/EphraimB"><img src="img/GitHub-Mark-64px.png"  width="50px" height="auto"></a>
          <a target="_blank" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.autismforums.com/members/ephraim-becker.16496/"><img style="border: 1px solid black;" src="img/aflogo.png"  width="auto" height="50px"></a>
          <a target="_blank" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.aspergerexperts.com/profile/60330-ephraim-becker/"><img src="img/aesquare.png"  width="auto" height="50px"></a>
        </div>
      </div>
    </main>
    <script src="js/script.js"></script>
  </body>
</html>
