<?php
  include("connect.php");
?>
 
<!DOCTYPE html>
<html>
<head>
    <title>Search results</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css"/>
</head>
<body>
<?php
	 echo "<style>
					#customers 
					{
						font-family: 'Trebuchet MS', Arial, Helvetica, sans-serif;
						border-collapse: collapse;
						width: 100%;
					}

					#customers td, #customers th 
					{
						border: 1px solid #ddd;
						padding: 8px;
						border-bottom-left-radius: 25px;
					}
	
					#customers tr:nth-child(even)
					{
						background-color: #f2f2f2;
						border-bottom-left-radius: 25px;
					}

					#customers tr:hover 
					{
						background-color: #ddd;
					}

					#customers th 
					{
						padding-top: 12px;
						padding-bottom: 12px;
						text-align: left;
						background-color: #4CAF50;
						color: white;
					}
					</style>";
				echo "<body bgcolor='#F5F5DC'>";
    $query = $_GET['query']; 
     
    $min_length =3;

    if(strlen($query) >= $min_length)
	{
          
  
        $query = mysqli_real_escape_string($connect,htmlspecialchars($query));
        
         
        $raw_results = mysqli_query($connect,"SELECT * FROM product
            WHERE (`name` LIKE '%".$query."%') OR (`contact` LIKE '%".$query."%') OR (`pname` LIKE '%".$query."%') OR (`email` LIKE '%".$query."%')")  or die(mysqli_error());
             
           
        if(mysqli_num_rows($raw_results) > 0)
		{ 	
			
				echo "<table border='1' cellpadding='10' align='center' id='customers'>";
				echo "<tr>
				<th>Id</font></th>
				<th>Name</font></th>
				<th>Email ID</font></th>
				<th>Contact</font></th>
				<th>Product Name</font></th>
				<th>Quantity</font></th>
				<th>Edit</font></th>
				<th>Delete</font></th>
				</tr>";
            while($results = mysqli_fetch_array($raw_results))
			{
            
					echo "<tr>";
					echo '<td><b><font color="#663300">' . $results['id'] . '</font></b></td>';
					echo '<td><b><font color="#663300">' . $results['name'] . '</font></b></td>';
					echo '<td><b><font color="#663300">' . $results['email'] . '</font></b></td>';
					echo '<td><b><font color="#663300">' . $results['contact'] . '</font></b></td>';
					echo '<td><b><font color="#663300">' . $results['pname'] . '</font></b></td>';
					echo '<td><b><font color="#663300">' . $results['quantity'] . '</font></b></td>';
					echo '<td><b><font color="#663300"> <a href="editdet.php?id=' . $results['id'] . '">Edit</a></font></b></td>';
					echo '<td><b><font color="#663300"> <a href="del.php?id=' . $results['id'] . '">Delete</a></font></b></td>';
					echo "</tr>";

            }
             echo "</table><br><BR>";
        }
        else
		{ 
            echo "No results";
        }
         
    }
    else
	{ 
        echo "Minimum length is ".$min_length;
    }
?>
</body>
</html>