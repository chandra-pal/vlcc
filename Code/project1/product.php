<!DOCTYPE HTML>
<?php
	include("connect.php");
	$result = mysqli_query($connect,"SELECT product.* , country.cname as country, state.sname as state, city.cityname as city FROM product left join country on country.idcountry=product.cname left join state on state.idstate=product.sname
		left join city on city.idcity=product.cityname")
	or die(mysqli_error($connect));
?>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet"href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript">
	$(document).ready(function(){
	$('#email').change(function(){	
		var id=$(this).val();
		var html= new Array();
		//alert(id);
		$.ajax({	
			type:'POST',
			url:'confirm.php',
			data: 'email='+id,
			dataType: "json",
			success:function(html)
			{	
				if( html.count > 0)
				{
					if(confirm('the email already exists, press "ok" if you want to continue'))
					{	
						$("#name").val(html.data.name);
						$("#email").val(html.data.email);
						$("#contact").val(html.data.contact);
						$("#product").val(html.data.pname);
						$("#quantity").val(html.data.quantity);
						$("#country").val(html.data.cname);
						$("#state").val(html.data.sname);
						$("#city").val(html.data.cityname);
					}
					else
					{
						$("#name").val("");
						$("#contact").val("");
						$("#product").val("");
						$("#quantity").val("");
						$("#country").val("");
						$("#state").val("");
						$("#city").val("");
					}
				}
				else
				{
					
					$("#contact").val("");
					$("#product").val("");
					$("#quantity").val("");
					$("#country").val("");
					$("#state").val("");
					$("#city").val("");
				}
			}
		});	
	});
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
	$('#sub').click(function()
	{	var x = document.forms["myform"]["name"].value;
		var y = document.forms["myform"]["email"].value;
		var z = document.forms["myform"]["contact"].value;
		var a = document.forms["myform"]["product"].value;
		var b = document.forms["myform"]["quantity"].value;
		var c = document.forms["myform"]["country"].value;
		var d = document.forms["myform"]["state"].value;
		var e = document.forms["myform"]["city"].value;

		var submit = true;
		
		var reg = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z]{2,3})+$/g;
		var val = /^[789]+[0-9]+$/; 
		if(reg.test(document.getElementById("email").value))
		{
			submit = true;
			if(val.test(document.getElementById("contact").value))
			{
				submit = true;
			}
			else
			{
				submit = false;
				alert("contact number is not valid \n 1. contact number should contains only digits \n 2. only enter phone number starting with 7,8,9");
			}
		}
		else
		{	
		alert("email id not valid \n['1. should not start with digit or any special characters \n 2. it should be in this pattern abc@mail.com(any valid domain)']");
			submit = false;
		if(val.test(document.getElementById("contact").value))
			{
				submit = true;
			}
			else
			{
				submit = false;
				alert("contact number is not valid \n 1. contact number should contains only digits \n 2. only enter phone number starting with 7,8,9");
			}
		}
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

		if (a == null || a == "") 
		{
			productError = "Please enter Product Name";
			document.getElementById("product").style.border="1.5px solid red";
			document.getElementById("product_error").style.color="Red";
			document.getElementById("product_error").innerHTML = productError;
			submit = false;
		}

		if (b == null || b == "") 
		{
			quantityError = "Please enter Product Quantity";
			document.getElementById("quantity").style.border="1.5px solid red";
			document.getElementById("quantity_error").style.color="Red";
			document.getElementById("quantity_error").innerHTML = quantityError;
			submit = false;
		}

		if (c == null || c == "") 
		{
			countryError = "Please enter Country";
			document.getElementById("country").style.border="1.5px solid red";
			submit = false;
		}

		if (d == null || d == "") 
		{
			stateError = "Please enter state";
			document.getElementById("state").style.border="1.5px solid red";
			submit = false;
		}

		if (e == null || e == "") 
		{
			cityError = "Please enter your city";
			document.getElementById("city").style.border="1.5px solid red";
			submit = false;
		}
		alert(submit);
		return submit;

		function removeWarning() 
		{
			document.getElementById(this.id).style.border = "";
			document.getElementById(this.id + "_error").innerHTML = "";
		}

	document.getElementById("name").onkeyup = removeWarning;
	document.getElementById("email").onkeyup = removeWarning;
	document.getElementById("contact").onkeyup = removeWarning;
	document.getElementById("product").onkeyup = removeWarning;
	document.getElementById("quantity").onkeyup = removeWarning;
		if(submit == 'true')
		{
		var name=$("#name").val();
		var email=$("#email").val();
		var contact=$("#contact").val();
		var pname=$("#product").val();
		var quantity=$("#quantity").val();
		var cname=$("#country").val();
		var sname=$("#state").val();
		var cityname=$("#city").val();
		$.ajax({
			type: 'POST',
			url: 'config.php',
			data: {name: name, email: email, contact: contact, pname: pname, quantity: quantity, cname: cname, sname: sname, cityname: cityname},
			dataType: "json",
			success:function(html)
			{
				alert(html);
					//return false;
					alert('records inserted successfully.');
					$("#del-"+html).show("slow");
					$("#customers").load("product.php #customers");
					$("#myform")[0].reset();
			}
		});
		}
	});
	
});
	function delFunction(id)
	{
		if(confirm('Are you sure you want to delete the record'))
		{	
			$.ajax({
				type:'POST',
				url:'del.php',
				data:'id='+id,
				success:function(html)
				{	
					$("#del-"+id).fadeOut("slow");
					$("#customers").load("product.php #customers");
				}
			});
		}
	}

	</script>
	<style>
		label
		{
			font-weight:bold;
			padding:10px;
			color: 		#F0FFF0;
		}
		
		
 
		.color {
			color:green;
		}
 
		.link {
		color:red;
		}
		body {margin:0;}

		.icon-bar 
		{
			width: 100%;
			background-color: #555;
			overflow: auto;
		}

		.icon-bar a 
		{
			float: left;
			width: 20%;
			text-align: center;
			padding: 12px 0;
			transition: all 0.3s ease;
			color: white;
			font-size: 36px;
		}

		.icon-bar a:hover 
		{
			background-color: #000;
		}

		.active 
		{
			background-color: #4CAF50 !important;
		}
		
		@keyframes example
			{
				from {background-color: #FFFFCC}
				to {background-color: 	#F5F5DC}
			}
		body{
			
			animation-duration:4s;
			animation-name: example;
			animation-iteration-count: infinite;
			animation-direction: alternate-reverse;
			
		}
		input:focus
		{
			background-color: 	#FAFAD2;
		}
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
		#edi 
		{
			font-family: 'Trebuchet MS', Arial, Helvetica, sans-serif;
			border-collapse: collapse;
			width: 100%;
			
		}

		#edi td, #edi th 
		{
			border: 1px solid #ddd;
			padding: 8px;
			border-bottom-left-radius: 25px;
		}

		#edi tr:nth-child(even)
		{
			background-color: #f2f2f2;
			border-bottom-left-radius: 25px;
		}

		#edi tr:hover 
		{
			background-color: #ddd;
		}

		#edi th 
		{
			padding-top: 12px;
			padding-bottom: 12px;
			text-align: left;
			background-color: #4CAF50;
			color: white;
		}
	</style>
</head>
<body>
	<header>
		<div class="icon-bar">
		<a class="active" href="#"><i class="fa fa-home"></i></a> 
		<a href="#"><i class="fa fa-search"></i></a> 
		<a href="#"><i class="fa fa-envelope"></i></a> 
		<a href="#"><i class="fa fa-globe"></i></a>
		<a href="#"><i class="fa fa-trash"></i></a> 
		</div>
	</header>
	
	<br><br><br><br>
	<form  method="GET" action="search.php" > 
	      <input  type="text" name="query"> 
	      <input  type="submit" name="submit" value="Search"> 
   </form>
	<div style="text-align: right; margin:auto ; border: 3px solid 	#D2B48C ; border-bottom-left-radius: 25px; border-top-right-radius:25px; height:400px; 
	background: linear-gradient(to bottom right, #180c02,#fad7b7)">
 	<form method ="POST" align="center" id="myform" name="myform" class="myform">
         <br><br>
		 <label for="name">Name:</label>
		 <input type = "text" name = "name" id= "name" class="name"><span id="name_error"></span>
		 </input>
		 <br><br>
		 <label for="emailid">Email Id:</label>
		 <input type = "text" name = "email" id="email" class = "email" size="20" ><span id="email_error"></span>
		</input>
		 <br><br>
		 <label for="Contactnum">Contact Number:</label>
		 <input type = "number" name = "contact" id ="contact" maxlength="10" oninput="if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"><span id="contact_error"></span>
		 </input>
		 <br><br>
	 	 <label for="Pname">Product Name:</label>
		 <input type = "text" name = "pname" id="product"><span id="product_error"></span>
		 </input>
		 <br><br>
		 <label for="quant">Quantity:</label>
		 <input type = "text" name = "quantity" id="quantity"><span id="quantity_error"></span>
		 </input>
		 <br><br>
		<?php
		$run_query = mysqli_query($connect,"SELECT * FROM country  ORDER BY cname ASC");
		$count = mysqli_num_rows($run_query);
			
		?>
		<label for="country">Country:</label>
		<select name="cname" id="country">
        <option value="">Select Country</option>
        <?php
        if($count > 0)
		{
            while($row = mysqli_fetch_array($run_query))
			{
				$country_id=$row['idcountry'];
				$country_name=$row['cname'];
                echo "<option value='$country_id'>$country_name</option>";
            }
        }
		else
		{
            echo '<option value="">Country not available</option>';
        }
        ?>
    </select><br><br>
    <label for="state">State:</label>
    <select name="sname" id="state">
    <option value = "">Select country first</option>
    </select>
	<br><br>
    <label for="city">City:</label>
    <select name="cityname" id="city">
	<option value="">Select state first</option>
    </select>
		<br><br>
        <button id="sub">submit</button>
		 </form>

	</div>
	
	<?php
	echo "<body bgcolor='#F5F5DC'>";
	echo "<table border='1' cellpadding='10' align='center' id='customers'>";
	echo "<tr>
	<th>Id</font></th>
	<th>Name</font></th>
	<th>Email ID</font></th>
	<th>Contact</font></th>
	<th>Product Name</font></th>
	<th>Quantity</font></th>
	<th>Country</th>
	<th>state</th>
	<th>city</th>
	<th>Edit</font></th>
	<th>Delete</font></th>
	</tr>";

	while($row = mysqli_fetch_array( $result ))
	{
		$id=$row['id'];
		echo "<tr class='del-".$id."' id='del-".$id."'>";
		echo '<td><b><font color="#663300">' . $id . '</font></b></td>';
		echo '<td><b><font color="#663300">' . $row['name'] . '</font></b></td>';
		echo '<td><b><font color="#663300">' . $row['email'] . '</font></b></td>';
		echo '<td><b><font color="#663300"><a href="#" onclick="viewFunc('.$row['contact'].');" id="view">' . $row['contact'] . '</a></font></b></td>';
		echo '<td><b><font color="#663300">' . $row['pname'] . '</font></b></td>';
		echo '<td><b><font color="#663300">' . $row['quantity'] . '</font></b></td>';
		echo '<td><b><font color="#663300">' . $row['country'] . '</font></b></td>';
		echo '<td><b><font color="#663300">' . $row['state'] . '</font></b></td>';
		echo '<td><b><font color="#663300">' . $row['city'] . '</font></b></td>';
		echo '<td><b><font color="#663300"> <button id="btnSubmit" onclick="editFunc('.$id.');" class="btnSubmit">Edit</button></font></b></td>';
		echo '<td><b><font color="#663300"> <button id="del" onclick="delFunction('.$id.');" class= "del">Delete</button></font></b></td>';
		echo "</tr>";

	}
	
	echo "</table><br><BR>";
	?>
	<div id="dialog">
	<script>
	$(document).ready(function(){
	$('#country1').change(function(){
        var countryID = $(this).val();
		//alert(countryID);
        if(countryID)
		{
            $.ajax(
			{
                type:'POST',
				url:'change.php',
                data:'idcountry='+countryID,
                success:function(html)
				{
                    $('#state1').html(html);
                    $('#city1').html('<option value="">Select state first</option>'); 
                }
            }); 
        }
		else
		{
            $('#state1').html('<option value="">Select country first</option>');
            $('#city1').html('<option value="">Select state first</option>'); 
        }
    });
    
    $('#state1').change(function()
	{
        var stateID = $(this).val();
		//alert(stateID);
        if(stateID)
		{
            $.ajax(
			{
                type:'POST',
				url:'change.php',
				data:'idstate='+stateID,
                success:function(html)
				{
                    $('#city1').html(html);
                }
            }); 
        }
		else
		{
            $('#city1').html('<option value="">Select state first</option>'); 
        }
    }); 
});
	function editFunc(id)
	{
		var html= new Array();
		alert(id);
        $.ajax({
            type: 'POST',
            url: 'editup.php',
            data: 'id='+id,
            dataType: "json",
            success: function(html)
			{
				//alert(html);
				$("#id").val(html.data.id);
                $("#name1").val(html.data.name);
				$("#email1").val(html.data.email);
				$("#contact1").val(html.data.contact);
				$("#product1").val(html.data.pname);
				$("#quantity1").val(html.data.quantity);
				$("#country1").val(html.data.cname);
				$("#state1").val(html.data.sname);
				$("#city1").val(html.data.cityname);
                $("#dialog").dialog('open');
            }
        });
    }
	 $("#dialog").dialog({
        autoOpen: false,
        modal: true,
		height:600,
		width: 700,
        title: 'Edit Details',
        buttons: {
			edit: function()
			{
				var id=$("#id").val();
				var name=$("#name1").val();
				var email=$("#email1").val();
				var contact=$("#contact1").val();
				var pname=$("#product1").val();
				var quantity=$("#quantity1").val();
				var cname=$("#country1").val();
				var sname=$("#state1").val();
				var cityname=$("#city1").val();
				$.ajax({
					type: 'POST',
					url: 'edit.php',
					data: {id: id, name: name, email: email, contact: contact, pname: pname, quantity: quantity, cname: cname, sname: sname, cityname: cityname},
					success:function(html)
					{   
						alert(html);
						if(html==1)
						{
							$("#dialog").dialog('close');
						    $("#customers").load("product.php #customers");
						}
					}
				});
			}
        }
    });
	</script>
	 <form method="POST">
	 <input type="hidden" name="id" id="id" value="<?php echo $id; ?>"/>

	<table border="1" align="center" id="edi">
		<tr>
		<th colspan="2"><b><font color='white'>Edit Records </font></b></th>
		</tr>
		<tr>
		<td width="179"><b><font color='#663300'>Name<em>*</em></font></b></td>
		<td><label>
		<input type="text" name="name" id="name1" value=""><span id="name_error"></span>
		</input>
		</label></td>
		</tr>

		<tr>
		<td width="179"><b><font color='#663300'>Email<em>*</em></font></b></td>
		<td><label>
		<input type="text" name="email" value="" id="email1"><span id="email_error"></span>
		</input>
		</label></td>
		</tr>

		<tr>
		<td width="179"><b><font color='#663300'>Contact<em>*</em></font></b></td>
		<td><label>
		<input type="text" name="contact" maxlength="10" value="" id="contact1"><span id="contact_error"></span>
		</input>
		</label></td>
		</tr>

		<tr>
		<td width="179"><b><font color='#663300'>Product Name<em>*</em></font></b></td>
		<td><label>
		<input type="text" name="pname" value="" id="product1" />
		</label></td>
		</tr>
		
		<tr>
		<td width="179"><b><font color='#663300'>Quantity<em>*</em></font></b></td>
		<td><label>
		<input type="text" name="quantity" value="" id="quantity1"/>
		</label></td>
		</tr>
		
		<tr>
		<td width="179"><b><font color='#663300'>Country<em>*</em></font></b></td>
		<td><label> 
		<?php
		$run_query = mysqli_query($connect,"SELECT * FROM country  ORDER BY cname ASC");
		$count = mysqli_num_rows($run_query);
			
		?>
		<select name= "cname" id="country1" >
        <option value="">Select Country</option>
		<?php
        if($count > 0)
		{
            while($row = mysqli_fetch_array($run_query))
			{
				$country_id=$row['idcountry'];
				$country_name=$row['cname'];
                echo "<option value='$country_id'>$country_name</option>";
            }
        }else{
            echo '<option value="">Country not available</option>';
        }
        ?>
		</select>
		</label></td>
		</tr>
		
		<tr>
		<td width="179"><b><font color='#663300'>State<em>*</em></font></b></td>
		<td><label>
		<select name="sname" id="state1" >
		<option value="" > Select country first</option>
		</select>
		</label></td>
		</tr>
		
		<tr>
		<td width="179"><b><font color='#663300'>City<em>*</em></font></b></td>
		<td><label>
		<select name="cityname" id="city1" >
		<option value= "" >select state first</option>	
		</select>
		</label></td>
		</tr>
	</table>
	</form>
	</div>
	<div id="dialog_view">
	<style>
	#viewstyle
		{
			font-family: 'Trebuchet MS', Arial, Helvetica, sans-serif;
			border-collapse: collapse;
			width: 100%;
			
		}
		#viewstyle label 
		{
			font-weight: bold;
			padding: 10px;
			color: #195b4c;
		}
		#viewstyle td, #viewstyle th 
		{
			border: 1px solid #ddd;
			padding: 8px;
			border-bottom-left-radius: 25px;
		}

		#viewstyle tr:nth-child(even)
		{
			background-color: #f2f2f2;
			border-bottom-left-radius: 25px;
		}

		#viewstyle tr:hover 
		{
			background-color: #ddd;
		}

		#viewstyle th 
		{
			padding-top: 12px;
			padding-bottom: 12px;
			text-align: left;
			background-color: #4CAF50;
			color: white;
		}
	</style>
	<script>
	function viewFunc(id)
	{
		var html= new Array();
		alert(id);
		$.ajax({
			type: 'POST',
			url: 'contactview.php',
			data: 'contact='+id,
			dataType: "json",
			success: function(html)
			{
				$("#id_view").val(html.data.id);
				document.getElementById("name_view").innerHTML = html.data.name;
				document.getElementById("email_view").innerHTML = html.data.email;
				document.getElementById("contact_view").innerHTML = html.data.contact;
				document.getElementById("product_view").innerHTML = html.data.pname;
				document.getElementById("quantity_view").innerHTML = html.data.quantity;
				document.getElementById("country_view").innerHTML = html.data.country;
				document.getElementById("state_view").innerHTML = html.data.state;
				document.getElementById("city_view").innerHTML = html.data.city;
				$("#dialog_view").dialog('open');
			}
		});
	}
	$("#dialog_view").dialog({
        autoOpen: false,
        modal: true,
		height:400,
		width: 600,
        title: 'View Details',
        buttons: {
			close: function()
			{   
				$("#dialog_view").dialog('close');
			}
		}
    });
	</script>
	<form method="POST">
	<input type="hidden" name="id" id="id_view" value="<?php echo $id; ?>"/>
	<table border="1" align="center" id="viewstyle"> 
	<tr>
	<td width="179"><b><font color='#663300'>Name</font></b></td>
	<td><label>
	<span id="name_view"></span>
	</label>
	</td>
	</tr>
	
	<tr>
	<td width="179"><b><font color='#663300'>Email</font></b></td>
	<td><label>
	<span id="email_view"></span>
	</label>
	</td>
	</tr>
	
	<tr>
	<td width="179"><b><font color='#663300'>Contact</font></b></td>
	<td><label>
	<span id="contact_view"></span>
	</label></td>
	</tr>
	
	<tr>
	<td width="179"><b><font color='#663300'>Product</font></b></td>
	<td><label>
	<span id="product_view"></span>
	</label></td>
	</tr>
	
	<tr>
	<td width="179"><b><font color='#663300'>Quantity</font></b></td>
	<td><label>
	<span id="quantity_view"></span>
	</label></td>
	</tr>
	
	<tr>
	<td width="179"><b><font color='#663300'>Country</font></b></td>
	<td><label>
	<span id="country_view"></span>
	</label></td>
	</tr>
	
	<tr>
	<td width="179"><b><font color='#663300'>State</font></b></td>
	<td><label>
	<span id="state_view"></span>
	</label></td>
	</tr>
	
	<tr>
	<td width="179"><b><font color='#663300'>City</font></b></td>
	<td><label>
	<span id="city_view"></span>
	</label></td>
	</tr>
	</table>
	</form>
	</div>
	<footer align="center"><font color= "#DEB887">
		<?php 
			echo $_SERVER["SERVER_NAME"];
			echo $_SERVER["SERVER_SOFTWARE"];
			
		?>
	</footer>
</body>
</html>