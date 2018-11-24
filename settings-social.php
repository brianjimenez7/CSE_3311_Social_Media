<?php
include('./classes/DB.php');
include('./classes/TESTLOGIN.php');
session_start();

if (!TESTLOGIN::isLoggedIn()) {
  header("Location: http://localhost/monosmash/login.php?set=1");
} 
else {
  $userid = TESTLOGIN::isLoggedIn();
  $connects = array();
  if (isset($_GET["fresh_connect"])) {
    $fresh_connect = (int)$_GET["fresh_connect"];
  }
  if (DB::query('SELECT user_id from facebook where user_id=:userid', array(':userid' => $userid))) {
    $connects['fb'] = 'CONNECTED';
  }
  if (DB::query('SELECT user_id from twitter where user_id=:userid', array(':userid' => $userid))) {
    $connects['twit'] = 'CONNECTED';
  }
  if (DB::query('SELECT user_id from youtube where user_id=:userid', array(':userid' => $userid))) {
    $connects['yt'] = 'CONNECTED';
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M"
    crossorigin="anonymous">
  <style>
    .container-fluid {
      padding-right: 0;
      padding-left: 0;
    }

    .myleftnav li {
      border: 1px solid #D3D3D3;
    }

    .myleftnav .current {
      border-left: 4px solid #d9534f;
    }

    .myleftnav .current a {
      font-weight: bold;
      color: black;
    }

    .myhomenav nav {
      border-bottom: 1px solid #D3D3D3;
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <!-- NAVBAR -->
    <div class="myhomenav">
      <nav class="navbar navbar-light bg-light justify-content-between">
        <a class="navbar-brand" href="home.php">
          <img src="img/planless.svg" width="40" height="40" alt="">
        </a>
        <form class="form-inline">
          <input class="form-control mr-sm-2" type="search" placeholder="Search">
        </form>
        <div>
          <a class="navbar-brand" href="settings.php">
            <i class="material-icons">settings</i>
          </a>
          <a class="navbar-brand" href="logout.php">
            <i class="material-icons">exit_to_app</i>
          </a>
        </div>
      </nav>
    </div>
    <!-- PAGE -->
    <!-- LEFT NAV LINKS -->
    <div class="row ml-5 mr-5 mt-5">
      <div class="col-md-2 mb-5 myleftnav">
        <ul class="nav nav-tabs flex-column">
          <li class="nav-item">
            <a class="nav-link" href="settings.php">Profile</a>
          </li>
          <li class="nav-item current">
            <a class="nav-link" href="settings-social.php">Social</a>
          </li>
        </ul>
      </div>
      <div class="col-md-9">
        <div class="card mysocial">
          <h4 class="card-header">Connected Social Accounts</h4>
          <div class="card-body">
          <div class="row align-items-center mt-2">
              <div class="col-sm-3">
                <h6>Youtube</h6>
              </div>
              <div class="col-sm-9">
                <?php if($fresh_connect==3) : ?>
                  <a href="#" class="btn btn-danger">CONNECTED</a>
                <?php elseif(array_key_exists("yt",$connects)) : ?>
                  <a href="#" class="btn btn-danger">CONNECTED</a>
                <?php else :?>
                  <a href="http://localhost/monosmash/youtube/callback.php" class="btn btn-danger" id="youtube">Click to Connect</a>
                <!-- <a href=""><i class="material-icons align-middle">close</i></a> -->
                <?php endif; ?>
              </div>
            </div>
            <div class="row align-items-center mt-2">
              <div class="col-sm-3">
                <h6>Twitter</h6>
              </div>
              <div class="col-sm-9">
                <?php if($fresh_connect==1) : ?>
                  <a href="#" class="btn btn-info">CONNECTED</a>
                <?php elseif(array_key_exists("twit",$connects)) : ?>
                  <a href="#" class="btn btn-info">CONNECTED</a>
                <?php else :?>
                  <a href="twitter/twitter_login.php" class="btn btn-info" id="twitter">Click to Connect</a>
                  <!-- <a href=""><i class="material-icons align-middle">close</i></a> -->
                <?php endif; ?>
              </div>
            </div>          
            <div class="row align-items-center mt-2">
              <div class="col-sm-3">
                <h6>Facebook</h6>
              </div>
              <div class="col-sm-9">
              <?php if($fresh_connect==2) : ?>
                <a href="#" class="btn btn-primary">CONNECTED</a>
              <?php elseif(array_key_exists("fb",$connects)) : ?>
                <a href="#" class="btn btn-primary">CONNECTED</a>
              <?php else :?>
                <a href="facebook/facebook_login.php" class="btn btn-primary" id="facebook">Click to Connect</a>
                  <!-- <a href=""><i class="material-icons align-middle">close</i></a> -->
              <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
    crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4"
    crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1"
    crossorigin="anonymous"></script>
  <script>
    $(document).ready(function () {
    });
  </script>
</body>
</html>