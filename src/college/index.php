<?php
  $title = "Ephraim Becker - College";
  $header = "College Life";
  $localStyleSheet = NULL;
  $localScript = NULL;

  $body = '<table>
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
      </div>';

  $url = $_SERVER['REQUEST_URI'];
  require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
?>