<html>
<head></head>
<body>
<form method = "POST" action = "">
First Name:<input type = "text" id = "strn1" name = "strn1"></input>
Last Name:<input type = "text" id = "strn2" name = "strn2"></input>
<input type = "submit" id = "sub" name = "sub">
</form>
</body>
</html>
<?php
if(isset($_POST['sub']))
{
	$str1 = $_POST['strn1'];
	$str2 = $_POST['strn2'];
	$concat = $str1."\n".$str2;
	echo $concat;
}
?>