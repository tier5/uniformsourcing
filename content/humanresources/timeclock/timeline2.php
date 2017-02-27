<?php
require('Application.php');
$eid=$_GET['employees'];
$startmonth=$_GET['startmonth'];
$startday=$_GET['startday'];
$startyear=$_GET['startyear'];
$endmonth=$_GET['endmonth'];
$endday=$_GET['endday'];
$endyear=$_GET['endyear'];
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
$query2=("SELECT * ".
		 "FROM \"timeclock\" ".
		 "WHERE \"firstname\" = '".$data1[0]['firstname']."' AND ".
		 "\"lastname\" = '".$data1[0]['lastname']."' AND".
		 "\"workday\" >= '$startdate1' AND ".
		 "\"workday\" <= '$enddate1' AND ".
		 "\"status\" = 'out' ".
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
echo "<center>";
$total="0";
$ot="0";
echo "<table cellspacing=\"2\" width=\"50%\">";
echo "<tr align=\"center\">";
echo "<td><font face=\"arial\" size=\"-1\"><b>Date</b></font></td>";
echo "<td><font face=\"arial\" size=\"-1\"><b>In</b></font></td>";
echo "<td><font face=\"arial\" size=\"-1\"><b>Out</b></font></td>";
echo "<td><font face=\"arial\" size=\"-1\"><b>Total</b></font></td>";
echo "</tr>";
for($i=0; $i < count($data2); $i++){
	$workday1=$data2[$i]['workday'];
	$intime=$data2[$i]['clockin'];
	$outtime=$data2[$i]['out'];
	$intime2=date("H:i:s", $intime);
	$outtime2=date("H:i:s", $outtime);
	$workdate=date("m/d/Y", $workday1);
	$workdate1=$workday1;
	$totaltime1=bcdiv("".$data2[$i]['total']."", "60", "2");
	$time=bcadd("$totaltime1", "$time", "2");
	if(bcmod("$i", "2") == 1){
		$color1="d0d0d0";
	}else{
		$color1="ffffff";
	}
	echo "<tr bgcolor=\"$color1\">";
	echo "<td align=\"center\"><font face=\"arial\" size=\"-1\">$workdate</font></td>";
	echo "<td align=\"center\"><font face=\"arial\" size=\"-1\">$intime2</font></td>";
	echo "<td align=\"center\"><font face=\"arial\" size=\"-1\">$outtime2</font></td>";
	echo "<td align=\"right\"><font face=\"arial\" size=\"-1\">$totaltime1</font></td>";
	echo "</tr>";
}
echo "</table>";
echo "<p>";
echo "<table>";
echo "<tr>";
echo "<td><font face=\"arial\" size=\"+1\"><b>Total Hours:</b></font></td>";
echo "<td><font face=\"arial\" size=\"+1\" color=\"008000\">$time</font></td>";
echo "</tr>";
echo "</table>";
echo "</div>";
require('../../trailer.php');
?>
