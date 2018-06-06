<?php
	include("connect.php");
	if(isset($_POST['id']))
	{	
		if(is_numeric($_POST['id']))
		{
		$id=$_POST['id'];
		$name = mysqli_real_escape_string($connect,htmlspecialchars($_POST['name']));
		$email = mysqli_real_escape_string($connect,htmlspecialchars($_POST['email']));
		$contact = mysqli_real_escape_string($connect,htmlspecialchars($_POST['contact']));
		$pname = mysqli_real_escape_string($connect,htmlspecialchars($_POST['pname']));
		$quantity = mysqli_real_escape_string($connect,htmlspecialchars($_POST['quantity']));
		$cname = mysqli_real_escape_string($connect,htmlspecialchars($_POST['cname']));
		$sname = mysqli_real_escape_string($connect,htmlspecialchars($_POST['sname']));
		$cityname = mysqli_real_escape_string($connect,htmlspecialchars($_POST['cityname']));
		
		if($name== '' || $email == '' || $contact == '' || $pname == '' || $quantity == '' || $cname == '' || $sname == '' || $cityname == '')
		{
			$error = 'ERROR: Please fill in all  fields!';
		}
		else
		{
			$result = mysqli_query($connect,"UPDATE product SET name='$name', email='$email' ,contact='$contact' , pname='$pname' , quantity='$quantity', 
									cname='$cname' , sname='$sname' , cityname = '$cityname' WHERE id= '$id' ") or die(mysqli_error());
			echo $result;
		}
		}
	}
?>