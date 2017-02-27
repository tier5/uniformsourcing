<?php
require('Application.php');
$cID=$_POST['cID'];
$employee=$_POST['employee'];
$workid=$_POST['workid'];
$hours=$_POST['hours'];
$commonth=$_POST['commonth'];
$comday=$_POST['comday'];
$comyear=$_POST['comyear'];
$jobnotes=$_POST['jobnotes'];
$code=$_POST['code'];
if($debug == "on"){
	echo "cID IS $cID<br>";
	echo "employee IS $employee<br>";
	echo "workid IS $workid<br>";
	echo "hours IS $hours<br>";
	echo "commonth IS $commonth<br>";
	echo "comday IS $comday<br>";
	echo "comyear IS $comyear<br>";
	echo "jobnotes IS $jobnotes<br>";
	echo "code IS $code<br>";
}
if($commonth == "month" OR $comday == "day" OR $comyear == "year"){
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
			 "WHERE \"active\" = 'yes'");
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
	$query4=("SELECT \"ID\", \"clientID\", \"employeeID\", \"itemid\", \"hours\", \"completiondate\", \"jobnotes\" ".
			 "FROM \"work1\" ".
			 "WHERE \"ID\" = '$code'");
	if(!($result4=pg_query($connection,$query4))){
		print("Failed query4: " . pg_last_error($connection));
		exit;
	}
	while($row4 = pg_fetch_array($result4)){
		$whoop[]=$row4;
	}
	$query5=("SELECT \"ID\", \"client\" ".
			 "FROM \"clientDB\" ".
			 "WHERE \"ID\" = '$cID'");
	if(!($result5=pg_query($connection,$query5))){
		print("Failed query5: " . pg_last_error($connection));
		exit;
	}
	while($row5 = pg_fetch_array($result5)){
		$client7[]=$row5;
	}
	$query6=("SELECT \"employeeID\", \"firstname\", \"lastname\" ".
			 "FROM \"employeeDB\" ".
			 "WHERE \"employeeID\" = '$employee'");
	if(!($result6=pg_query($connection,$query6))){
		print("Failed query6: " . pg_last_error($connection));
		exit;
	}
	while($row6 = pg_fetch_array($result6)){
		$employee7[]=$row6;
	}
	$query7=("SELECT \"ID\", \"description\", \"price\" ".
			 "FROM \"billingcodes\" ".
			 "WHERE \"ID\" = '$workid'");
	if(!($result7=pg_query($connection,$query7))){
		print("Failed query7: " . pg_last_error($connection));
		exit;
	}
	while($row7 = pg_fetch_array($result7)){
		$billingcodes1[]=$row7;
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
	echo "<td><input type=\"text\" name=\"hours\" value=\"$hours\" size=\"3\" maxlength=\"6\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td><font color=\"red\"><b>Completion Date:</b></font></td>";
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
	echo "<td><textarea wrap=\"physical\" name=\"jobnotes\" rows=\"7\" cols=\"35\">$jobnotes</textarea></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td><input type=\"hidden\" name=\"code\" value=\"$code\"></td>";
	echo "<td><input type=\"submit\" name=\"submit\" value=\" Enter Workorder \"></td>";
	echo "</tr>";
	echo "</table>";
	echo "</form>";
	require('../trailer.php');
	exit;
}
$comdate=mktime(0, 0, 0, $commonth, $comday, $comyear);
$query1=("SELECT * ".
		 "FROM \"billingcodes\" ".
		 "WHERE \"ID\" = '$workid'");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
$query2=("SELECT \"employeeID\", \"email\", \"firstname\", \"lastname\" ".
		 "FROM \"employeeDB\" ".
		 "WHERE \"employeeID\" = '$employee'");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2)){
	$data2[]=$row2;
}
$query3=("SELECT \"ID\", \"clientID\", \"client\" ".
		 "FROM \"clientDB\" ".
		 "WHERE \"ID\" = '$cID'");
if(!($result3=pg_query($connection,$query3))){
	print("Failed query3: " . pg_last_error($connection));
	exit;
}
while($row3 = pg_fetch_array($result3)){
	$data3[]=$row3;
}
if($debug == "on"){
	echo "count query1 IS ".count($data1)."<br>";
	echo "count query2 IS ".count($data2)."<br>";
	echo "count query3 IS ".count($data3)."<br>";
}
$total= 60 * $hours;
$completiondate=$comdate;
$datenow=mktime(0, 0, 0, date("m"), date("d"), date("Y"));
$query4=("UPDATE \"work1\" ".
		 "set \"clientID\" = '$cID', ".
		 "\"employeeID\" = '$employee', ".
		 "\"itemid\" = '$workid', ".
		 "\"hours\" = '$hours', ".
		 "\"total\" = '$total', ".
		 "\"reassigned\" = '$datenow', ".
		 "\"completiondate\" = '$completiondate', ".
		 "\"jobnotes\" = '$jobnotes' ".
		 "WHERE \"ID\" = '$code'");
if(!($result4=pg_query($connection,$query4))){
	print("Failed query4: " . pg_last_error($connection));
	exit;
}
require($PHPLIBDIR.'mailfunctions.php');
$headers=create_smtp_headers("A workorder for client ".$data3[0]['client']." has been assigned to ".$data2[0]['firstname']." ".$data2[0]['lastname']."", "workorders@i2net.com", $data2[0]['email'], "Workorder System","","text/html");
$data=$headers. "<html>".
"<ul>".
"<li><b>Client Name:</b>".$data3[0]['client']."".
"<li><b>Type of Work:</b>".$data1[0]['description']."".
"<li><b>Estimated Hours:</b>".$hours."".
"<li><b>Expected Completion Date:</b>".date("m/d/Y", mktime(1, 1, 1, $commonth, $comday, $comyear))."".
"<li><b>Person that Assigned W/O:</b>".$_SESSION['firstname']." ".$_SESSION['lastname']."".
"<li><b>Person Assigned to:</b>".$data2[0]['firstname']." ".$data2[0]['lastname']."".
"<li><b>Job Notes:</b><br><hr width=\"50%\">".
"<p>$jobnotes<hr width=\"50%\"><p>".
"<li><b>Please log onto The intranet to review this open ticket, and remember to bill when you are completed with this item</b>".
"</ul>".
"</html>";
if((send_smtp("mail.i2net.com","workorders@i2net.com",$data2[0]['email'], $data)) === false){
	global $last_output;
	echo "ERROR sending message d00d. $last_output<br>";
	exit;
}
header("location: ../index.php");
?>
