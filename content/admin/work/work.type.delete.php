<?php
require('Application.php');
$ID=$_GET['ID'];
$query1=("UPDATE \"billingcodes\" ".
		"SET ".
		"\"active\" = 'no' ".
		"WHERE \"ID\" = '$ID'");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
header("location: ../index.php");
?>
