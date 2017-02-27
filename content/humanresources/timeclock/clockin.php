<?php
require('Application.php');
$query1=("INSERT INTO \"timeclock\" ".
		 "(\"firstname\", \"lastname\", \"workday\", \"clockin\", \"status\") ".
		 "VALUES ('".$_SESSION['firstname']."', '".$_SESSION['lastname']."', '".mktime(0, 0, 0, date("m"), date("d"), date("Y"))."', '".mktime()."', 'in')");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
header("location: ../../index.php");
?>
