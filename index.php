<?php
session_start();
require_once "pdo.php";
require_once 'util.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Slytherins Pharmacy</title>
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
  <a class="navbar-brand" href="#">Pharmacy</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item active">
        <a class="nav-link disabled" href="#">Home <span class="sr-only">(current)</span></a>
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
<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel" ">
  <ol class="carousel-indicators">
    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
  </ol>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="AIP3.jpg" class="d-block w-100" style="height: 20em; alt="...">
    </div>
    <div class="carousel-item">
      <img src="AIP.jpg" class="d-block w-100" style="height: 20em; alt="...">
    </div>
    <div class="carousel-item">
      <img src="AIP2.jpg" class="d-block w-100" style="height: 20em; alt="...">
    </div>
  </div>
  <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>
<br>
<div style="margin-left: 20px !important;">
<button><a href="add.php">+ Add Prescription</a></button>
</div>
<br>
<?php flashmessage(); ?>
<div class="col-sm-10" allign="center">
<br>
<table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">#</th>
      <th scope="col">Prescription ID</th>
      <th scope="col">Status</th>
      <th scope="col">Actions</th>
    </tr>
  </thead>
  <?php
  if (isset($_SESSION['name']) && isset($_SESSION['user_id']) )
  {
	$stmt = $pdo->prepare('select * from prescription where user_id= :uid');
	$stmt->execute(array(':uid' => $_SESSION['user_id']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$sr = 1;
	if ($row !== false)
	{
		$id = $row['ID'];
		$status = $row['status'];
		$class = 'light';
		if ($status === 'Approved')
		{ $class = 'success'; }
		else if ($status === 'Rejected')
		{ $class = 'danger'; }
		echo '<tbody><tr class="table-'.$class.'"><th>'.$sr.'</th><th>PID-20200'.$id.'</th><th>'.$status.'</th>';
		if ($status === 'Approved')
		{ echo ' <th><a href="view.php?id='.$id.'">Details</a></th></tr></tbody>'; }
		else if ($status === 'Rejected')
		{ echo ' <th><a href="update.php?id='.$id.'">Update</a></th></tr></tbody>'; }
		else
		{
			echo ' <th><a href="#">Edit</a></th></tr></tbody>';
		}
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$sr += 1;
			$id = $row['ID'];
		$status = $row['status'];
		$class = 'light';
		if ($status === 'Approved')
		{ $class = 'success'; }
		else if ($status === 'Rejected')
		{ $class = 'danger'; }
		echo '<tbody><tr class="table-'.$class.'"><th>'.$sr.'</th><th>PID-20200'.$id.'</th><th>'.$status.'</th>';
		if ($status === 'Approved')
		{ echo ' <th><a href="view.php?id='.$id.'">Details</a></th></tr></tbody>'; }
		else if ($status === 'Rejected')
		{ echo ' <th><a href="verify.php?id='.$id.'">Verify</a></th></tr></tbody>'; }
		else
		{
			echo ' <th><a href="#">Edit</a></th></tr></tbody>';
		}
		}
	}
	else
	{
		echo '<tbody><tr class="table-active"><th colspan=4>No Prescriptions found!</th></tr></tbody>';
	}
  }
  ?>
<!--
  <tbody>
    <tr class="table-danger">
      <th scope="row">1</th>
      <td>Mark</td>
      <td>Otto</td>
      <td>@mdo</td>
    </tr>
    <tr>
      <th scope="row">2</th>
      <td>Jacob</td>
      <td>Thornton</td>
      <td>@fat</td>
    </tr>
    <tr>
      <th scope="row">3</th>
      <td>Larry</td>
      <td>the Bird</td>
      <td>@twitter</td>
    </tr>
  </tbody>
-->
</table>
</div>

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
</body>
</html>
