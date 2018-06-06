<html>
   <body>
      
      <?php
         echo Form::open(['action']);
            echo Form::text('name','Username');
            echo '<br/>';
            
            echo Form::text('email', 'example@gmail.com');
            echo '<br/>';
     
            echo Form::password('password');
            echo '<br/>';
            
            echo Form::submit('submit');
         echo Form::close();
      ?>
   
   </body>
</html>