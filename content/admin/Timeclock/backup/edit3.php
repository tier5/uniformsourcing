<?php
require('Application.php');
require('../../header.php');
$ID=$_GET['ID'];
if($debug == "on"){
	echo "ID IS $ID";
}
$query1=("SELECT * ".
		 "FROM \"timeclock\" ".
		 "WHERE \"ID\" = '$ID'");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
$clockin1=explode(" ", $data1[0]['clockin']);
$clockin2=explode("/", $clockin1[0]);
$clockin3=explode(":", $clockin1[1]);
$clockinday=date("d", mktime($clockin3[0], $clockin3[1], $clockin3[2], $clockin2[0], $clockin2[1], $clockin2[2]));
$clockinmonth=date("m", mktime($clockin3[0], $clockin3[1], $clockin3[2], $clockin2[0], $clockin2[1], $clockin2[2]));
$clockinyear=date("Y", mktime($clockin3[0], $clockin3[1], $clockin3[2], $clockin2[0], $clockin2[1], $clockin2[2]));
$clockinhour=date("H", mktime($clockin3[0], $clockin3[1], $clockin3[2], $clockin2[0], $clockin2[1], $clockin2[2]));
$clockinmin=date("i", mktime($clockin3[0], $clockin3[1], $clockin3[2], $clockin2[0], $clockin2[1], $clockin2[2]));
$clockinsec=date("s", mktime($clockin3[0], $clockin3[1], $clockin3[2], $clockin2[0], $clockin2[1], $clockin2[2]));
$clockout1=explode(" ", $data1[0]['out']);
$clockout2=explode("/", $clockout1[0]);
$clockout3=explode(":", $clockout1[1]);
$clockoutday=date("d", mktime($clockout3[0], $clockout3[1], $clockout3[2], $clockout2[0], $clockout2[1], $clockout2[2]));
$clockoutmonth=date("m", mktime($clockout3[0], $clockout3[1], $clockout3[2], $clockout2[0], $clockout2[1], $clockout2[2]));
$clockoutyear=date("Y", mktime($clockout3[0], $clockout3[1], $clockout3[2], $clockout2[0], $clockout2[1], $clockout2[2]));
$clockouthour=date("H", mktime($clockout3[0], $clockout3[1], $clockout3[2], $clockout2[0], $clockout2[1], $clockout2[2]));
$clockoutmin=date("i", mktime($clockout3[0], $clockout3[1], $clockout3[2], $clockout2[0], $clockout2[1], $clockout2[2]));
$clockoutsec=date("s", mktime($clockout3[0], $clockout3[1], $clockout3[2], $clockout2[0], $clockout2[1], $clockout2[2]));
echo "<center>";
echo "<form action=\"edit4.php\" method=\"post\">";
echo "<input type=\"hidden\" name=\"id\" value=\"".$data1[0]['ID']."\">";
echo "<table>";
echo "<tr>";
echo "<td><b><font face=\"arial\" size=\"-1\"><div align=\"center\">Clock In</div></font></b></td>";
echo "<td><b><font face=\"arial\" size=\"-1\"><div align=\"center\">Clock Out</div></font></b></td>";
echo "</tr>";
echo "<tr bgcolor=999999>";
echo "<td>DATE <select name=\"clockinmonth\">";
echo "<option value=\"$clockinmonth\">$clockinmonth</option>";
for($i=1; $i <= 12; $i++){
	$month1=date("m", mktime(1, 1, 1, $i, date("d"), date("Y")));
	echo "<option value=\"$month1\">$month1</option>";
}
echo "</select> / ";
echo "<select name=\"clockinday\">";
echo "<option value=\"$clockinday\">$clockinday</option>";
for($i=1; $i <= 31; $i++){
	$day1=date("d", mktime(1, 1, 1, date("m"), $i, date("Y")));
	echo "<option value=\"$day1\">$day1</option>";
}
echo "</select> / ";
echo "<select name=\"clockinyear\">";
echo "<option value=\"$clockinyear\">$clockinyear</option>";
for($i=0; $i <= 5; $i++){
	$year1=date("Y", mktime(1, 1, 1, date("m"), date("d"), date("Y")-$i));
	echo "<option value=\"$year1\">$year1</option>";
}
echo "</select>";
echo "</td>";
echo "<td>DATE <select name=\"clockoutmonth\">";
echo "<option value=\"$clockoutmonth\">$clockoutmonth</option>";
for($i=1; $i <= 12; $i++){
	$month2=date("m", mktime(1, 1, 1, $i, date("d"), date("Y")));
	echo "<option value=\"$month2\">$month2</option>";
}
echo "</select> / ";
echo "<select name=\"clockoutday\">";
echo "<option value=\"$clockoutday\">$clockoutday</option>";
for($i=1; $i <= 31; $i++){
	$day2=date("d", mktime(1, 1, 1, date("m"), $i, date("Y")));
	echo "<option value=\"$day2\">$day2</option>";
}
echo "</select> / ";
echo "<select name=\"clockoutyear\">";
echo "<option value=\"$clockoutyear\">$clockoutyear</option>";
for($i=0; $i <= 5; $i++){
	$year2=date("Y", mktime(1, 1, 1, date("m"), date("d"), date("Y")-$i));
	echo "<option value=\"$year2\">$year2</option>";
}
echo "</select>";
echo "</td>";
echo "</tr>";
echo "<tr bgcolor=bbbbbb>";
echo "<td>";
echo "TIME ";
echo "<select name=\"clockinhour\">";
echo "<option value=\"$clockinhour\">$clockinhour</option>";
for($i=0; $i <= 23; $i++){
	$hour1=date("H", mktime($i, 1, 1, date("m"), date("d"), date("Y")));
	echo "<option value=\"$hour1\">$hour1</option>";
}
echo "</select> : ";
echo "<select name=\"clockinmin\">";
echo "<option value=\"$clockinmin\">$clockinmin</option>";
for($i=0; $i <= 59; $i++){
	$min1=date("i", mktime(1, $i, 1, date("m"), date("d"), date("Y")));
	echo "<option value=\"$min1\">$min1</option>";
}
echo "</select> : ";
echo "<select name=\"clockinsec\">";
echo "<option value=\"$clockinsec\">$clockinsec</option>";
for($i=0; $i <= 59; $i++){
	$sec1=date("s", mktime(1, 1, $i, date("m"), date("d"), date("Y")));
	echo "<option value=\"$sec1\">$sec1</option>";
}
echo "</select>";
echo "</td>";
echo "<td>";
echo "TIME ";
echo "<select name=\"clockouthour\">";
echo "<option value=\"$clockouthour\">$clockouthour</option>";
for($i=0; $i <= 23; $i++){
	    $hour2=date("H", mktime($i, 1, 1, date("m"), date("d"), date("Y")));
		    echo "<option value=\"$hour2\">$hour2</option>";
}
echo "</select> : ";
echo "<select name=\"clockoutmin\">";
echo "<option value=\"$clockoutmin\">$clockoutmin</option>";
for($i=0; $i <= 59; $i++){
	    $min2=date("i", mktime(1, $i, 1, date("m"), date("d"), date("Y")));
		    echo "<option value=\"$min2\">$min2</option>";
}
echo "</select> : ";
echo "<select name=\"clockoutsec\">";
echo "<option value=\"$clockoutsec\">$clockoutsec</option>";
for($i=0; $i <= 59; $i++){
	    $sec2=date("s", mktime(1, 1, $i, date("m"), date("d"), date("Y")));
		    echo "<option value=\"$sec2\">$sec2</option>";
}
echo "</select>";
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td colspan=\"2\"><div align=\"center\"><input type=\"Submit\" name=\"submit\" value=\"Submit\"></div></td>";
echo "</tr>";
echo "</table>";
echo "</center>";
echo "</form>";
require('../../trailer.php');
?>
