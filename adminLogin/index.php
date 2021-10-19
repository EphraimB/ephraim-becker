<?php
  $title = "Ephraim Becker - Admin login";
  $localStyleSheet = '<link rel="stylesheet" href="css/style.css" />';
  $header = "Ephraim Becker - Admin login";
  
  $localScript = NULL;

  $body = '<form action="login.php" method="post">
              <div class="row">
                <label for="username">User Name: </label>
                <input type="text" id="username" name="username" />
              </div>
              <br />
              <div class="row">
                <label for="password">Password: </label>
                <input type="password" id="password" name="password" />
              </div>
              <br />
              <input type="submit" name="loginButton" id="submitButton" value="Login" />
            </form>
  ';

  require($_SERVER['DOCUMENT_ROOT'] . "/base.php");
?>
