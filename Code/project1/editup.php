<?php
	include("connect.php");
	$arr = array();
	if(isset($_POST["id"]))
	{
		$id=$_POST['id'];
		
		$run_query=mysqli_query($connect,"SELECT * from product where id= '$id' ");
		$count=mysqli_num_rows($run_query);
		$row=mysqli_fetch_array($run_query);
		$arr['count'] = $count;
		$arr['data'] = $row;
		echo json_encode($arr);
	}
?>
