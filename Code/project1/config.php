<?php
   
   include("connect.php");
      
		 
	$name = mysqli_real_escape_string($connect,htmlspecialchars($_POST['name']));
	$email = mysqli_real_escape_string($connect,htmlspecialchars($_POST['email']));
	$contact= mysqli_real_escape_string($connect,htmlspecialchars($_POST['contact']));
	$pname= mysqli_real_escape_string($connect,htmlspecialchars($_POST['pname']));
	$quantity= mysqli_real_escape_string($connect,htmlspecialchars($_POST['quantity']));
	$cname= mysqli_real_escape_string($connect,htmlspecialchars($_POST['cname']));
	$sname= mysqli_real_escape_string($connect,htmlspecialchars($_POST['sname']));
	$cityname= mysqli_real_escape_string($connect,htmlspecialchars($_POST['cityname']));
		 
	if ($name == '' || $email == '' || $contact == '' || $pname == '' || $quantity == '' || $cname == '' || $sname == '' || $cityname == '')
	{
		$error = 'ERROR: Please fill in all required fields!';
		echo $error;
	}
	else
	{	
		$result = mysqli_query($connect,"INSERT INTO product(name,email,contact,pname,quantity,cname,sname,cityname)
		VALUES('$name','$email','$contact','$pname','$quantity','$cname','$sname','$cityname')") 
		or die(mysqli_error());
		$id = mysqli_insert_id($connect);
		echo $id;
	}
?>