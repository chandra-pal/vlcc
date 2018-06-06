<?php
	include("connect.php");
	$arr = array();
	if(isset($_POST['contact']))
	{	
		$contact = $_POST["contact"];
			
		$run_query=mysqli_query($connect,"SELECT * from product where contact='$contact' LIMIT 1");
		$count = mysqli_num_rows($run_query);
		$row = mysqli_fetch_array($run_query);
		$arr['count'] = $count;
		$arr['data'] = $row;
		echo json_encode($arr);
		
	}
?>