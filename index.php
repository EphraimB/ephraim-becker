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
  </head>
  <body>
    <nav>
      <ul>
        <li><a id="first" href="#"><img src="img/ephraim-becker.jpg" width="50px" height="auto"></a></li>
        <li><a href="timeline.php">Timeline</a></li>
        <li><a href="contact.php">Contact me</a></li>
        <li><a href="about.php">About</a></li>
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
          <span>Modern Orthodox Jewish</span>
        </p>
        <p>
          <span style="font-weight: bold;">Interests: </span>
          <span>Technology, Astronomy, Trains</span>
        </p>
        <p>
          <span style="font-weight: bold;">Location: </span>
          <span>Far Rockaway, NY, United States</span>
        </p>
        <div>
          <a target="_blank" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.facebook.com/ephraim.becker/"><img src="img/f-Logos-2019-1/f_Logo_Online_04_2019/Color/PNG/f_logo_RGB-Blue_58.png" width="50px" height="auto"></a>
          <a target="_blank" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.messenger.com/t/100009677345527/"><img src="img/Messenger-BRC-10.19.2020/Downloadable Assets/Logo/sRGB/logo-16@3x.png"  width="50px" height="auto"></a>
          <a target="_blank" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.instagram.com/ephraim.becker/"><img src="img/Instagram-BRC-Asset-Downloads-Feb-2021-20210302T011642Z-001/Instagram BRC Asset Downloads - Feb 2021/IG Glyph Icon/Instagram_Glyph_Gradient_RGB.png"  width="50px" height="auto"></a>
          <a target="_blank" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.youtube.com/channel/UCIHxAXYLxYlNaQiv0do0bUg"><img src="img/youtube_full_color_icon/youtube_full_color_icon/digital_and_tv/yt_icon_rgb.png"  width="50px" height="auto"></a>
        </div>
      </div>
    </main>
  </body>
</html>
