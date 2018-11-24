<?php
include('classes/DB.php');
if (isset($_POST['register'])) {
	$username = $_POST['username'];
  $password = $_POST['password'];
	$email = $_POST['email'];
	$usernameError ="";
	$emailError ="";
  $passwordError="";
  // header("Location: home.php");
  // echo $username;
  // echo "<br>";
  // echo $password;
  // echo "<br>";
  // echo $email;
  // echo "<br>";

	if (!DB::query('SELECT email FROM users WHERE email=:email', array(':email'=>$email))) {
		if(strlen($username) >= 3 && strlen($username) <= 32) {
			if(preg_match('/[a-zA-Z0-9_]+/', $username)) {
				if (strlen($password) >= 6 && strlen($password) <= 60 ) {
					if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
						DB::query('INSERT INTO users VALUES (null, :username, :password, :email)', array(':username'=>$username, ':password'=>password_hash($password, PASSWORD_BCRYPT), ':email'=>$email));
						header('Location: login.php');
					} else {
						$emailError = "Invalid email";
					}
				} else {
					$passwordError = "Invalid password, min 6 characters";
				}
			} else {
				$usernameError = "Invalid username, numbers and letters only";
			}
		} else {
			$usernameError = "Invalid username, 3-32 characters";
		}
  } 
  else {
    $emailError = "Email already exist";
  }
  
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy"
		crossorigin="anonymous">
		<link rel="stylesheet" href="main.css">
	<title>Register</title>
  <style>
    .myregister p {
      font-size: 32px;
		}
		.myregisterheader img:hover {
			cursor: pointer;
		}
  </style>
</head>

<body>
<div class="myregister">
    <div class="row mt-5 justify-content-center myregisterheader">
      <div class="col-6 text-center">
        <img src="img/planless.svg" width="80" height="80" alt="" id="logo">
        <p class="lead">Register to SmashFeed</p>
      </div>
    </div>
    <div class="row justify-content-center myregisterform">
      <div class="col-6">
        <div class="card mt-3">
          <div class="card-body">
            <form action="register.php" method="post" id="validateRegister" novalidate>
              <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" name="username" placeholder="Enter Username">
                <small class="text-danger"><?php echo $usernameError;?></small>
              </div>
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
              <button type="submit" class="btn btn-primary btn-block mt-3" name="register" value="register">REGISTER</button>
            </form>
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
      $("#logo").click(function () {
        window.location.href = "http://localhost/monosmash/register.php";
      });

    });
  </script>
</body>
</html>