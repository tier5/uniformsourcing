<?php
require('Application.php');
require('../../header.php');
$query1=("SELECT * ".
		 "FROM \"employeeDB\" ".
		 "WHERE \"username\" = '".$_SESSION['username']."' AND \"password\" = '".$_SESSION['password']."' ");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
$query2=("SELECT * ".
		 "FROM \"timeclock\" ".
		 "WHERE \"firstname\" = '".$data1[0]['firstname']."' AND \"lastname\" = '".$data1[0]['lastname']."' AND \"status\" = 'in' ");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2)){
	$data2[]=$row2;
}
$clockintime=strtotime($data2[0]['clockin']);
$nowtime=mktime();
$now24time=mktime(date("H")+24, date("i"), date("s"), date("m"), date("d"), date("Y"));
if(count($data2) == 0){
	echo "<center>";
	echo "<font face=\"arial\"><b>".$data1[0]['firstname']." ".$data1[0]['lastname']."</b> You are not clocked in</font>";
	echo "</center>";
}elseif($clockintime > $now24time){
	echo "<center>";
	echo "<font face=\"arial\"><b>".$data1[0]['firstname']." ".$data1[0]['lastname']."</b> You are clocked in and you have been clocked in for more than 24 hours.</font>";
	echo "</center>";
}elseif(count($data2) == 1){
	echo "<center>";
	echo "<font face=\"arial\"><b>".$data1[0]['firstname']." ".$data1[0]['lastname']."</b> You are clocked in.</font>";
	echo "</center>";
}
echo "<center>";
if($_GET['error'] == "startdate"){
	echo "<font face=\"arial\" color=\"red\" size=\"+1\">";
	echo "Please enter a valid STARTDATE.<br> Month, Day, and Year must all be valid";
	echo "</font>";
}
if($_GET['error'] == "enddate"){
	echo "<font face=\"arial\" color=\"red\" size=\"+1\">";
	echo "Please enter a valid ENDDATE.<br> Month, Day, and Year must all be valid";
	echo "</font>";
}
if($_GET['error'] == "invalid"){
	echo "<font face=\"arial\" color=\"red\" size=\"+1\">";
	echo "Your STARTDATE is after your ENDDATE.<br> Please make sure your ENDDATE is after your STARTDATE";
	echo "</font>";
}
echo "<p>";
echo "<p>";
echo "<form action=\"timeline.php\" method=\"post\">";
echo "<table>";
echo "<tr>";
echo "<td><font face=\"arial\">Start Date:</font></td>";
echo "<td><select name=\"startmonth\">";
echo "<option value=\"month\">Month</option>";
for($i=1; $i <= 12; $i++){
	$monthnam=date("M", mktime(1, 1, 1, $i, 1, 2005));
	$monthnum=date("m", mktime(1, 1, 1, $i, 1, 2005));
	echo "<option value=\"$monthnum\">$monthnam</option>";
}
echo "</select> / ";
echo "<select name=\"startday\">";
echo "<option value=\"day\">Day</option>";
for($i=1; $i <= 31; $i++){
	$daynum=date("d", mktime(1, 1, 1, 1, $i, 2005));
	echo "<option value=\"$daynum\">$daynum</option>";
}
echo "</select> / ";
echo "<select name=\"startyear\">";
echo "<option value=\"year\">Year</option>";
for($i=0; $i < 5; $i++){
	$yearnum=date("Y", mktime(1, 1, 1, 1, 1, date("Y")-$i));
	echo "<option value=\"$yearnum\">$yearnum</option>";
}
echo "</select>";
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td><font face=\"arial\">End Date:</font></td>";
echo "<td><select name=\"endmonth\">";
echo "<option value=\"month\">Month</option>";
for($i=1; $i <= 12; $i++){
	$endmonthnam=date("M", mktime(1, 1, 1, $i, 1, 2005));
	$endmonthnum=date("m", mktime(1, 1, 1, $i, 1, 2005));
	echo "<option value=\"$endmonthnum\">$endmonthnam</option>";
}
echo "</select> / ";
echo "<select name=\"endday\">";
echo "<option value=\"day\">Day</option>";
for($i=1; $i <= 31; $i++){
	$enddaynum=date("d", mktime(1, 1, 1, 1, $i, 2005));
	echo "<option value=\"$enddaynum\">$enddaynum</option>";
}
echo "</select> / ";
echo "<select name=\"endyear\">";
echo "<option value=\"year\">Year</option>";
for($i=0; $i < 5; $i++){
	$endyearnum=date("Y", mktime(1, 1, 1, 1, 1, date("Y")-$i));
	echo "<option value=\"$endyearnum\">$endyearnum</option>";
}
echo "</select>";
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"View Log (Payroll)\"><p><input name=\"Forward\" type=\"submit\" value=\"View Log\"></form></td>";
echo "<td></td>";
echo "</tr>";
echo "</table>";
echo "</center>";
require('../../trailer.php');
?>
