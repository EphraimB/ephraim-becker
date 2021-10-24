<?php
declare(strict_types=1);

session_start();

global $link;

require($_SERVER['DOCUMENT_ROOT'] . "/base.php");

class Index extends Base
{
  private $age;

  function __construct()
  {
    $this->setAge();
  }

  function setAge(): void
  {
    $epochTime = mktime(0, 0, 0, 07, 19, 1996);
    $birthDate = "1996-07-19";

    $age = date_diff(date_create($birthDate), date_create(date("Y-m-d")));

    $this->age = intval($age->format("%y"));
  }

  function getAge():int
  {
    return $this->age;
  }

  function profileCard(): string
  {
    $html = '<img src="img/ephraim-becker.jpg" itemprop="image" alt="Photo of Ephraim Becker" width="100px" height="100px" />
    <h1 style="font-weight: bold;" itemprop="name">Ephraim Becker</h1>
    <p>
      <span style="font-weight: bold;">Age: </span>
      <span>' . $this->getAge() . '</span>
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
    </p>';

    return $html;
  }

  function socialMediaLinks(): string
  {
    $html = '<div class="socialLinks">
      <a target="_blank" rel="noopener" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.facebook.com/ephraim.becker/"><img src="img/f_logo_RGB-Blue_1024.png" alt="Facebook logo" width="50px" height="auto" /></a>
      <a target="_blank" rel="noopener" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.messenger.com/t/100009677345527/"><img src="img/logo-16@3x.png" alt="Messenger logo" width="50px" height="auto" /></a>
      <a target="_blank" rel="noopener" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.instagram.com/ephraim.becker/"><img src="img/Instagram_Glyph_Gradient_RGB.png" alt="Instagram logo" width="50px" height="auto" /></a>
      <a target="_blank" rel="noopener" style="display: inline; background-color: rgba(0,0,0,0);" href="https://twitter.com/emb180"><img src="img/twitterLogo.png" alt="Twitter logo" width="50px" height="auto"></a>
      <a target="_blank" rel="noopener" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.youtube.com/channel/UCIHxAXYLxYlNaQiv0do0bUg"><img src="img/yt_icon_rgb.png" alt="YouTube logo" width="50px" height="auto" /></a>
      <a target="_blank" rel="noopener" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.linkedin.com/in/ephraim-becker-3263b810b/"><img src="img/LI-In-Bug.png" alt="Linkedin logo" width="50px" height="auto" /></a>
      <a target="_blank" rel="noopener" style="display: inline; background-color: rgba(0,0,0,0);" href="https://github.com/EphraimB"><img src="img/GitHub-Mark-64px.png" alt="GitHub logo" width="50px" height="auto" /></a>
      <a target="_blank" rel="noopener" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.autismforums.com/members/ephraim-becker.16496/"><img style="border: 1px solid black;" src="img/aflogo.png" alt="Autism Forums logo" width="auto" height="50px" /></a>
      <a target="_blank" rel="noopener" style="display: inline; background-color: rgba(0,0,0,0);" href="https://www.aspergerexperts.com/profile/60330-ephraim-becker/"><img src="img/aesquare.png" alt="Aspergers Experts logo" width="auto" height="50px" /></a>
    </div>';

    return $html;
  }

  function main(): string
  {
    $html = '<div id="profileCard" itemscope itemtype="https://schema.org/Person">';
    $html .= $this->profileCard();
    $html .= $this->socialMediaLinks();
    $html .= '</div>';

    return $html;
  }
}

$index = new Index();
$index->setTitle("Ephraim Becker - All about my autistic life");
$index->setLocalStyleSheet(NULL);
$index->setLocalScript(NULL);
$index->setHeader(NULL);
$index->setUrl('/');
$index->setBody($index->main());

$index->html();
?>
