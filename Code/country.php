<?php
$dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
	$dbname="test";
	$connect = mysql_connect($dbhost,$dbuser,$dbpass,$dbname) 
	or die ('MySQL connect failed. ' . mysql_error());
	
	mysql_select_db($dbname,$connect) or die('Cannot select database. ' . mysql_error());
	$result = mysql_query("SELECT * FROM country")
	or die(mysql_error());
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
<script type="text/javascript" src="jquery-1.4.1.min.js"></script>
<script type="text/javascript">
/*$(document).ready(function()
{
	
	$('#country').change(function()
	{
		
		var id=$(this).val();
		var dataString = 'id='+ id;
		$('#state').find('option').remove();
		$('#city').find('option').remove();
		$.ajax
		({
			type: 'POST',
			url: 'state.php',
			data: dataString,
			cache: false,
			success: function(html)
			{
				
				$('#state').html(html);
			} 
		});
	});
	
	
	$('#state').change(function()
	{
		
		var id=$(this). val();
		var dataString = 'id='+ id;
	
		$.ajax
		(
		{
			type: 'POST',
			url: 'city.php',
			data: dataString,
			cache: false,
			success: function(html)
			{
				
				$('#city').html(html);
			} 
		}
		);
	});
	
});
*/
$(document).ready(function(){
    $('#country').on('change',function(){
        var countryID = $(this).val();
        if(countryID){
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
    
    $('#state').on('change',function(){
        var stateID = $(this).val();
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
label
{
font-weight:bold;
padding:10px;
}
div
{
	margin-top:100px;
}
select
{
	width:200px;
	height:35px;
	border:2px solid #456879;
	border-radius:10px;
}
 
.color {
	color:green;
}
 
.link {
	color:red;
}
</style>
</head>
 
<body>
<label>Country :</label> 
<select name="country" class="select" id="country">
<option selected="selected">--Select Country--</option>
<?php
	$results =mysql_query("SELECT * FROM country order by cname ASC");
	
	while($row=mysql_fetch_array($results))
	{	
		$idcountry=$row['idcountry'];
		?>
        <option value='$idcountry'><?php echo $row['cname']; ?></option>
        <?php
	} 
?>
</select>
<br><br><br>
<label>State :</label> <select name="state" class="select" id="state">
<option selected="selected">--Select State--</option>
</select>

<br><br><br>
<label>City :</label> <select name="city" class="select" id="city">
<option selected="selected">--Select City--</option>
</select>

<br><br><br>
</div>
<br />
</center>
</body>
</html>