<?php
require('Application.php');
$cID=$_POST['cID']; // client ID clientDB
$employee=$_POST['employee']; // employeeID from employeeDB based on session employeeID
$workid=$_POST['workid']; // billingcodes ID
$hours=$_POST['hours']; // hours worked
$commonth=$_POST['commonth'];
$comday=$_POST['comday'];
$comyear=$_POST['comyear'];
$jobnotes=$_POST['jobnotes'];
if($debug == "on"){
	echo "cID IS $cID<br>";
	echo "employee IS $employee<br>";
	echo "workid IS $workid<br>";
	echo "hours IS $hours<br>";
	echo "commonth IS $commonth<br>";
	echo "comday IS $comday<br>";
	echo "comyear IS $comyear<br>";
	echo "jobnotes IS $jobnotes<br>";
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
$headers=create_smtp_headers("A workorder for client ".$data3[0]['client']." that was assigned to ".$data2[0]['firstname']." ".$data2[0]['lastname']." was completed", "workorders@i2net.com", $data2[0]['email'], "Workorder System","","text/html");
$data=$headers. "<html>".
"<ul>".
"<li><b>Client Name:</b>".$data3[0]['client']."".
"<li><b>Type of Work:</b>".$data1[0]['description']."".
"<li><b>Total Hours:</b>".$hours."".
"<li><b>Completion Date:</b>".date("m/d/Y")."".
"<li><b>Person that closed W/O:</b>".$_SESSION['firstname']." ".$_SESSION['lastname']."".
"<li><b>Person Assigned to:</b>".$data2[0]['firstname']." ".$data2[0]['lastname']."".
"<li><b>Job Notes:</b><br><hr width=\"50%\">".
"<p>$jobnotes<hr width=\"50%\"><p>".
"</ul>".
"</html>";
$error="";
if((send_smtp("mail.i2net.com","workorders@i2net.com",$data5[0]['email'], $data)) === false){
	global $last_output;
	$error.="ERROR sending 1st message d00d. $last_output<br>";
	$exit=1;
}
if($exit==1){
	echo $error;
	exit;
}
header("location: ../index.php");
?>
