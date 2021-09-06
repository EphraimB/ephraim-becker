<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

  global $link;
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Ephraim Becker - Admin - Timeline</title>
    <link rel="stylesheet" href="../../css/style.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="icon" href="../../img/ephraim_becker.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="../../img/ephraim-becker.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  </head>
  <body>
    <nav>
      <ul>
        <li id="first"><img src="../../img/ephraim-becker.jpg" alt="Photo of Ephraim Becker" width="70px" height="70px" /></li>
        <li id="hamburger-icon"><a href="#" onclick="toggleNavMenu()">&#9776;</a></li>
        <div id="links">
          <li><a href="../">Admin</a></li>
          <li class="focus"><a href="#">Timeline</a></li>
        </div>
      </ul>
    </nav>
    <header>
      <h1 style="font-weight: bold;">Admin - Timeline</h1>
    </header>
    <main>
      <table>
        <tr>
          <td rowspan="2">Legend</td>
          <td class="remembered-memory">Remembered memory</td>
        </tr>
        <tr>
          <td class="diary-memory">Diary memory</td>
        </tr>
      </table>
      <div class="row">
        <ul class="subNav">
          <li><a style="text-decoration: none;" href="addEvent/">+</a></li>
        </ul>
      </div>
      <?php
      $sql = "SELECT * FROM timeline ORDER BY DateModified ASC";
      $sqlResult = mysqli_query($link, $sql);

      while($row = mysqli_fetch_array($sqlResult)){
        $eventDate = $row['EventDate'];
        $eventTime = $row['EventTime'];

        $endEventDate = $row['EndEventDate'];

        $endEventDateFormatted = date("F d, Y", strtotime($endEventDate));

        $eventDateFormatted = date("F d, Y", strtotime($eventDate));
        $eventTimeFormatted = date("h:i A", strtotime($eventTime));

        $eventTitle = $row['EventTitle'];
        $eventDescription = $row['EventDescription'];
        $memoryType = $row['MemoryType'];

        $eventYouTubeLink = $row['EventYouTubeLink'];

        $eventMedia = $row['EventMedia'];
        $eventMediaDescription = $row['EventMediaDescription'];
      ?>

      <div class="<?php if($memoryType == 0) { echo 'remembered-memory'; } else if($memoryType == 1) { echo 'diary-memory'; } ?> ">
        <h2><time itemprop="startDate" datetime="<?php echo $eventDate ?>"><?php if(!is_null($eventTime)) { echo $eventDateFormatted . " " . $eventTimeFormatted; } else { echo $eventDateFormatted; } ?></time><?php if(!is_null($endEventDate)) { echo " - <time itemprop='endDate' datetime='" . $endEventDate . "'>" . $endEventDateFormatted . "</time>"; } ?></h2>
        <h3 itemprop="name"><?php echo $eventTitle ?></h3>
        <p itemprop="description"><?php echo $eventDescription ?></p>

        <?php
        if(!is_null($eventYouTubeLink)) {
        ?>
        <iframe width="560" height="315" src="<?php echo $eventYouTubeLink ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        <?php
        }

        if(!is_null($eventMedia)) {
        ?>
          <img src="img/<?php echo $eventMedia ?>" alt="<?php echo $eventMediaDescription ?>" width="200px" height="auto" />
        <?php } ?>
      </div>

    <?php } ?>
    </main>
    <script src="../../js/script.js"></script>
  </body>
</html>
