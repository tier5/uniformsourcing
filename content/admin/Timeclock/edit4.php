<?php
require('Application.php');
$id=$_POST['id'];
$clockinmonth=$_POST['clockinmonth'];
$clockinday=$_POST['clockinday'];
$clockinyear=$_POST['clockinyear'];
$clockinhour=$_POST['clockinhour'];
$clockinmin=$_POST['clockinmin'];
$clockinsec=$_POST['clockinsec'];
$clockoutmonth=$_POST['clockoutmonth'];
$clockoutday=$_POST['clockoutday'];
$clockoutyear=$_POST['clockoutyear'];
$clockouthour=$_POST['clockouthour'];
$clockoutmin=$_POST['clockoutmin'];
$clockoutsec=$_POST['clockoutsec'];
if($debug == "on"){
	require('../../header.php');
	echo "id IS $id<br>";
	echo "clockinmonth IS $clockinmonth<br>";
	echo "clockinday IS $clockinday<br>";
	echo "clockinyear IS $clockinyear<br>";
	echo "clockinhour IS $clockinhour<br>";
	echo "clockinmin IS $clockinmin<br>";
	echo "clockinsec IS $clockinsec<br>";
	echo "clockoutmonth IS $clockoutmonth<br>";
	echo "clockoutday IS $clockoutday<br>";
	echo "clockoutyear IS $clockoutyear<br>";
	echo "clockouthour IS $clockouthour<br>";
	echo "clockoutmin IS $clockoutmin<br>";
	echo "clockoutsec IS $clockoutsec<br>";
}
$intime=mktime($clockinhour, $clockinmin, 0, $clockinmonth, $clockinday, $clockinyear);
$intime1=date("m/d/Y H:i:s", $intime);
$outtime=mktime($clockouthour, $clockoutmin, 0, $clockoutmonth, $clockoutday, $clockoutyear);
$outtime1=date("m/d/Y H:i:s", $outtime);
$total=($outtime - $intime);
$total1=($total / 60);
if($total < 0){
	require('../../header.php');
	echo "<b>The times you entered are invalid.</b>";
	require('../../trailer.php');
	exit;
}
if($debug == "on"){
	echo "<center>";
	echo "<table>";
	echo "<tr>";
	echo "<td><b>In Unix Time</b></td>";
	echo "<td><b>Out Unix Time</b></td>";
	echo "<td><b>Diff Unix Time</b></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td>$intime</td>";
	echo "<td>$outtime</td>";
	echo "<td>$total</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td>$intime1</td>";
	echo "<td>$outtime1</td>";
	echo "<td>$total1</td>";
	echo "</tr>";
	echo "</table>";
	require('../../trailer.php');
}else{
	$query1=("UPDATE \"timeclock\" ".
			 "SET \"clockin\" = '$intime1', ".
			 "\"out\" = '$outtime1', ".
			 "\"total\" = '$total1' ".
			 "WHERE \"ID\" = '$id'");
	if(!($result1=pg_query($connection,$query1))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	header("location: ../index.php");
}
?>
