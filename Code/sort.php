<?php
	$arr = array(4,75,48,5,45,77,1,-2);
	$size = count($arr);
	for($i=0;$i<=$size-1;$i++)
	{
		for($j=$i+1;$j<=$size-1;$j++)
		{
			if($arr[$j]<$arr[$i])
			{
				$temp = $arr[$j];
				$arr[$j] = $arr[$i];
				$arr[$i] = $temp;
			}
		}
		print $arr[$i]."\n";
	}
?>