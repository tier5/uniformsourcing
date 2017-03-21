<?php
require('Application.php');
$query1=("INSERT INTO \"timeclock\" ".
		 "(\"firstname\", \"lastname\", \"workday\", \"clockin\", \"status\") ".
		 "VALUES ('".$_SESSION['firstname']."', '".$_SESSION['lastname']."', '".mktime(0, 0, 0, date("m"), date("d"), date("Y"))."', '".mktime()."', 'in')");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
$sql = '';
$sql = "INSERT INTO \"audit_logs\" (";
$sql .= " \"inventory_id\", \"employee_id\", \"updated_time\",";
$sql .= " \"log\") VALUES (";
$sql .= " 'null' ";
$sql .= ", '". $_SESSION['employeeID'] ."'";
$sql .= ", '". date('U') ."'";
$sql .= ", 'Clock In'";
$sql .= ")";
if(!($audit = pg_query($connection,$sql))){
	$return_arr['error'] = pg_last_error($connection);
}
header("location: ../../index.php");
?>
