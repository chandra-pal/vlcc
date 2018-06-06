<html>
<head>
</head>
<body>
<form method="GET" action="">
Enter a number to check odd or even<input type="number" id="val" name="val" ></input>
<input type="submit" id="done" name="done">
</form>
<?php
	if(isset($_GET['done']))
	{
		$num=$_GET['val'];
		if($num%2 == 0)
		{
			echo "Number is Even";
		}
		else
		{
			echo "Number is odd";
		}
	}
?>