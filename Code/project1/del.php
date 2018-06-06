<?php
	include("connect.php");
	
	if (isset($_POST["id"]))
	{	
		$id = $_POST["id"];

		$result = mysqli_query($connect,"DELETE FROM product WHERE id= '$id' ")
		or die(mysqli_error());
		$result = mysqli_query($connect,"SET @num := 0")
		or die(mysqli_error());
		$result = mysqli_query($connect,"UPDATE product SET id = @num := (@num+1)")
		or die(mysqli_error());
		$result = mysqli_query($connect,"ALTER TABLE product AUTO_INCREMENT = 1")
		or die(mysqli_error());
		echo $result;
	}
	
?>

