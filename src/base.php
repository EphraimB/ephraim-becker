<?php declare(strict_types=1);
final class Base
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

  function ensureValidDocumentRoot(string $documentRoot): bool
  {
    if($documentRoot == '/') {
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
        ' . $localStyleSheet . '
        <link rel="canonical" href="https://www.ephraimbecker.com/" />
        <link rel="icon" href="' . $documentRoot . '/img/ephraim_becker.ico" type="image/x-icon" />
        <link rel="apple-touch-icon" href="' . $documentRoot . '/img/ephraim-becker.png" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Hi! My name is Ephraim Becker and this is a website about my life and how people can learn from it." />
        <meta name="keywords" content="Ephraim Becker, autism, aspergers, ADHD" />
      </head>
    ';

    return $html;
  }

  function nav(string $documentRoot, string $url): string
  {
    $html = '
      <nav>
        <ul>
          <li id="first"><img src="' . $documentRoot . '/img/ephraim-becker.jpg" alt="Photo of Ephraim Becker" width="70px" height="70px" /></li>
          <li id="hamburger-icon"><a href="javascript:;" onclick="toggleNavMenu()">&#9776;</a></li>
          <div id="links">
            <li><a href="' . $documentRoot . '/index.php">Home</a></li>
            <li><a href="' . $documentRoot . '/timeline/">Timeline</a></li>
            <div id="dropdown">
              <li><a href="javascript:;" onclick="toggleNavSubmenu()">Daily Life &emsp; &#x25BC;</a></li>
              <div id="dropdown-content">
                <li><a href="' . $documentRoot . '/everydayLife/">Everyday Life</a></li>
                <li><a href="' . $documentRoot . '/college/">College Life</a></li>
              </div>
            </div>
            <li><a href="' . $documentRoot . '/projects/">Projects</a></li>
            <li><a href="' . $documentRoot . '/resources/">Resources</a></li>
            <li><a href="' . $documentRoot . '/about/">About</a></li>';

            if(isset($_SESSION['username'])) {
              $html .= '<li><a href="' . $documentRoot . '/adminLogout.php?fromUrl=' . $url . '">Logout</a></li>';
            } else {
              $html .= '<li><a href="' . $documentRoot . '/adminLogin/index.php?fromUrl=' . $url . '">Login</a></li>';
            }
          $html .= '</div>
        </ul>
      </nav>';

      return $html;
    }

    function header($header): string
    {
      $html = '<header>
        <h1 style="font-weight: bold;">' . $header . '</h1>
      </header>';

      return $html;
    }

    function main(string $body): string
    {
      $html = '
        <main id="main">
          ' . $body . '
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

    function scripts($localScript, string $documentRoot): string
    {
      $html = '<script src="' . $documentRoot . '/js/script.js"></script>
      ' . $localScript;

      return $html;
    }

    function html($localStyleSheet, string $url, string $title, $header, string $body, $localScript, string $documentRoot)
    {
      $html = '<!DOCTYPE html>
      <html lang="en" dir="ltr">';
      $html .= $this->head($localStyleSheet, $title);
      $html .= '<body>';
      $html .= $this->nav($documentRoot, $url);
      $html .= $this->header($header);
      $html .= $this->main($body);
      $html .= $this->footer();
      $html .= $this->scripts($localScript, $documentRoot);
      $html .= '
        </body>
      </html>';

      echo $html;
    }
}

$base = new Base($localStyleSheet, $url, $title, $header, $body, $localScript);
$documentRoot = $base->getDocumentRoot();
$base->html($localStyleSheet, $url, $title, $header, $body, $localScript, $documentRoot);
?>
