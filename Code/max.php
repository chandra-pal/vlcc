<?php
	$arr = array(4,75,48,5,45,77,1,-2,89);
	$size = count($arr);
	$max = $arr[0];
	for($i=0;$i<=$size-1;$i++)
	{
		if($max<=$arr[$i])
		{
			$max = $arr[$i];
		}
	}
	print $max;
?>