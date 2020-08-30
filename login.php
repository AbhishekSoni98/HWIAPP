<?php
session_start();
require_once "pdo.php";
require_once 'util.php';
if (isset($_POST['email']) and isset($_POST['password']))
{
	if (strlen($_POST['email']) < 1 or strlen($_POST['password']) < 1)
	{
		$_SESSION['error'] = "User name and password are required";
		header('Location: login.php');
		return;
	}
	else
	{
		$email = $_POST['email'];
		$check = hash('md5', $_POST['password']);
		$stmt = $pdo->prepare('select * from user_info where email= :email');
		$stmt->execute(array(':email' => $_POST['email']));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row !== false)
			{
				$stored_hash = $row['password'];
			}
			else{
				$stored_hash = "";
			}
        if ( $check == $stored_hash ) {
			unset($_SESSION['Name']);
			$_SESSION['name'] = $row['Name'];
    		$_SESSION['user_id'] = $row['user_id'];
			$_SESSION['user_type'] = $row['user_type'];
			header("Location: index.php");
            return;
        } else {
			$_SESSION['error'] = "Incorrect password";
			header('Location: login.php');
			return;
        }
	}
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Slytherins Pharmacy</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.4.1/css/simple-line-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="login.css">
</head>
<body>

    <div class="registration-form">
	<h1 align="center">Log In!</h1>
        <form method="POST">
            <div class="form-icon">
                <span><i class="icon icon-login"></i></span>
            </div>
			<?php flashmessage(); ?>
            <div class="form-group">
                <input type="email" class="form-control item" id="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="password" class="form-control item" id="password" name="password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-block create-account" value="Log In!" />
            </div>
        </form>
        <div class="social-media">
            <h5><a href="signup.php"> Not Registered?</a></h5>
            <div class="social-icons">
            </div>
        </div>
    </div>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
    <script src="app1.js"></script>
</body>
</html>
