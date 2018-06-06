<?php

	$arr = array(5,7,1,70,8,4,10,15,10,16,10);
	$size = count($arr);
	$key = array_keys($arr,10);
	$coun = count($key);
	print_r($key);
	echo "\r\n";
	for($i=0;$i<$coun;$i++)
	{	$k = $key[$i];
		unset($arr[$k]);
	}
	print_r($arr);
	
?>
