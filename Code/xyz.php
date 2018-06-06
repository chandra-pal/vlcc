<?php
   if( $_POST["name"] || $_POST["age"] ) {
      if (preg_match("/[^A-Za-z'-]/",$_POST['name'] )) {
         die ("invalid name and name should be alpha");
      }
      echo "Welcome ". $_POST['name']. "<br />";
      echo "You are ". $_POST['age']. " years old.";
      
      exit();
   }
?>
<html>
   <body>
   
      <form action = "<?php $_PHP_SELF ?>" method = "POST">
         Name: <input type = "text" name = "name" />
         Age: <input type = "text" name = "age" />
         <input type = "submit" />
      </form>
   
   </body>
</html>

$(document).ready(function()
{
	
	$('.country').on('change',function()
	{
		
		var id=$(this).val();
		var dataString = 'id='+ id;
		$('.state').find('option').remove();
		$('.city').find('option').remove();
		$.ajax
		({
			type: 'POST',
			url: 'state.php',
			data: dataString,
			cache: false,
			success: function(html)
			{
				
				$('.state').html(html);
				$('.city').html('<option value="">--Select state--</option>');
			} 
		});
	});
	
	
	$('.state').on('change',function()
	{
		
		var id=$(this).val();
		var dataString = 'id='+ id;
	
		$.ajax
		({
			type: 'POST',
			url: 'city.php',
			data: dataString,
			cache: false,
			success: function(html)
			{
				
				$('.city').html('<option value="">Select city first</option>');
			} 
		});
	});
	
});
