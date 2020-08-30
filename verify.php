<?php
session_start();
require_once "pdo.php";
require_once 'util.php';
if (!(isset($_SESSION['name']) && isset($_SESSION['user_id']) ))
{
	die("ACCESS DENIED");
}
if (isset($_GET['id']))
{
	$pid = $_GET['id'];
	$stmt = $pdo->prepare('select * from prescription where ID=:pid');
	$stmt->execute(array(':pid' => $pid));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	if ( $row !== false)
	{
		if ($row['user_id'] == $_SESSION['user_id'])
		{
			$rc = (int)$row['reject_count'];
			$rc += 1;
			if ($rc <= 3)
			{
			$stmt = $pdo->prepare('update prescription set reject_count=:rc where ID=:pid');
			$stmt->execute(array(':pid' => $pid,':rc' => $rc));
			$_SESSION['pid'] = $pid;
			}
			else
			{
				$_SESSION['error'] = "Maximum Rejections for PID-20200$pid please contact Administrators!";
				header('Location: index.php');
				return;
			}
		}
	}
}
if (!isset($_SESSION['pid']))
{
	die("ACCESS DENIED");
}

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
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
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
<div >


    <form method="POST" action="storeImage.php">

        <div >

            <div >

                <div id="my_camera"></div>

                <br/>
                
                <input type=button value="Take Snapshot" onClick="take_snapshot()">

                <input type="hidden" name="image" class="image-tag">

            </div>

            <div >

                <div id="results">Your captured image</div>

            </div>

            <div >

                <br/>

                <input type="submit" value="Submit" >

            </div>

        </div>
        </form>

</div>

  

<!-- Configure a few settings and attach camera -->

<script language="JavaScript">

    Webcam.set({

        width: 490,

        height: 390,

        image_format: 'jpeg',

        jpeg_quality: 90

    });

  

    Webcam.attach( '#my_camera' );
    
    function take_snapshot() {

        Webcam.snap( function(data_uri) {

            $(".image-tag").val(data_uri);

            document.getElementById('results').innerHTML = '<img src="'+data_uri+'"/>';

        } );

    }

</script>

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

</body>
</html>
