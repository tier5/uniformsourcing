<?php
require('Application.php');
$query1=("SELECT * ".
		 "FROM \"timeclock\" ".
		 "WHERE \"firstname\" = '".$_SESSION['firstname']."' AND \"lastname\" = '".$_SESSION['lastname']."' AND \"status\" = 'in' ");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
$intime=$data1[0]['clockin'];
$outtime=mktime();
$total=($outtime - $intime);
$total1=bcdiv("$total", "60", "0");
$query2=("UPDATE \"timeclock\" ".
		 "SET ".
		 "\"out\" = '$outtime', ".
		 "\"status\" = 'out', ".
		 "\"total\" = '$total1' ".
		 "WHERE \"ID\" = '".$data1[0]['ID']."' ");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
header("location: ../../index.php");
?>
