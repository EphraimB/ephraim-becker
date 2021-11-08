<?php
declare(strict_types=1);

session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class Accomplishments extends Base
{

  function __construct()
  {

  }

  function main(): string
  {
    $body = '<div class="grid-container">
          <div style="background-color: green;" class="card">
            <a href="../../projects/">
              <h2>Projects</h2>
              <p>I\'ve made some projects over the years</p>
              <p>Click to view</p>
            </a>
          </div>
          <div style="background-color: green;" class="card">
            <a href="../../../college/">
              <h2>College</h2>
              <p>I\'m in middle of college</p>
              <p>Click to view</p>
            </a>
          </div>
          <div style="background-color: green;" class="card">
            <a href="gamingSetup/">
              <h2>I built my own computer</h2>
              <p>I built my own gaming computer with a semi-fancy setup</p>
              <p>Click to view</p>
            </a>
          </div>
          <div style="background-color: green;" class="card">
            <h2>I took Amtrak from New York to Florida by myself</h2>
            <p>I took Amtrak from NY Penn Station to Deerfield Beach, FL for Passover in 2018 (March 28 to be exact). I got Red Cap disability services at NY Penn Station because of my autism. Unfortunately, the trip didn\'t go as planned because of the 14 hour delay at Petersburg, VA becuase of a derailed auto-train ahead. I started crying because things didn\'t go as planned and I was anxious that I wasn\'t going to make it for Passover but people on the train calmed me down. I arrived at Deerfield Beach Friday morning at 8AM instead of Thursday afternoon at 5:45PM. My father picked me up.</p>
            <img src="img/AmtrakAtDeerfieldBeach.jpg" alt="Amtrak at Deerfield Beach" width="250px" height="auto" />
          </div>
        </div>';

      return $body;
    }
}

$accomplishments = new Accomplishments();
$accomplishments->setUrl($_SERVER['REQUEST_URI']);
$accomplishments->setTitle('Ephraim Becker - Everyday Life - Accomplishments');
$accomplishments->setLocalStyleSheet('css/style.css');
$accomplishments->setLocalScript(NULL);
$accomplishments->setHeader('Accomplishments');
$accomplishments->setBody($accomplishments->main());

$accomplishments->html();
?>
