<?php

   /*
    $dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
	$dbname="test"*/
    $connect=mysqli_connect("localhost","root","","test");
		 $name = mysqli_real_escape_string($connect,$_REQUEST['name']);
		 $email = mysqli_real_escape_string($connect,$_REQUEST['email']);
		 $contact = mysqli_real_escape_string($connect,$_REQUEST['contact']);
		 $pname = mysqli_real_escape_string($connect,$_REQUEST['pname']);
		 $Quantity = mysqli_real_escape_string($connect,$_REQUEST['Quantity']);
		 
		 if($email == mysql_query("SELECT email from product"))
		{
			$sql = "UPDATE product SET pname = '$pname',Quantity = '$Quantity' WHERE email='$email'";

		if ($connect->query($sql) === TRUE) 
		{
		echo "Record updated successfully";
		} 
		else 
		{
		echo "Error updating record: " . $connect->error;
		}
		}

	    mysqli_close($connect);
/*
            $result = mysql_query("select * from product WHERE `Personid` = '".$rid."';");

            if (!$result) 
			{
                die('Query failed: ' . mysql_error());
            }
            $i = 0;
            while ($i < mysql_num_fields($result)) {
                $meta = mysql_fetch_field($result, $i);
                if (!$meta) {
                    echo "ERROR";
                }
                $name = $meta->name;

                $r = mysql_fetch_array(mysql_query("select * from contactinfo WHERE `id` = '".$rid."';"));
                $content = $r[$name];

                if($name != 'id') {
                    echo "<tr><td align='center'><div align='left'>Edit ".$name."</div></td></tr>";
                    echo "<tr><td align='center'><input type='text' value='" . $content . "' /></td></tr>";
                }
                $i++;
            }
            mysql_free_result($result);
   */
   ?>

