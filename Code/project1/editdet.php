<?php
	include("connect.php");
	$result = mysqli_query($connect,"SELECT * FROM product")
	or die(mysqli_error($connect));
?>
<?php
	function valid($id, $name, $email, $contact, $pname, $quantity, $cname, $sname, $cityname, $error,$connect)
	{
?>
<html>
<head>
<title>Edit Records</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script type="text/javascript">
	$(document).ready(function()
{
    $('#country').change(function(){
        var countryID = $(this).val();
		//alert(countryID);
        if(countryID)
		{
            $.ajax(
			{
                type:'POST',
				url:'state.php',
                data:'idcountry='+countryID,
                success:function(html)
				{
                    $('#state').html(html);
                    $('#city').html('<option value="">Select state first</option>'); 
                }
            }); 
        }
		else
		{
            $('#state').html('<option value="">Select country first</option>');
            $('#city').html('<option value="">Select state first</option>'); 
        }
    });
    
    $('#state').change(function()
	{
        var stateID = $(this).val();
		//alert(stateID);
        if(stateID)
		{
            $.ajax(
			{
                type:'POST',
				url:'state.php',
				data:'idstate='+stateID,
                success:function(html)
				{
                    $('#city').html(html);
                }
            }); 
        }
		else
		{
            $('#city').html('<option value="">Select state first</option>'); 
        }
    });	
});
</script>
	<style>
	#customers 
		{
			font-family: Trebuchet MS, Arial, Helvetica, sans-serif;
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
	</style>
</head>
<body style="background-color: 	#F5F5DC">
	<?php
		if ($error != '')
		{
			echo '<div style="padding:4px; border:1px solid red;color:red;">'.$error.'</div>';
		}
	?>

	<form id="myform" action="" method="post" align="center" name="myform" >
	<input type="hidden" name="id" value="<?php echo $id; ?>"/>

	<table border="1" align="center" id="customers">
		<tr>
		<th colspan="2"><b><font color='white'>Edit Records </font></b></th>
		</tr>
		<tr>
		<td width="179"><b><font color='#663300'>Name<em>*</em></font></b></td>
		<td><label>
		<input type="text" name="name" id="name" value="<?php echo $name; ?>"><span id="name_error"></span>
		</input>
		</label></td>
		</tr>

		<tr>
		<td width="179"><b><font color='#663300'>Email<em>*</em></font></b></td>
		<td><label>
		<input type="text" name="email" value="<?php echo $email; ?>"><span id="email_error"></span>
		</input>
		</label></td>
		</tr>

		<tr>
		<td width="179"><b><font color='#663300'>Contact<em>*</em></font></b></td>
		<td><label>
		<input type="text" name="contact" maxlength="10" value="<?php echo $contact; ?>"><span id="contact_error"></span>
		</input>
		</label></td>
		</tr>

		<tr>
		<td width="179"><b><font color='#663300'>Product Name<em>*</em></font></b></td>
		<td><label>
		<input type="text" name="pname" value="<?php echo $pname; ?>" />
		</label></td>
		</tr>
		
		<tr>
		<td width="179"><b><font color='#663300'>Quantity<em>*</em></font></b></td>
		<td><label>
		<input type="text" name="quantity" value="<?php  echo $quantity; ?>" />
		</label></td>
		</tr>
		
		<tr>
		<td width="179"><b><font color='#663300'>Country<em>*</em></font></b></td>
		<td><label> 
		 <select name= "cname" id="country" >
		 <?php
			$run_query = mysqli_query($connect,"SELECT * FROM country  ORDER BY cname ASC");
			$count = mysqli_num_rows($run_query);
			
		?>
		 <option value="">Select Country</option>
     
        <?php
        if($count > 0)
		{
            while($row = mysqli_fetch_array($run_query))
			{ ?>
               <option value="<?php echo $row['idcountry'] ?>" <?php if($row['idcountry']==$cname) {echo 'selected';} ?>> <?php echo$row['cname'] ?></option>
          <?php  }
        }
		else
		{
            echo '<option value="">Country not available</option>';
        }
        ?>
		</select>
		</label></td>
		</tr>
		
		<tr>
		<td width="179"><b><font color='#663300'>State<em>*</em></font></b></td>
		<td><label>
		<select name="sname" id="state" >
		<?php 
			$run_query = mysqli_query($connect, "SELECT * from state where idcountry=$cname ORDER BY sname ASC");
			$count = mysqli_num_rows($run_query);
		?>
		<?php
		if($count>0)
		{
			while($row = mysqli_fetch_array($run_query))
			{	?>
				<option value="<?php echo $row['sname'] ?>"<?php if($row['idstate']==$sname) {echo 'selected';}?>> <?php echo $row['sname'] ?></option>
			<?php	
			}
		}?>
		</select>
		</label></td>
		</tr>
		
		<tr>
		<td width="179"><b><font color='#663300'>City<em>*</em></font></b></td>
		<td><label>
		<select name="cityname" id="city" >
		<?php
			$run_query=mysqli_query($connect,"SELECT * from city where idstate=$sname ORDER BY cityname ASC");
			$count= mysqli_num_rows($run_query);
		?>
		<?php
		if($count>0)
		{
			while($row = mysqli_fetch_array($run_query))
			{
				?>
				<option value="<?php echo $row['cityname'] ?>"<?php if($row['idcity']==$cityname) {echo 'selected';}?>> <?php echo $row['cityname'] ?></option>
			<?php
			}
		}
        ?>
		
		</select>
		</select>
		</label></td>
		</tr>
		
		<tr align="Right">
		<td colspan="2"><label>
		<input type="submit" name="submit" value="Edit Records">
		</label></td>
		</tr>
	</table>
	</form>
	<script>
	document.getElementById("myform").onsubmit = function () 
	{
		var x = document.forms["myform"]["name"].value;
		var y = document.forms["myform"]["email"].value;
		var z = document.forms["myform"]["contact"].value;

		var submit = true;

		if (x == null || x == "") 
		{
			nameError = "Please enter your name";
			document.getElementById("name").style.border="1.5px solid red";
			document.getElementById("name_error").style.color="Red";
			document.getElementById("name_error").innerHTML = nameError;
			submit = false;
		}

		if (y == null || y == "") 
		{
			emailError = "Please enter your email";
			document.getElementById("email").style.border="1.5px solid red";
			document.getElementById("email_error").style.color="Red";
			document.getElementById("email_error").innerHTML = emailError;
			submit = false;
		}

		if (z == null || z == "") 
		{
			telephoneError = "Please enter your contact number";
			document.getElementById("contact").style.border="1.5px solid red";
			document.getElementById("contact_error").style.color="Red";
			document.getElementById("contact_error").innerHTML = telephoneError;
			submit = false;
		}

		return submit;
	}

		function removeWarning() 
		{
			document.getElementById(this.id).style.border = "";
			document.getElementById(this.id + "_error").innerHTML = "";
		}

	document.getElementById("name").onkeyup = removeWarning;
	document.getElementById("email").onkeyup = removeWarning;
	document.getElementById("contact").onkeyup = removeWarning;
	</script>
</body>
</html>
	<?php
	}
	
	if (isset($_POST['submit']))
	{
	
	if (is_numeric($_POST['id']))
		{

			$id = $_POST['id'];
			$name = mysqli_real_escape_string($connect,htmlspecialchars($_POST['name']));
			$email = mysqli_real_escape_string($connect,htmlspecialchars($_POST['email']));
			$contact = mysqli_real_escape_string($connect,htmlspecialchars($_POST['contact']));
			$pname = mysqli_real_escape_string($connect,htmlspecialchars($_POST['pname']));
			$quantity = mysqli_real_escape_string($connect,htmlspecialchars($_POST['quantity']));
			$cname = mysqli_real_escape_string($connect,htmlspecialchars($_POST['cname']));
			$sname = mysqli_real_escape_string($connect,htmlspecialchars ($_POST['sname']));
			$cityname = mysqli_real_escape_string($connect,htmlspecialchars($_POST['cityname']));

			if ($name == '' || $email == '' || $contact == '' || $pname == '' || $quantity == '' || $cname == '' || $sname == '' || $cityname == '')
			{

				$error = 'ERROR: Please fill in all  fields!';


				valid($id, $name, $email, $contact, $pname, $quantity, $cname, $sname, $cityname, $error,$connect);
			}
				
			else
			{	
				$run_query = mysqli_query($connect, "SELECT * from product where email = '$email' ");
				$count = mysqli_num_rows($run_query);
				if($count>0)
				{
					echo "<script>
					alert('this email id already exists');						
						</script>";
					include("product.php");
				}
				else
				{					
					mysqli_query($connect, "UPDATE product SET name='$name', email='$email' ,contact='$contact' , pname='$pname' , quantity='$quantity', 
									cname='$cname' , sname='$sname' , cityname = '$cityname' WHERE id= $id")
								or die(mysqli_error($connect));
					header("Location: view.php");
				}
			}
		}
			else
			{	

				echo 'Error!';
			}
	}
	else

	{

		if (isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0)
		{

			$id = $_GET['id'];
			$result = mysqli_query($connect,"SELECT * FROM product WHERE id= $id")
			or die(mysqli_error($connect));
			$row = mysqli_fetch_array($result);

			if($row)
			{

				$name = $row['name'];
				$email = $row['email'];
				$contact = $row['contact'];
				$pname = $row['pname'];
				$quantity = $row['quantity'];
				$cname = $row['cname'];
				$sname = $row['sname'];
				$cityname = $row['cityname'];

				valid($id, $name, $email, $contact, $pname, $quantity, $cname, $sname, $cityname, '', $connect);
			}
			else
			{
				echo "No results!";
			}
		}
		else

		{
			echo 'Error!';
		}
	}
	echo "<footer>";
		 
			echo $_SERVER["SERVER_NAME"];
			echo $_SERVER["SERVER_SOFTWARE"];
			
	echo "</footer>";
?>