<?php
	$dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
	$dbname="test";
	$connect = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname);
	
	mysqli_select_db($connect,$dbname);
	if(isset($_POST["email"])
	{
		$email=$_POST['email'];
		$run_query=mysqli_query($connect,"SELECT * from product where email=$email limit 1");
		$row=mysqli_fetch_array($run_query);
		if($row)
		{
			$arr= array("$row['name']","$row['email']","$row['contact']","","","$row['cname']","$row['sname']","$row['cityname']");
			echo $arr[0].$arr[1].$arr[2].$arr[3].$arr[4].$arr[5].$arr[6].$arr[7];
		}
	}
?>