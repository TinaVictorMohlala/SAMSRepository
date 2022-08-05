<?php
	session_start();
	unset($_SESSION['signedin']);
	
	if(session_destroy())
	{
		header("Location: ../Admin/index.php");
	}
?>