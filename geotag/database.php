<?php
$con=mysqli_connect('localhost','root','borson7795','geotag');

if(mysqli_connect_errno())
{
echo 'Failed to connect '.mysqli_connect_error();
}else echo "Connection Successful";
?>