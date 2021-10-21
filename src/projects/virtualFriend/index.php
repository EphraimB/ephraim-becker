<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Ephraim Becker - Virtual Friend</title>
    <link rel="canonical" href="https://www.ephraimbecker.com/projects/virtualFriend/" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="icon" href="../../img/ephraim_becker.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="../../img/ephraim-becker.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" media="(prefers-color-scheme: light)" content="#0000FF" />
    <meta name="theme-color" media="(prefers-color-scheme: dark)" content="#000064" />
    <meta name="description" content="Hi! My name is Ephraim Becker and this is my Virtual Friend project" />
    <meta name="keywords" content="Ephraim Becker, virtual friend" />
  </head>
  <body>
    <main>
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
          <div id="statusBar">Verizon 5G</div>
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
    <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', function() {
      navigator.serviceWorker.register('js/service-worker.js').then(function(registration) {
      // Registration was successful
      console.log('Registered!');
      }, function(err) {
      // registration failed :(
      console.log('ServiceWorker registration failed: ', err);
      }).catch(function(err) {
      console.log(err);
      });
      });
      } else {
      console.log('service worker is not supported');
      }
    </script>
  </body>
</html>
