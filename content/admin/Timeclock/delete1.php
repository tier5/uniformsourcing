<?php
require('Application.php');
$id=$_POST['id'];
if($debug == "on"){
	echo "id IS $id<br>";
}else{
	$query1=("DELETE ".
			 "FROM \"timeclock\" ".
			 "WHERE \"ID\" = '$id'");
	$result1=mysql_query($query1,$connection)
		or die(mysql_error());
	header("location: ../index.php");
}
