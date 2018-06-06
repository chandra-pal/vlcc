<?php
include("connect.php");
//Include database configuration file

if(isset($_POST["idcountry"]))
{	
    //Get all state data
		$idcountry= $_POST['idcountry'];
     
		$run_query = mysqli_query($connect, "SELECT * FROM state WHERE idcountry = '$idcountry' ORDER BY sname ASC");
		$count = mysqli_num_rows($run_query);
		if($count>0)
		{
		 echo '<option value="">Select state</option>';
        while($row = mysqli_fetch_array($run_query))
		{
			$idstate=$row['idstate'];
			$sname=$row['sname'];
			echo "<option value='$idstate'>$sname</option>";
        }
		}
		else
		{
			echo '<option value="">State not available</option>';
		}
}

if(isset($_POST["idstate"]))
{
	$idstate= $_POST['idstate'];
    //Get all city data
	$run_query = mysqli_query($connect,"SELECT * FROM city WHERE idstate = '$idstate' ORDER BY cityname ASC");
		$count= mysqli_num_rows($run_query);
		if($count>0)
		{
        echo '<option value="">Select city</option>';
        while($row = mysqli_fetch_array($run_query))
		{
			$idcity=$row['idcity'];
			$cityname=$row['cityname']; 
			echo "<option value='$idcity'>$cityname</option>";
        
		}
		}
	else
	{
        echo '<option value="">City not available</option>';
    }
}
?>