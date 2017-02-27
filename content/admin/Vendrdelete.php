<?php
require('Application.php');
$vendorID=$_GET['vendorID'];
if($debug == "on"){
	echo "vendorID IS $vendorID<br>";
}
$query1="UPDATE \"vendor\" ".
		 "SET ".
		 "\"active\" = 'no' ".
		 "WHERE \"vendorID\" = $vendorID ";
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
header("location: index.php");
?>