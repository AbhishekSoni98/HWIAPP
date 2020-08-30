<?php
session_start();
require_once "pdo.php";
require_once 'util.php';
if (!(isset($_SESSION['name']) && isset($_SESSION['user_id']) ))
{
	die("ACCESS DENIED");
}
if (!isset($_SESSION['pid']))
{
	$stmt = $pdo->prepare('insert into prescription(user_id) values(:uid)');
	$stmt->execute(array(':uid' => $_SESSION['user_id']));
	$pid = $pdo->lastInsertId();
	$_SESSION['pid'] = $pid;
}
if (isset($_POST['add']) and isset($_POST['medicine']))
{
	$stmt = $pdo->prepare('select * from medicine where Name=:name');
	$stmt->execute(array('name' => $_POST['medicine']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($row === false)
	{
		$_SESSION['error'] = "Medicine  not in our Inventory";
		header('Location: add.php');
		return;
	}
	$mid = $row['ID'];
	$type = $row['Type'];
	$stmt = $pdo->prepare('insert into prescription_data(PID,Medicine_id,Type) values(:pid,:mid,:type)');
	$stmt->execute(array(':pid' => $_SESSION['pid'],':mid' => $mid, ':type' => $type ));
	$_SESSION['success'] = "Medicine Added";
	header('Location: add.php');
	return;
}

if (isset($_POST['submit']) and isset($_POST['medicine']))
{
	$stmt = $pdo->prepare('select * from medicine where Name=:name');
	$stmt->execute(array('name' => $_POST['medicine']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($row === false)
	{
		$_SESSION['error'] = "Medicine  not in our Inventory";
		header('Location: add.php');
		return;
	}
	$mid = $row['ID'];
	$type = $row['Type'];
	$stmt = $pdo->prepare('insert into prescription_data(PID,Medicine_id,Type) values(:pid,:mid,:type)');
	$stmt->execute(array(':pid' => $_SESSION['pid'],':mid' => $mid, ':type' => $type ));
	$OK = true;
	$stmt = $pdo->prepare('select * from prescription_data where PID = :pid');
	$stmt->execute(array(':pid' => $_SESSION['pid']));
	while($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		if ($row['Type'] === "Human prescription drug")
		{
			$OK = false;
			break;
		}
	}
	if ($OK === true)
	{
		$conf = '1.0';
		$status = 'Approved';
		$pid = $_SESSION['pid'];
		$stmt = $pdo->prepare('update prescription set status=:st,conf=:conf where ID=:pid');
		$stmt->execute(array(':pid' => $pid, ':st' => $status,':conf' => $conf));
		$_SESSION['success'] = 'All OTC Drugs. Prescription Confirmed';
		unset($_SESSION['pid']);
		header('Location: index.php');
		return;
	}
	header('Location: verify.php');
	return;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pharmacy</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.4.1/css/simple-line-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="login.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="index.php">Pharmacy</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item active">
        <a class="nav-link disabled" href="index.php">Home <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link disabled" href="#">Appointments</a>
      </li>
	  <?php
	  //var_dump($_SESSION);
	  if (isset($_SESSION['name']) && isset($_SESSION['user_id']) )
	  {
		echo '<li class="nav-item">';
		echo '<a class="nav-link" href="#">'.htmlentities($_SESSION['name']).'</a>';
		echo '</li>';
		echo '<li class="nav-item">';
		echo '<a class="nav-link" href="logout.php">(LogOut)</a>';
		echo '</li>';
	  }
	  else
	  {
		echo '<li class="nav-item">';
		echo '<a class="nav-link" href="signup.php">SignUp</a>';
		echo '</li>';
		echo '<li class="nav-item">';
		echo '<a class="nav-link" href="login.php">LogIn</a>';
		echo '</li>';
	  }
	  ?>

    </ul>
  </div>
</nav>
<br>
<?php flashmessage(); ?>
<br>
<div class="col-sm-8">
		<form method="POST">
			<?php flashmessage(); ?>
            <div class="form-group">
               <input type="text" class="form-control item" id="medicine" name="medicine" autocomplete="off" placeholder="Medicine Name" required>
            </div>
			<div class="form-group">
				<input type="submit"  style="width: 30%" name= "add" value="Add More" />
			</div>
            <div class="form-group">
                <input type="submit"  style="width: 30%" name="submit" value="Submit" />
            </div>
        </form>
</div>

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
	<script
  src="https://code.jquery.com/jquery-3.2.1.js"
  integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
  crossorigin="anonymous"></script>

<script
  src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
  integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="
  crossorigin="anonymous"></script>
	<script>
	$('#medicine').autocomplete({
    source: "medicine.php"
});
	</script>
</body>
</html>
