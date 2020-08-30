<?php
session_start();
require_once "pdo.php";
require_once 'util.php';

if (isset($_POST['email']) and isset($_POST['password']) and isset($_POST['username']) and isset($_POST['phone-number']) and isset($_FILES['image']))
{
	if (strlen($_POST['email']) < 1 or strlen($_POST['password']) < 1 or strlen($_POST['username']) < 1 or strlen($_POST['phone-number']) < 1)
	{
		$_SESSION['error'] = "All fields are required";
		header('Location: signup.php');
		return;
	}
	else
	{
		$target_dir = "uploads/";
		$target_file = $target_dir . basename($_FILES["image"]["name"]);
		$uploadOk = 1;
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		if ($_FILES["image"]["size"] > 5000000) 
		{
			$_SESSION['error'] = "Sorry, your file is too large.";
			$uploadOk = 0;
			header('Location: signup.php');
			return;
		}
		if(!($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" ) )
		{
			$_SESSION['error'] = "Sorry, only JPG, JPEG, & PNG files are allowed.";
			$uploadOk = 0;
			header('Location: signup.php');
			return;
		}
		
		$email = $_POST['email'];
		$pass = hash('md5', $_POST['password']);
		$name = $_POST['username'];
		$phone = $_POST['phone-number'];
		
		$stmt = $pdo->prepare('select * from user_info where email= :email');
		$stmt->execute(array(':email' => $_POST['email']));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row !== false)
			{
				$_SESSION['error'] = "Email Already Exists!";
				header('Location: signup.php');
				return;
			}
        else {
			unset($_SESSION['name']);
			$_SESSION['name'] = $row['name'];
    		$_SESSION['user_id'] = $row['user_id'];
			$stmt = $pdo->prepare('insert into user_info(name,email,phone,password) value(:name,:email,:phone,:pass)');
			$stmt->execute(array(':email' => $email, ':name' => $name, ':pass' => $pass, ':phone' => $phone));
			$profile_id = $pdo->lastInsertId();
			$imgloc = "uploads/$profile_id.$imageFileType";
			
			if (move_uploaded_file($_FILES["image"]["tmp_name"], $imgloc) and $uploadOk == 1) 
			{
				$_SESSION['success'] =  "The file ". basename( $_FILES["image"]["name"]). " has been uploaded. Please Log In!";
				$stmt = $pdo->prepare('update user_info set face_id = :fid where user_id = :uid');
				$stmt->execute(array(':uid' => $profile_id , ':fid' => $imgloc));
				header('Location: login.php');
				return;
			} 
			else 
			{
				$_SESSION['error'] = "Sorry, there was an error uploading your file.";
				header('Location: signup.php');
				return;
			}
			header("Location: signup.php");
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
	<h1 align="center">Sign Up!</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-icon">
                <span><i class="icon icon-user"></i></span>
            </div>
			<?php flashmessage(); ?>
            <div class="form-group">
                <input type="text" class="form-control item" id="username" name="username" placeholder="Name" required>
            </div>
            <div class="form-group">
                <input type="password" class="form-control item" id="password" name="password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <input type="email" class="form-control item" id="email"  name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control item" id="phone-number" name="phone-number" placeholder="Phone Number" required>
            </div>
			<div class="form-group">
                <input type="file" class="form-control item" id="image" name="image" required>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-block create-account" value="Sign Up!" />
            </div>
        </form>
        <div class="social-media">
            <h5><a href="login.php"> Already Registered?</a></h5>
            <div class="social-icons">
            </div>
        </div>
    </div>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
    <script src="app1.js"></script>
</body>
</html>
