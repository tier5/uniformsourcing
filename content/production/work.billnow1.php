<?php
require('Application.php');
require('../header.php');
$query1=("SELECT \"ID\", \"clientID\", \"client\", \"active\" ".
		 "FROM \"clientDB\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER by \"client\" ASC");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$client8[]=$row1;
}
$query2=("SELECT \"firstname\", \"lastname\", \"employeeID\" ".
		 "FROM \"employeeDB\" ".
		 "WHERE \"employeeID\" = '".$_SESSION['employeeID']."' AND \"active\" = 'yes'");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2)){
	$employee8[]=$row2;
}
$query3=("SELECT * ".
		 "FROM \"billingcodes\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER BY \"description\" ASC");
if(!($result3=pg_query($connection,$query3))){
	print("Failed query3: " . pg_last_error($connection));
	exit;
}
while($row3 = pg_fetch_array($result3)){
	$billingcodes[]=$row3;
}
echo "<font face=\"arial\">";
echo "<blockquote>";
echo "<font face=\"arial\" size=\"+2\"><b><center>Edit Workorder</center></b></font>";
echo "<p>";
echo "<form action=\"work.billnow2.php\" method=\"post\">";
echo "<table align=\"center\">";
echo "<tr>";
echo "<td>Choose Client:</td>";
echo "<td><select name=\"cID\">";
for($i=0; $i < count($client8); $i++){
	echo "<option value=\"".$client8[$i]['ID']."\">".$client8[$i]['client']."</option>";
}
echo "</select></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Person Assigned To:</td>";
echo "<td><select name=\"employee\">";
for($i=0; $i < count($employee8); $i++){
	echo "<option value=\"".$employee8[$i]['employeeID']."\">".$employee8[$i]['firstname']." ".$employee8[$i]['lastname']."</option>";
}
echo "</select></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Type of Work to be done:</td>";
echo "<td><select name=\"workid\">";
for($i=0; $i < count($billingcodes); $i++){
	echo "<option value=\"".$billingcodes[$i]['ID']."\">".$billingcodes[$i]['description']." - ".$billingcodes[$i]['price']."</option>";
}
echo "</select></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Estimated Number of Hours:</td>";
echo "<td><input type=\"text\" name=\"hours\" value=\"$hours\" size=\"3\" maxlength=\"6\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Completion Date:</b></td>";
echo "<td><select name=\"commonth\">";
$month1=date("F", mktime());
$month2=date("m", mktime());
echo "<option value=\"$month2\">$month1</option>";
echo "</select> / ";
echo "<select name=\"comday\">";
$day1=date("d", mktime());
echo "<option value=\"$day1\">$day1</option>";
echo "</select> / ";
echo "<select name=\"comyear\">";
$year1=date("Y", mktime());
echo "<option value=\"$year1\">$year1</option>";
echo "</select>";
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td>Job Notes:</td>";
echo "<td><textarea wrap=\"physical\" name=\"jobnotes\" rows=\"7\" cols=\"35\">$jobnotes</textarea></td>";
echo "</tr>";
echo "<tr>";
echo "<td><input type=\"submit\" name=\"submit\" value=\" Immediate Bill \"></td>";
echo "</tr>";
echo "</table>";
echo "</form>";
require('../trailer.php');
?>
