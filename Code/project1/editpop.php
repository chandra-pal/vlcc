<?php
	$dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
	$dbname="test";
	$connect = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname) 
	or die ('MySQL connect failed. ' . mysqli_error());
	
	mysqli_select_db($connect,$dbname) or die('Cannot select database. ' . mysqli_error());
	$result = mysqli_query($connect,"SELECT * FROM product")
	or die(mysqli_error());
	$arr = array();
	if(isset($_POST["id"]))
	{
		$id=$_POST['id'];
		$run_query=mysqli_query($connect,"SELECT * from product where id= '$id' ");
		$row=mysqli_fetch_array($run_query);
		$count=mysqli_num_rows($run_query);
		$arr['count'] = $count;
		$arr['data'] = $row;
		echo json_encode($arr);
	}
?>
