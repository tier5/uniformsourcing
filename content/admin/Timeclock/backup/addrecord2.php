<?php
require('Application.php');
$employee=$_POST['employee'];
$workmonth=$_POST['workmonth'];
$workday=$_POST['workday'];
$workyear=$_POST['workyear'];
$clockinhour=$_POST['clockinhour'];
$clockinmin=$_POST['clockinmin'];
$clockinsec=$_POST['clockinsec'];
$clockouthour=$_POST['clockouthour'];
$clockoutmin=$_POST['clockoutmin'];
$clockoutsec=$_POST['clockoutsec'];
if($debug == "on"){
	require('../../header.php');
	echo "employee IS $employee<br>";
	echo "workmonth IS $workmonth<br>";
	echo "workday IS $workday<br>";
	echo "workyear IS $workyear<br>";
	echo "clockinhour IS $clockinhour<br>";
	echo "clockinmin IS $clockinmin<br>";
	echo "clockinsec IS $clockinsec<br>";
	echo "clockouthour IS $clockouthour<br>";
	echo "clockoutmin IS $clockoutmin<br>";
	echo "clockoutsec IS $clockoutsec<br>";
}
$workdate=mktime(00, 18, 00, $workmonth, $workday, $workyear);
$clockin=mktime($clockinhour, $clockinmin, $clockinsec, $workmonth, $workday, $workyear);
$clockout=mktime($clockouthour, $clockoutmin, $clockoutsec, $workmonth, $workday, $workyear);
$checktime=($clockout - $clockin);
$total1=($checktime / 60);
if($debug == "on"){
	echo $today . "today<br>";
	echo $workday . "workday<br>";
	echo $clockin . "clockin<br>";
	echo $clockout . "clockout<br>";
	echo $checktime . "checktime<br>";
	echo $total1 . "total1<br>";
}
if($checktime <= 0){
	require('../../header.php');
	echo "There is a problem with the times you entered. They are either the same or clockout time is before clockin time.";
	require('../../trailer.php');
	exit;
}
$query1=("SELECT * ".
		 "FROM \"employeeDB\" ".
		 "WHERE \"employeeID\" = '$employee'");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
if(count($data1) > 1 OR count($data1) < 1){
	require('../../header.php');
	echo "For some reason there was a problem getting the employees record";
	require('../../trailer.php');
	exit;
}
$firstname1=$data1[0]['firstname'];
$lastname1=$data1[0]['lastname'];
$query2=("INSERT INTO \"timeclock\" ".
		 "(\"firstname\", \"lastname\", \"workday\", \"clockin\", \"out\", \"status\", \"total\") ".
		 "VALUES ('$firstname1', '$lastname1', '$workdate', '$clockin', '$clockout', 'out', '$total1')");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
header("location: ../index.php");
?>
