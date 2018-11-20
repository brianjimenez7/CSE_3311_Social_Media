<?php
include('classes/DB.php');
if (isset($_GET["set"])) {
  $set = (int)$_GET["set"];
}
if (isset($_POST['login'])) {
	$email = $_POST['email'];
	$password = $_POST['password'];
	$emailError ="";
  $passwordError="";
  // echo $email;
  // echo "<br>";
  // echo $password;
	if (DB::query('SELECT email FROM users WHERE email=:email', array(':email' => $email))) {
		if (password_verify($password, DB::query('SELECT password FROM users WHERE email=:email', array(':email' => $email))[0]['password'])) {
			$cstrong = true;
      $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
      $user_id = DB::query('SELECT id FROM users WHERE email=:email', array(':email' => $email))[0]['id'];
      DB::query('INSERT INTO login_tokens VALUES (null, :token, :user_id)', array(':token' => sha1($token), ':user_id' => $user_id));
      //echo "here passed";
      setcookie("SNID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);
			setcookie("SNID_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);
			header('Location: home.php');
    } 
    else {
			$passwordError = "Incorrect Password";
		}
  } 
  else {
		$emailError = "User does not exist";
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
    .myloginheader p {
      font-size: 32px;
    }
    .myloginheader img:hover {
			cursor: pointer;
    }
    .myloginform button:hover {
			cursor: pointer;
		}
  </style>
</head>

<body>
  <div class="mylogin">
    <?php if ($set==1){ ?>
      <div class="row mt-1 justify-content-center">
        <div class="alert alert-danger" role="alert">
         Log In to see your feed
        </div>
      </div>
    <?php } ?>
  
    <div class="row mt-5 justify-content-center myloginheader">
      <div class="col-6 text-center">
        <img src="img/planless.svg" width="80" height="80" alt="" id="logo">
        <p class="lead">Sign in to MonoSmash</p>
      </div>
    </div>
    <div class="row justify-content-center myloginform">
      <div class="col-6 ">
        <div class="card mt-3">
          <div class="card-body">
            <form action="login.php" method="post">
              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" placeholder="Enter email">
                <small class="text-danger"><?php echo $emailError;?></small>
              </div>
              <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Enter Password">
                <small class="text-danger"><?php echo $passwordError;?></small>
              </div>
              <button type="submit" class="btn btn-primary btn-block mt-3" name="login" value="login">LOGIN</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="row mt-3 justify-content-center myloginfooter">
      <div class="col-6 text-center">
        <div class="card">
          <a href="#">I FORGOT MY PASSWORD</a>
          <a href="register.php">NEW HERE ? REGISTER</a>
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
      $("#logo").click(function () {
        window.location.href = "http://localhost/monosmash/main.php";
      });

    });
  </script>
</body>

</html>
