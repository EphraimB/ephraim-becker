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
        <li><a href="about.php">About</a></li>
      </ul>
    </nav>
    <main>
      <div>
        <h3>Ephraim Becker</h3>
        <span>Age: </span>
        <span><?php echo $age; ?></span>
      </div>
    </main>
  </body>
</html>
