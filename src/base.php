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
        <script src="' . $this->getDocumentRoot() . '/wheelnav-master/js/required/raphael.min.js"></script>
        <script src="' . $this->getDocumentRoot() . '/wheelnav-master/js/required/raphael.icons.min.js"></script>
        <script src="' . $this->getDocumentRoot() . '/wheelnav-master/js/dist/wheelnav.min.js"></script>
        <link rel="stylesheet" href="' . $this->getDocumentRoot() . '/wheelnav-master/css/index.css" />
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

  function loginNotification(): string
  {
    $html = '';

    if(isset($_GET['loggedIn'])) {
      if($_GET['loggedIn'] == true) {
        $html .= '<div id="loginNotification" style="background-color: green; color: white; width: 100%; height: 2%; padding: 2%;">
          <p>Login sucessful</p>
        </div>';
      } else if($_GET['loggedIn'] == false) {
        $html .= '<div id="loginNotification" style="background-color: red; color: white; width: 100%; height: 2%; padding: 2%;">
          <p>Login failed</p>
        </div>';
      }
    }

    return $html;
  }

  function loginNotificationDisappear(): string
  {
    $html = '<script>
      const myTimeout = setTimeout(clearNotification, 2000);

      function clearNotification() {
        document.getElementById("loginNotification").style.display = "none";
      }
    </script>';

    return $html;
  }

  function nav(): string
  {
    $html = '
    <nav>
        <div id="piemenu" data-wheelnav
        data-wheelnav-colors="#ADD8E6"
         data-wheelnav-slicepath="DonutSlice"
         data-wheelnav-navangle="270"
         data-wheelnav-titleheight="45"
         data-wheelnav-init>
          <div data-wheelnav-navitemicon="imgsrc:/img/nav-icons/home_black_24dp.svg"><a href="' . $this->getDocumentRoot() . '/index.php">Home</a></div>
          <div data-wheelnav-navitemicon="imgsrc:/img/nav-icons/timeline_black_24dp.svg"><a href="' . $this->getDocumentRoot() . '/timeline/">Timeline</a></div>
          <div data-wheelnav-navitemicon="imgsrc:/img/nav-icons/sentiment_dissatisfied_black_24dp.svg"><a href="javascript:;"></a></div>
          <div data-wheelnav-navitemicon="imgsrc:/img/nav-icons/code_black_24dp.svg"><a href="' . $this->getDocumentRoot() . '/projects/">Projects</a></div>
          <div data-wheelnav-navitemicon="imgsrc:/img/nav-icons/link_black_24dp.svg"><a href="' . $this->getDocumentRoot() . '/resources/">Resources</a></div>
          <div data-wheelnav-navitemicon="imgsrc:/img/nav-icons/info_black_24dp.svg"><a href="' . $this->getDocumentRoot() . '/about/">About</a></div>';
          if(isset($_SESSION['username'])) {
            $html .= '<div data-wheelnav-navitemicon="imgsrc:/img/nav-icons/ephraim-becker-round.png"><a href="javascript:;"></a></div>
            <div data-wheelnav-navitemicon="imgsrc:/img/nav-icons/logout_black_24dp.svg"><a href="' . $this->getDocumentRoot() . '/adminLogout.php?fromUrl=' . $this->getUrl() . '">Logout</a></div>';
          } else {
            $html .= '<div data-wheelnav-navitemicon="imgsrc:/img/nav-icons/login_black_24dp.svg"><a href="' . $this->getDocumentRoot() . '/adminLogin/index.php?fromUrl=' . $this->getUrl() . '">Login</a></div>';
          }

      $html .= '</div>
      <div id="piesubmenu" data-wheelnav
      data-wheelnav-colors="#497F4C"
        data-wheelnav-slicepath="DonutSlice"
        data-wheelnav-navangle="270"
        data-wheelnav-titleheight="50"
        data-wheelnav-init>
        <div data-wheelnav-navitemicon="imgsrc:/img/nav-icons/sentiment_dissatisfied_black_24dp.svg"><a href="' . $this->getDocumentRoot() . '/everydayLife/">Everyday Life</a></div>
        <div data-wheelnav-navitemicon="imgsrc:/img/nav-icons/school_black_24dp.svg"><a href="' . $this->getDocumentRoot() . '/college/">College Life</a></div>
      </div>

      <div id="pieadminmenu" data-wheelnav
      data-wheelnav-colors="#013f28"
       data-wheelnav-slicepath="DonutSlice"
       data-wheelnav-navangle="270"
       data-wheelnav-titleheight="45"
       data-wheelnav-init>
        <div data-wheelnav-navitemicon="imgsrc:/img/nav-icons/paid_white_24dp.svg"><a href="' . $this->getDocumentRoot() . '/budgeting/">Budgeting</a></div>
      </div>';

    if(isset($_SESSION['username'])) {
      $html .= '<script>
        var isAdmin = true;
      </script>';
    } else {
      $html .= '<script>
        var isAdmin = false;
      </script>';
    }

    $html .= '</nav>';

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

    function scripts(): string
    {
      $html = '<script src="' . $this->getDocumentRoot() . '/js/script.js"></script>';

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
      $html .= $this->loginNotification();
      $html .= $this->loginNotificationDisappear();
      $html .= $this->nav();
      $html .= $this->header();
      $html .= $this->main();
      $html .= $this->scripts();
      $html .= '
        </body>
      </html>';

      echo $html;
    }
}
?>
