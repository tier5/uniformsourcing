<?php
require('Application.php');
require('../header.php');
$code=$_POST['code'];
if($debug == "on"){
	echo "code IS $code<br>";
}
$query1=("SELECT \"ID\", \"clientID\", \"employeeID\", \"itemid\", \"hours\", \"completiondate\", \"jobnotes\" ".
		 "FROM \"work1\" ".
		 "WHERE \"ID\" = '$code'");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$whoop[]=$row1;
}
$query2=("SELECT \"ID\", \"clientID\", \"client\", \"active\" ".
		 "FROM \"clientDB\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER by \"client\" ASC");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2)){
	$client8[]=$row2;
}
$query3=("SELECT \"ID\", \"clientID\", \"client\", \"active\" ".
		 "FROM \"clientDB\" ".
		 "WHERE \"ID\" = '".$whoop[0]['clientID']."'");
if(!($result3=pg_query($connection,$query3))){
	print("Failed query3: " . pg_last_error($connection));
	exit;
}
while($row3 = pg_fetch_array($result3)){
	$client7[]=$row3;
}
$query4=("SELECT \"firstname\", \"lastname\", \"employeeID\" ".
		 "FROM \"employeeDB\" ".
		 "WHERE \"active\" = 'yes'");
if(!($result4=pg_query($connection,$query4))){
	print("Failed query4: " . pg_last_error($connection));
	exit;
}
while($row4 = pg_fetch_array($result4)){
	$employee8[]=$row4;
}
$query5=("SELECT \"firstname\", \"lastname\", \"employeeID\" ".
		 "FROM \"employeeDB\" ".
		 "WHERE \"employeeID\" = '".$whoop[0]['employeeID']."'");
if(!($result5=pg_query($connection,$query5))){
	print("Failed query5: " . pg_last_error($connection));
	exit;
}
while($row5 = pg_fetch_array($result5)){
	$employee7[]=$row5;
}
$query6=("SELECT * ".
		 "FROM \"billingcodes\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER BY \"description\" ASC");
if(!($result6=pg_query($connection,$query6))){
	print("Failed query6: " . pg_last_error($connection));
	exit;
}
while($row6 = pg_fetch_array($result6)){
	$billingcodes[]=$row6;
}
$query7=("SELECT * ".
		 "FROM \"billingcodes\" ".
		 "WHERE \"ID\" = '".$whoop[0]['itemid']."'");
if(!($result7=pg_query($connection,$query7))){
	print("Failed query7: " . pg_last_error($connection));
	exit;
}
while($row7 = pg_fetch_array($result7)){
	$billingcodes1[]=$row7;
}
if($debug == "on"){
	echo "count whoop IS ".count($whoop)."<br>";
	echo "count client8 IS ".count($client8)."<br>";
	echo "count client7 IS ".count($client7)."<br>";
	echo "count employee8 IS ".count($employee8)."<br>";
	echo "count employee7 IS ".count($employee7)."<br>";
	echo "count billingcodes IS ".count($billingcodes)."<br>";
	echo "count billingcodes1 IS ".count($billingcodes1)."<br>";
}
$commonth1=date("F", $whoop[0]['completiondate']);
$commonth2=date("m", $whoop[0]['completiondate']);
$comday1=date("d", $whoop[0]['completiondate']);
$comday2=date("d", $whoop[0]['completiondate']);
$comyear1=date("Y", $whoop[0]['completiondate']);
$comyear2=date("Y", $whoop[0]['completiondate']);
echo "<font face=\"arial\">";
echo "<blockquote>";
echo "<font face=\"arial\" size=\"+2\"><b><center>Edit Workorder</center></b></font>";
echo "<p>";
echo "<form action=\"work.edit1.php\" method=\"post\">";
echo "<table align=\"center\">";
echo "<tr>";
echo "<td>Choose Client:</td>";
echo "<td><select name=\"cID\">";
echo "<option value=\"".$client7[0]['ID']."\">".$client7[0]['client']."</option>";
for($i=0; $i < count($client8); $i++){
	echo "<option value=\"".$client8[$i]['ID']."\">".$client8[$i]['client']."</option>";
}
echo "</select></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Person Assigned To:</td>";
echo "<td><select name=\"employee\">";
echo "<option value=\"".$employee7[0]['employeeID']."\">".$employee7[0]['firstname']." ".$employee7[0]['lastname']."</option>";
for($i=0; $i < count($employee8); $i++){
	echo "<option value=\"".$employee8[$i]['employeeID']."\">".$employee8[$i]['firstname']." ".$employee8[$i]['lastname']."</option>";
}
echo "</select></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Type of Work to be done:</td>";
echo "<td><select name=\"workid\">";
echo "<option value=\"".$billingcodes1[0]['ID']."\">".$billingcodes1[0]['description']." - ".$billingcodes1[0]['price']."</option>";
for($i=0; $i < count($billingcodes); $i++){
	echo "<option value=\"".$billingcodes[$i]['ID']."\">".$billingcodes[$i]['description']." - ".$billingcodes[$i]['price']."</option>";
}
echo "</select></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Estimated Number of Hours:</td>";
echo "<td><input type=\"text\" name=\"hours\" value=\"".$whoop[0]['hours']."\" size=\"3\" maxlength=\"6\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Completion Date:</td>";
echo "<td><select name=\"commonth\">";
echo "<option value=\"$commonth2\">$commonth1</option>";
echo "<option value=\"month\">Month</option>";
for($i=1; $i <= 12; $i++){
	$month1=date("F", mktime(1, 1, 1, $i, date("d"), date("Y")));
	$month2=date("m", mktime(1, 1, 1, $i, date("d"), date("Y")));
	echo "<option value=\"$month2\">$month1</option>";
}
echo "</select> / ";
echo "<select name=\"comday\">";
echo "<option value=\"$comday2\">$comday1</option>";
echo "<option value=\"day\">Day</option>";
for($i=1; $i <= 31; $i++){
	$day1=date("d", mktime(1, 1, 1, date("m"), $i, date("Y")));
	echo "<option value=\"$day1\">$day1</option>";
}
echo "</select> / ";
echo "<select name=\"comyear\">";
echo "<option value=\"$comyear2\">$comyear1</option>";
echo "<option value=\"year\">Year</option>";
for($i=0; $i <= 2; $i++){
	$year1=date("Y", mktime(1, 1, 1, date("d"), date("m"), date("Y")+$i));
	echo "<option value=\"$year1\">$year1</option>";
}
echo "</select>";
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td>Job Notes:</td>";
echo "<td><textarea wrap=\"physical\" name=\"jobnotes\" rows=\"7\" cols=\"35\">".$whoop[0]['jobnotes']."</textarea></td>";
echo "</tr>";
echo "<tr>";
echo "<td><input type=\"hidden\" name=\"code\" value=\"$code\"></td>";
echo "<td><input type=\"submit\" name=\"submit\" value=\" Enter Workorder \"></td>";
echo "</tr>";
echo "</table>";
echo "</form>";
require('../trailer.php');
?>
