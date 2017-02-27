<?php
require('Application.php');
$clientnum=$_POST['clientnum'];
$employee=$_POST['employee'];
$workid=$_POST['workid'];
$hours=$_POST['hours'];
$commonth=$_POST['commonth'];
$comday=$_POST['comday'];
$comyear=$_POST['comyear'];
$jobnotes=$_POST['jobnotes'];
if($commonth == "month" OR $comday == "day" OR $comyear == "year"){
	require('../../header.php');
	echo "You entered in a wrong date. Please go back and correct it.";
	require('../../trailer.php');
	exit;
}
if($debug == "on"){
	require('../../header.php');
	echo "clientnum IS $clientnum<br>";
	echo "employee IS $employee<br>";
	echo "workid IS $workid<br>";
	echo "hours IS $hours<br>";
	echo "commonth IS $commonth<br>";
	echo "comday IS $comday<br>";
	echo "conyear IS $comyear<br>";
	echo "jobnotes IS $jobnotes<br>";
}
$comdate=mktime(0, 0, 0, $commonth, $comday, $comyear);
$checkdate=mktime(0, 0, 0, date("m"), date("d")+2, date("Y"));
if($debug == "on"){
	echo "comdate IS $comdate <br>";
	echo "comdate DATE IS".date("m/d/Y", $comdate)."<br>";
	echo "checkdate IS $checkdate <br>";
	echo "checkdate DATE IS ".date("m/d/Y", $checkdate)."<br>";
	echo "checkdate should be lessthan comdate for it to go<br>";
}
if($checkdate > $comdate){
	require('../../header.php');
	echo "You entered a Date that was less than 2 days from today. You need to give at least 2 days for the work order to be completed.<br>";
	echo "Please go back and put in a reasonable date for the workorder to be completed.";
	require('../../trailer.php');
	exit;
}
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
$query3=("SELECT \"clientID\", \"client\" ".
		 "FROM \"clientDB\" ".
		 "WHERE \"ID\" = '$clientnum'");
if(!($result3=pg_query($connection,$query3))){
	print("Failed query: " . pg_last_error($connection));
	exit;
}
while($row3 = pg_fetch_array($result3)){
	$data3[]=$row3;
}
$whoassigned=$_SESSION['employeeID'];
$total = 60 * $hours;
$completiondate=$comdate;
$dateassigned=mktime(0, 0, 0, date("m"), date("d"), date("Y"));
$open = "no";
$query4=("INSERT INTO \"work1\" ".
		 "(\"clientID\", \"employeeID\", \"itemid\", \"hours\", \"total\", \"whoassigned\", \"dateassigned\", \"completiondate\", \"jobnotes\", \"open\") ".
		 "VALUES ('$clientnum', '$employee', '$workid', '$hours', '$total', '$whoassigned', '$dateassigned', '$completiondate', '$jobnotes', '$open')");
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
