<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . '/environment.php');

  global $link;

  session_start();

  if(isset($_POST['loginButton'])) {
    $sql = $link->prepare("SELECT * FROM admins WHERE username=?");
    $sql->bind_param("s", $username);

    $username = $_POST['username'];

    $sql->execute();

    $sql = $sql->get_result();

    while($row = mysqli_fetch_array($sql)) {
      $password = $row['password'];
    }

    if(password_verify($_POST['password'], $password)) {
      $_SESSION['username'] = $_POST['username'];
    }
    
    header("location: /index.php");
  }
?>
