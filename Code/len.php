<html>
<head></head>
<body>
<form method = "POST" action = "">
enter a string = <input type = "text" id = "str" name = "str"></input>
<input type = "submit" id = "sub" name = "sub"></input>
</form>
</body>
</html>
<?php
if(isset($_POST['sub']))
{
	$strn = $_POST['str'];
	$len = strlen($strn);
	print_r($len);
}
?>