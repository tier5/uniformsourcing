<?php
require('Application.php');
$eid=$_POST['employees'];
$startmonth=$_POST['startmonth'];
$startday=$_POST['startday'];
$startyear=$_POST['startyear'];
$endmonth=$_POST['endmonth'];
$endday=$_POST['endday'];
$endyear=$_POST['endyear'];
if($debug == "on"){
	echo "eid IS $eid<br>";
	echo "startmonth IS $startmonth<br>";
	echo "startday IS $startday<br>";
	echo "startyear IS $startyear<br>";
	echo "endmonth IS $endmonth<br>";
	echo "endday IS $endday<br>";
	echo "endyear IS $endyear<br>";
}
if($startmonth == "month" OR $startday == "day" OR $startyear == "year"){
	header("location: review.php?error=startdate");
	exit;
}
if($endmonth == "month" OR $endday == "day" OR $endyear == "year"){
	header("location: review.php?error=enddate");
	exit;
}
$startdate=date("m/d/Y", mktime(0, 0, 0, $startmonth, $startday, $startyear));
$enddate=date("m/d/Y", mktime(0, 0, 0, $endmonth, $endday, $endyear));
$startdate1=mktime(0, 0, 0, $startmonth, $startday, $startyear);
$enddate1=mktime(0, 0, 0, $endmonth, $endday, $endyear);
if($enddate1 < $startdate1){
	header("location: review.php?error=invalid");
	exit;
}
if(isset($_POST['forward'])){
   header("location: timeline2.php?employees=$eid&startmonth=$startmonth&startday=$startday&startyear=$startyear&endmonth=$endmonth&endday=$endday&endyear=$endyear");
   exit;
}
require('../../header.php');
$query1=("SELECT * ".
		 "FROM \"employeeDB\" ".
		 "WHERE \"employeeID\" = '$eid' ");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
$query2=("SELECT SUM(total) as totaltime, \"workday\" ".
		 "FROM \"timeclock\" ".
		 "WHERE \"firstname\" = '".$data1[0]['firstname']."' AND ".
		 "\"lastname\" = '".$data1[0]['lastname']."' AND ".
		 "\"workday\" >= '$startdate1' AND ".
		 "\"workday\" <= '$enddate1' ".
		 "GROUP BY \"workday\" ".
		 "ORDER BY \"workday\" ASC");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2)){
	$data2[]=$row2;
}
echo "<center>";
echo "<font face=\"arial\"><b>Time Log For ".$data1[0]['firstname']." ".$data1[0]['lastname']."</b></font>";
echo "<br>";
echo "<font face=\"arial\" size=\"-1\"><b>$startdate - $enddate</b></font>";
echo "<p>";
echo "</center>";
echo "<div align=\"center\">";
$total="0";
$ot="0";
echo "<table cellpadding-\"2\" width=\"100%\">";
echo "<tr>";
echo "<td><font face=\"arial\" size=\"-1\"><b>Date</b></font></td>";
echo "<td><font face=\"arial\" size=\"-1\"><b>Total</b></font></td>";
echo "<td><font face=\"arial\" size=\"-1\"><b>OT Total</b></font></td>";
echo "</tr>";
for($i=0; $i < count($data2); $i++){
	$workday1=$data2[$i]['workday'];
	$workdate=date("m/d/Y", $data2[$i]['workday']);
	$workdate1=$workday1;
	if($data2[$i]['totaltime'] <= 480){
		$totaltime1=$data2[$i]['totaltime'] / 60;
		$ottime1=0;
	}else{
		$totaltime1=8;
		$ottime1=($data2[$i]['totaltime'] / 60) - 8;
	}
	$time=$totaltime1 + $time;
	$ottotal=$ottime1 + $ottotal;
	echo "<tr>";
	echo "<td><font face=\"arial\" size=\"-1\">$workdate</font></td>";
	echo "<td><font face=\"arial\" size=\"-1\">$totaltime1</font></td>";
	echo "<td><font face=\"arial\" size=\"-1\">$ottime1</font></td>";
	echo "</tr>";
}
echo "<tr>";
echo "<td></td>";
echo "<td><font face=\"arial\" size=\"-1\"><b>$time</b></font></td>";
echo "<td><font face=\"arial\" size=\"-1\"><b>$ottotal</b></font></td>";
echo "</tr>";
echo "</table>";
echo "<p>";
echo "<table>";
echo "<tr>";
echo "<td><font face=\"arial\" size=\"+1\"><b>Regular Hours:</b></font></td>";
echo "<td><font face=\"arial\" size=\"+1\" color=\"008000\">$time</font></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font face=\"arial\" size=\"+1\"><b>OT Hours:</b></font></td>";
echo "<td><font face=\"arial\" size=\"+1\" color=\"ff0000\">$ottotal</font></td>";
echo "</tr>";
echo "</table>";
echo "</div>";
require('../../trailer.php');
?>
