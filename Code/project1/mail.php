<?php
	include("connect.php");
	if(isset($_POST['id']))
	{
		$id=$_POST['id'];
		$name=$_POST['namae'];
		$email=$_POST['email'];
		$contact=$_POST['contact'];
		$pname=$_POST['pname'];
		$quantity=$_POST['quantity'];
		$country=$_POST['country'];
		$state=$_POST['state'];
		$city=$_POST['city'];
		if($name=='' || $email == '' || $contact == "" || $pname == '' || $quantity == '' || $country == '' || $state == '' || $city == '')
		{
			$error = 'ERROR: Please fill in all  fields!';
		}
		else
			mysqli_query($connect,"UPDATE product SET name='$name', email='$email' ,contact='$contact' , pname='$pname' , quantity='$quantity', 
									cname='$cname' , sname='$sname' , cityname = '$cityname' WHERE id= $id");

?>
	
