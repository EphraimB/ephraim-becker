<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Ephraim Becker - Virtual Friend</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="../../img/ephraim_becker.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="../../img/ephraim-becker.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Hi! My name is Ephraim Becker and this is my Virtual Friend project">
    <meta name="keywords" content="Ephraim Becker, virtual friend">
  </head>
  <body>
    <main>
      <div id="register">Register</div>

      <div id="closeRegistrationForm">X</div>

      <div id="registrationForm">
        <form method="post" action="php/register.php">
          <fieldset id="profileSection">
            <legend>Profile</legend>
            <div id="avatar"></div>
            <label for="avaterInput" id="avatarInputLabel">Upload photo of yourself</label>
            <input type="file" id="avatarInput" required>
          </fieldset>

          <fieldset id="passwordSection">
            <legend>Password</legend>
            <input type="password" id="passwordInput" placeholder="Password" required>
            <input type="password" id="confirmPasswordInput" placeholder="Confirm Password" required>
          </fieldset>

          <fieldset id="genderSection">
            <legend>Gender</legend>

            <label for="male" id="maleInputLabel">Male</label>
            <input type="radio" name="gender" id="male" required>

            <label for="female" id="femaleInputLabel">Female</label>
            <input type="radio" name="gender" id="female" required>
          </fieldset>

          <input type="submit" id="submitRegistrationForm">
        </form>
      </div>

      <div id="logIn">Log In</div>

      <div id="closeLogInForm">X</div>

      <div id="logInForm">
        <form method="post" action="php/login.php">
          <p id="formTitle">Log In</p>
          <input type="text" id="accountIdInput" placeholder="ID" required>
          <input type="password" id="accountPasswordInput" placeholder="Password" required>

          <input type="submit" id="submitLogInForm">
        </form>
      </div>

      <div id="account">

      </div>
      <div id="accountMenu">
        <ul>
          <li id="settings">Settings</li>
          <li id="logOut">Log Out</li>
        </ul>
      </div>
      <div id="sky">
        <div id="star"></div>
      </div>
      <div id="ground"></div>
      <div id="personOne">
        <div id="personOneFace">
          <div id="personOneLeftEye"></div>
          <div id="personOneRightEye"></div>
          <div id="personOneMouth"></div>
        </div>
        <div id="personOneBody"></div>
        <div id="personOneLeftLeg"></div>
        <div id="personOneRightLeg"></div>
        <div id="personOneLeftArm"></div>
        <div id="personOneRightArm"></div>
        <div id="personOneTalk"></div>

        <div id="personOneSmartphone">
          <div id="statusBar">Verizon LTE</div>
          <div id="content">
          <iframe id="displayedContent" src=""></iframe>
          </div>
        </div>
      </div>

      <div id="personTwo">
        <div id="personTwoFace">
          <div id="personTwoLeftEye"></div>
          <div id="personTwoRightEye"></div>
          <div id="personTwoMouth"></div>
        </div>
        <div id="personTwoBody"></div>
        <div id="personTwoLeftLeg"></div>
        <div id="personTwoRightLeg"></div>
        <div id="personTwoLeftArm"></div>
        <div id="personTwoRightArm"></div>
        <div id="personTwoTalk"><input type="text" id="personTwoSays" placeholder= "Type reply...">
        <div class="submitButton" id="submitButton">Submit</div>
        </div>
      </div>
      <div id="clock">--:-- -M</div>
      <div id="threeDimension">3D</div>
      <div id="version">Version 0.8.0 beta</div>
      </main>
    </main>
    <script src="../js/script.js"></script>
    <script src="js/script.js"></script>
  </body>
</html>
