<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Ephraim Becker - College</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="../img/ephraim_becker.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="../img/ephraim-becker.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Hi! My name is Ephraim Becker and this is how I'm doing in college.">
    <meta name="keywords" content="Ephraim Becker, autism, aspergers, ADHD">
  </head>
  <body>
    <nav>
      <ul>
        <li id="first"><img src="../img/ephraim-becker.jpg" alt="Ephraim Becker" width="122px" height="auto"></li>
        <li id="hamburger-icon"><a href="#" onclick="toggleNavMenu()">&#9776;</a></li>
        <div id="links">
          <li><a href="../">Home</a></li>
          <li><a href="../timeline/">Timeline</a></li>
          <li><a href="../everydayLife/">Everyday Life</a></li>
          <li><a href="#">> College Life <</a></li>
          <li><a href="../resources/">Resources</a></li>
          <li><a href="../about/">About</a></li>
        </div>
      </ul>
    </nav>
    <header>
      <h1 style="font-weight: bold;">College Life</h1>
    </header>
    <main>
      <table>
        <caption>Touro College - BS in Computer Science</caption>
        <tr>
          <th>Semester</th>
          <th>Course</th>
          <th>Credits</th>
          <th>Grade</th>
        </tr>
        <tr class="semesterDivider">
          <td>Spring 2020</td>
          <td>Fundamentals Of Computer W Micro</td>
          <td style="font-weight: bold;">4</td>
          <td>A+</td>
        </tr>
        <tr class="semesterDivider">
          <td rowspan="4">Fall 2020</td>
          <td>Computing Theory And Applications</td>
          <td style="font-weight: bold;">4</td>
          <td>A</td>
        </tr>
        <tr>
          <td>Introduction To Programming</td>
          <td style="font-weight: bold;">3</td>
          <td>B</td>
        </tr>
        <tr>
          <td>English Composition I</td>
          <td style="font-weight: bold;">0</td>
          <td>Incomplete</td>
        </tr>
        <tr>
          <td>Readings In Rambam</td>
          <td style="font-weight: bold;">3</td>
          <td>C+</td>
        </tr>
        <tr class="semesterDivider">
          <td rowspan="4">Spring 2021</td>
          <td>Advanced Programming & File Struct</td>
          <td style="font-weight: bold;">3</td>
          <td>A</td>
        </tr>
        <tr>
          <td>Computer Architecture</td>
          <td style="font-weight: bold;">3</td>
          <td>A</td>
        </tr>
        <tr>
          <td>College Math</td>
          <td style="font-weight: bold;">3</td>
          <td>A</td>
        </tr>
        <tr>
          <td>English Composition I (audit for makeup work)</td>
          <td style="font-weight: bold;">3</td>
          <td>A-</td>
        </tr>
        <tr class="semesterDivider">
          <td rowspan="4">Fall 2021</td>
          <td>Database Management and Administration</td>
          <td>3</td>
          <td></td>
        </tr>
        <tr>
          <td>Data Structures I</td>
          <td>3</td>
          <td></td>
        </tr>
        <tr>
          <td>Pre-Calculus</td>
          <td>3</td>
          <td></td>
        </tr>
        <tr>
          <td>Fund Of Speech I</td>
          <td>3</td>
          <td></td>
        </tr>
        <tr>
          <td colspan="4"><span style="font-weight: bold;">Total completed credits: </span>26/120</td>
        </tr>
      </table>

      <div style="margin-top: 10px;">
        <label for="MajorProgress">Major progress:</label>
        <progress id="MajorProgress" value="17" max="56">30%</progress>
      </div>

      <div>
        <label for="CoreProgress">Core progress:</label>
        <progress id="CoreProgress" value="9" max="24">37.5%</progress>
      </div>

      <div>
        <label for="DegreeProgress">Degree progress:</label>
        <progress id="DegreeProgress" value="26" max="120">21.7%</progress>
      </div>
    </main>
    <script src="../js/script.js"></script>
  </body>
</html>
