
<?php
$con=mysqli_connect('localhost','root','','test');
if ($con->connect_error) {
die("Database Connection failed: " . $con->connect_error);
}
?>