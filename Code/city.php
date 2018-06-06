<?php
$dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
	$dbname="test";
	$connect = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname);
	
	mysqli_select_db($dbname,$connect);
	/*if(is_numeric($_POST['id']))
	{
		$id=$_POST['id'];
		
		$stmt = mysqli_query("SELECT * FROM test.city WHERE idstate=$id");
	
		?>
		<option selected="selected">Select City :</option>
		<?php while($row=mysqli_fetch_array($stmt))
	{
		?>
		<option value="<?php echo $row['idcity']; ?>"><?php echo $row['cityname']; ?></option>
		<?php
	}
}*/
if(isset($_POST["idstate"]))
{ 
	$state_id= $_POST['idstate'];     //Get all city data    
	$query = "SELECT * FROM city WHERE idstate = '$idstate' ORDER BY cityname ASC";     
	$run_query = mysqli_query($connect, $query);     //Count total number of rows     
	$count = mysqli_num_rows($run_query);          //Display cities list     
	if($count > 0)
	{         
		echo 'Select city';         
		while($row = mysqli_fetch_array($run_query))
		{
			$city_id=$row['idcity']; 
			$city_name=$row['cityname'];        
			echo "$city_name";        
		}     
	}
	else
	{  
		echo 'City not available';    
	} 
}

?>