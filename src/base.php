<?php declare(strict_types=1);
class Base
{
  private $localStyleSheet;
  private $url;
  private $title;
  private $header;
  private $body;
  private $localScript;
  private $documentRoot;

  function __construct($localStyleSheet, string $url, string $title, $header, string $body, $localScript)
  {
    $this->localStyleSheet = $localStyleSheet;
    $this->url = $url;
    $this->title = $title;
    $this->header = $header;
    $this->body = $body;
    $this->localScript = $localScript;
    $this->documentRoot = $_SERVER['DOCUMENT_ROOT'];
  }

  function getDocumentRoot(): string
  {
    return $this->documentRoot;
  }

  function getLocalStyleSheet()
  {
    return $this->localStyleSheet;
  }

  function getUrl(): string
  {
    return $this->url;
  }

  function getTitle(): string
  {
    return $this->title;
  }

  function getHeader()
  {
    return $this->header;
  }

  function getBody(): string
  {
    return $this->body;
  }

  function getLocalScript()
  {
    return $this->localScript;
  }

  function ensureValidDocumentRoot(): bool
  {
    if($this->getDocumentRoot() == '/') {
      return true;
    } else {
      return false;
    }
  }

  function head($localStyleSheet, string $title): string
  {
    $html = '
      <head>
        <meta charset="utf-8">
        <title>' . $title . '</title>
        <link rel="stylesheet" href="' . $this->getDocumentRoot() . '/css/style.css" />
        ' . $this->getLocalStyleSheet() . '
        <link rel="canonical" href="https://www.ephraimbecker.com/" />
        <link rel="icon" href="' . $this->getDocumentRoot() . '/img/ephraim_becker.ico" type="image/x-icon" />
        <link rel="apple-touch-icon" href="' . $this->getDocumentRoot() . '/img/ephraim-becker.png" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Hi! My name is Ephraim Becker and this is a website about my life and how people can learn from it." />
        <meta name="keywords" content="Ephraim Becker, autism, aspergers, ADHD" />
      </head>
    ';

    return $html;
  }

  function nav(): string
  {
    $html = '
      <nav>
        <ul>
          <li id="first"><img src="' . $this->getDocumentRoot() . '/img/ephraim-becker.jpg" alt="Photo of Ephraim Becker" width="70px" height="70px" /></li>
          <li id="hamburger-icon"><a href="javascript:;" onclick="toggleNavMenu()">&#9776;</a></li>
          <div id="links">
            <li><a href="' . $this->getDocumentRoot() . '/index.php">Home</a></li>
            <li><a href="' . $this->getDocumentRoot() . '/timeline/">Timeline</a></li>
            <div id="dropdown">
              <li><a href="javascript:;" onclick="toggleNavSubmenu()">Daily Life &emsp; &#x25BC;</a></li>
              <div id="dropdown-content">
                <li><a href="' . $this->getDocumentRoot() . '/everydayLife/">Everyday Life</a></li>
                <li><a href="' . $this->getDocumentRoot() . '/college/">College Life</a></li>
              </div>
            </div>
            <li><a href="' . $this->getDocumentRoot() . '/projects/">Projects</a></li>
            <li><a href="' . $this->getDocumentRoot() . '/resources/">Resources</a></li>
            <li><a href="' . $this->getDocumentRoot() . '/about/">About</a></li>';

            if(isset($_SESSION['username'])) {
              $html .= '<li><a href="' . $this->getDocumentRoot() . '/adminLogout.php?fromUrl=' . $this->getUrl() . '">Logout</a></li>';
            } else {
              $html .= '<li><a href="' . $this->getDocumentRoot() . '/adminLogin/index.php?fromUrl=' . $this->getUrl() . '">Login</a></li>';
            }
          $html .= '</div>
        </ul>
      </nav>';

      return $html;
    }

    function header(): string
    {
      $html = '<header>
        <h1 style="font-weight: bold;">' . $this->getHeader() . '</h1>
      </header>';

      return $html;
    }

    function main(): string
    {
      $html = '
        <main id="main">
          ' . $this->getBody() . '
        </main>';

      return $html;
    }

    function footer(): string
    {
      $html = '<footer>
        <p>&copy; 2021 Ephraim Becker</p>
      </footer>';

      return $html;
    }

    function scripts(): string
    {
      $html = '<script src="' . $this->getDocumentRoot() . '/js/script.js"></script>
      ' . $this->getLocalScript();

      return $html;
    }

    function html()
    {
      $html = '<!DOCTYPE html>
      <html lang="en" dir="ltr">';
      $html .= $this->head($this->getLocalStyleSheet(), $this->getTitle());
      $html .= '<body>';
      $html .= $this->nav($this->getUrl());
      $html .= $this->header($this->getHeader());
      $html .= $this->main($this->getBody());
      $html .= $this->footer();
      $html .= $this->scripts($this->getLocalScript());
      $html .= '
        </body>
      </html>';

      echo $html;
    }
}

$base = new Base($localStyleSheet, $url, $title, $header, $body, $localScript);
$base->html();
?>
