<?php
require('Application.php');
$employeeID=$_GET['employeeID'];
if($debug == "on"){
	echo "employeeID IS $employeeID<br>";
}
$active1="no";
$query1=("UPDATE \"employeeDB\" ".
		 "SET ".
		 "\"active\" = '$active1' ".
		 "WHERE \"employeeID\" = '$employeeID' ");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
$query2=("UPDATE \"permissions\" ".
		 "SET ".
		 "\"login\" = 'off' ".
		 "WHERE \"employee\" = '$employeeID' ");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
header("location: index.php");
?>
