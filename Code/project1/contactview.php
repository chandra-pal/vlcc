<?php
	include("connect.php");
	$arr = array();
	if(isset($_POST["contact"]))
	{	
		$contact = $_POST["contact"];
			
		$run_query=mysqli_query($connect,"SELECT product.* , country.cname as country, state.sname as state, city.cityname as city FROM product 
		left join country on country.idcountry=product.cname left join state on state.idstate=product.sname
		left join city on city.idcity=product.cityname where contact='$contact' ");
		$count = mysqli_num_rows($run_query);
		$row = mysqli_fetch_array($run_query);
		$arr['count'] = $count;
		$arr['data'] = $row;
		echo json_encode($arr);
		
	}
?>