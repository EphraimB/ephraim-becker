<?php declare(strict_types=1);
class Base
{
  private $localStyleSheet;
  private $url;
  private $title;
  private $header;
  private $body;
  private $localScript;
  private $documentRoot = '';

  function __construct()
  {
    $this->documentRoot = '';
  }

  function getDocumentRoot(): string
  {
    return $this->documentRoot;
  }

  function getLocalStyleSheet()
  {
    return $this->localStyleSheet;
  }

  function setLocalStyleSheet($localStyleSheet): void
  {
    $this->localStyleSheet = $localStyleSheet;
  }

  function getUrl(): string
  {
    return $this->url;
  }

  function setUrl(string $url): void
  {
    $this->url = $url;
  }

  function getTitle(): string
  {
    return $this->title;
  }

  function setTitle(string $title): void
  {
    $this->title = $title;
  }

  function getHeader()
  {
    return $this->header;
  }

  function setHeader($header): void
  {
    $this->header = $header;
  }

  function getBody(): string
  {
    return $this->body;
  }

  function setBody(string $body): void
  {
    $this->body = $body;
  }

  function getLocalScript()
  {
    return $this->localScript;
  }

  function setLocalScript($localScript): void
  {
    $this->localScript = $localScript;
  }

  function ensureValidDocumentRoot(): bool
  {
    if($this->getDocumentRoot() == '') {
      return true;
    } else {
      return false;
    }
  }

  function head(): string
  {
    $html = '
      <head>
        <meta charset="utf-8">
        <title>' . $this->getTitle() . '</title>
        <script src="' . $this->getDocumentRoot() . '/js/wheelnav.min.js"></script>
        <script src="' . $this->getDocumentRoot() . '/js/raphael.min.js"></script>
        <script src="' . $this->getDocumentRoot() . '/js/raphael.icons.min.js"></script>
        <link rel="stylesheet" href="' . $this->getDocumentRoot() . '/css/style.css" />';

        if($this->getLocalStyleSheet() != NULL) {
          $html .= '<link rel="stylesheet" href="' . $this->getLocalStyleSheet() . '" />';
        }

        $html .= '<link rel="canonical" href="https://www.ephraimbecker.com' . $this->getUrl() . '" />
        <link rel="icon" href="'. $this->getDocumentRoot() . '/img/ephraim_becker.ico" type="image/x-icon" />
        <link rel="apple-touch-icon" href="' . $this->getDocumentRoot() . '/img/ephraim-becker.png" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Hi! My name is Ephraim Becker and this is a website about my life and how people can learn from it." />
        <meta name="keywords" content="Ephraim Becker, autism, aspergers, ADHD" />
      </head>';

    return $html;
  }

  function nav(): string
  {
    $html = '
        <nav id="divWheel" data-wheelnav data-wheelnav-titlewidth="50" data-wheelnav-titleheight="50">
          <div data-wheelnav-navitemtext="home"><a href="' . $this->getDocumentRoot() . '/index.php">Home</a></div>
          <div data-wheelnav-navitemtext="1""></div>
          <div data-wheelnav-navitemtext="2"><a href="#navitem"></a></div>
          <div data-wheelnav-navitemimg="../wheelnav_favicon.png"></div>
        </nav>

      <script>
      var wheel = new wheelnav("divWheel");
      wheel.createWheel();
      </script>';

    // $html .= '
    //   <nav>
    //     <ul>
    //       <li id="first"><img src="' . $this->getDocumentRoot() . '/img/ephraim-becker.jpg" alt="Photo of Ephraim Becker" width="70px" height="70px" /></li>
    //       <li id="hamburger-icon"><a href="javascript:;" onclick="toggleNavMenu()">&#9776;</a></li>
    //       <div id="links">
    //         <li><a href="' . $this->getDocumentRoot() . '/index.php">Home</a></li>
    //         <li><a href="' . $this->getDocumentRoot() . '/timeline/">Timeline</a></li>
    //         <div id="dropdown">
    //           <li><a href="javascript:;" onclick="toggleNavSubmenu()">Daily Life &emsp; &#x25BC;</a></li>
    //           <div id="dropdown-content">
    //             <li><a href="' . $this->getDocumentRoot() . '/everydayLife/">Everyday Life</a></li>
    //             <li><a href="' . $this->getDocumentRoot() . '/college/">College Life</a></li>
    //           </div>
    //         </div>
    //         <li><a href="' . $this->getDocumentRoot() . '/projects/">Projects</a></li>
    //         <li><a href="' . $this->getDocumentRoot() . '/resources/">Resources</a></li>
    //         <li><a href="' . $this->getDocumentRoot() . '/about/">About</a></li>';
    //
    //         if(isset($_SESSION['username'])) {
    //           $html .= '<li><a href="' . $this->getDocumentRoot() . '/adminLogout.php?fromUrl=' . $this->getUrl() . '">Logout</a></li>';
    //         } else {
    //           $html .= '<li><a href="' . $this->getDocumentRoot() . '/adminLogin/index.php?fromUrl=' . $this->getUrl() . '">Login</a></li>';
    //         }
    //       $html .= '</div>
    //     </ul>
    //   </nav>';

      return $html;
    }

    function header(): string
    {
      if($this->getHeader() != NULL) {
        $html = '<header>
          <h1 style="font-weight: bold;">' . $this->getHeader() . '</h1>
        </header>';
      } else {
        $html = '';
      }

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
      // $html = '<script src="' . $this->getDocumentRoot() . '/js/script.js"></script>';
      $html = '';

      if($this->getLocalScript() != NULL) {
        $html .= '<script src="' . $this->getLocalScript() . '"></script>';
      }

      return $html;
    }

    function html()
    {
      $html = '<!DOCTYPE html>
      <html lang="en" dir="ltr">';
      $html .= $this->head();
      $html .= '<body>';
      $html .= $this->nav();
      $html .= $this->header();
      $html .= $this->main();
      $html .= $this->footer();
      $html .= $this->scripts();
      $html .= '
        </body>
      </html>';

      echo $html;
    }
}
?>
