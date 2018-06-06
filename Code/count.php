<?php
	$strn = "shubhanshi singh";
	$count = strlen($strn);
	$ch = 0;
	for($i=0;$i<$count;$i++)
	{
		if($strn[$i]!=" ")
		{
			$ch++;
		}
	}
	print($ch);
?>