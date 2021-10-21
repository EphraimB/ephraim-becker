<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="<?php $_SERVER['DOCUMENT_ROOT'] ?>/css/style.css" />
    <?php echo $localStyleSheet; ?>
    <link rel="canonical" href="https://www.ephraimbecker.com/" />
    <link rel="icon" href="/img/ephraim_becker.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="/img/ephraim-becker.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Hi! My name is Ephraim Becker and this is a website about my life and how people can learn from it." />
    <meta name="keywords" content="Ephraim Becker, autism, aspergers, ADHD" />
  </head>
  <body>
    <nav>
      <ul>
        <li id="first"><img src="<?php $_SERVER['DOCUMENT_ROOT'] ?>/img/ephraim-becker.jpg" alt="Photo of Ephraim Becker" width="70px" height="70px" /></li>
        <li id="hamburger-icon"><a href="javascript:;" onclick="toggleNavMenu()">&#9776;</a></li>
        <div id="links">
          <li><a href="<?php $_SERVER['DOCUMENT_ROOT'] ?>/index.php">Home</a></li>
          <li><a href="<?php $_SERVER['DOCUMENT_ROOT'] ?>/timeline/">Timeline</a></li>
          <div id="dropdown">
            <li><a href="javascript:;" onclick="toggleNavSubmenu()">Daily Life &emsp; &#x25BC;</a></li>
            <div id="dropdown-content">
              <li><a href="<?php $_SERVER['DOCUMENT_ROOT'] ?>/everydayLife/">Everyday Life</a></li>
              <li><a href="<?php $_SERVER['DOCUMENT_ROOT'] ?>/college/">College Life</a></li>
            </div>
          </div>
          <li><a href="<?php $_SERVER['DOCUMENT_ROOT'] ?>/projects/">Projects</a></li>
          <li><a href="<?php $_SERVER['DOCUMENT_ROOT'] ?>/resources/">Resources</a></li>
          <li><a href="<?php $_SERVER['DOCUMENT_ROOT'] ?>/about/">About</a></li>
          <?php
          if(isset($_SESSION['username'])) {
          ?>
            <li><a href="<?php $_SERVER['DOCUMENT_ROOT'] ?>/adminLogout.php?fromUrl=<?php echo $url ?>">Logout</a></li>
          <?php
          } else {
          ?>
            <li><a href="<?php $_SERVER['DOCUMENT_ROOT'] ?>/adminLogin/index.php?fromUrl=<?php echo $url ?>">Login</a></li>
          <?php
          }
          ?>
        </div>
      </ul>
    </nav>
    <header>
      <h1 style="font-weight: bold;"><?php echo $header; ?></h1>
    </header>
    <body>
      <main id="main">
        <?php echo $body; ?>
      </main>
    </body>
    <footer>
      <p>&copy; 2021 Ephraim Becker</p>
    </footer>
    <script src="<?php $_SERVER['DOCUMENT_ROOT'] ?>/js/script.js"></script>
    <?php echo $localScript; ?>
  </body>
</html>
