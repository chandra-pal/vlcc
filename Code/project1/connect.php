<?php 
    $dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
	$dbname="test";
	
    $connect = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname)or die ('MySQL connect failed. ' . mysqli_error());
	 mysqli_select_db($connect,$dbname) or die('Cannot select database. ' . mysqli_error());
?>