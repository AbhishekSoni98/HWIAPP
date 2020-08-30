<?php
function flashmessage()
{
	if (isset($_SESSION['success']))
	{
		echo '<div class="alert alert-success">'.htmlentities($_SESSION['success']).'</div>';
		unset($_SESSION['success']);
		return;
	}
	else if (isset($_SESSION['error']))
	{
		echo '<div class="alert alert-danger">'.htmlentities($_SESSION['error']).'</div>';
		unset($_SESSION['error']);
		return;
	}
}
?>