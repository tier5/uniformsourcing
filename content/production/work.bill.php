<?php
require('Application.php');
$wodate=date("m/d/Y");
$code=$_POST['code'];
if($debug == "on"){
	echo "code IS $code<br>";
}
$query1=("SELECT \"ID\", \"clientID\", \"employeeID\", \"itemid\", \"hours\", \"total\", \"whoassigned\", \"jobnotes\", \"dateassigned\", \"completiondate\", \"reassigned\" ".
		 "FROM \"work1\" ".
		 "WHERE \"ID\" = '$code'");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
$query2=("SELECT * ".
		 "FROM \"billingcodes\" ".
		 "WHERE \"ID\" = '".$data1[0]['itemid']."'");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2)){
	$data2[]=$row2;
}
if($debug == "on"){
	echo "count data1 IS ".count($data1)."<br>";
	echo "count data2 IS ".count($data2)."<br>";
}
$start=$data1[0]['dateassigned'];
$end=$data1[0]['completiondate'];
$wodate=mktime();
$cost= $data2[0]['price'] * $data1[0]['hours'];
if($data1[0]['reassigned'] != ""){
	$reassigned=$data1[0]['reassigned'];
}else{
	$reassigned=$data1[0]['dateassigned'];
}
$query3=("INSERT INTO \"timelog\" ".
		 "(\"workid\", \"employee\", \"wodate\", \"start\", \"end\", \"total\", \"description\", \"billed\", \"client\", \"cost\", \"rate\", \"reassigned\") ".
		 "VALUES ('".$data1[0]['itemid']."', '".$data1[0]['employeeID']."', '$wodate', '$start', '$end', '".$data1[0]['total']."', '".$data1[0]['jobnotes']."', 'no', '".$data1[0]['clientID']."', '$cost', '".$data2[0]['price']."', '$reassigned')");
if(!($result3=pg_query($connection,$query3))){
	print("Failed query3: " . pg_last_error($connection));
	exit;
}
$datebilled=$wodate;
$query4=("UPDATE \"work1\" ".
		 "set \"open\" = 'yes', ".
		 "\"datebilled\" = '$datebilled' ".
		 "WHERE \"ID\" = '$code'");
if(!($result4=pg_query($connection,$query4))){
	print("Failed query4: " . pg_last_error($connection));
	exit;
}
$query5=("SELECT \"employeeID\", \"firstname\", \"lastname\", \"email\" ".
		 "FROM \"employeeDB\" ".
		 "WHERE \"employeeID\" = '".$data1[0]['employeeID']."'");
if(!($result5=pg_query($connection,$query5))){
	print("Failed query5: " . pg_last_error($connection));
	exit;
}
while($row5 = pg_fetch_array($result5)){
	$data5[]=$row5;
}
$query6=("SELECT \"ID\", \"clientID\", \"client\" ".
		 "FROM \"clientDB\" ".
		 "WHERE \"ID\" = '".$data1[0]['clientID']."'");
if(!($result6=pg_query($connection,$query6))){
	print("Failed query6: " . pg_last_error($connection));
	exit;
}
while($row6 = pg_fetch_array($result6)){
	$data6[]=$row6;
}
$query7=("SELECT \"employeeID\", \"email\" ".
		 "FROM \"employeeDB\" ".
		 "WHERE \"employeeID\" = '".$data1[0]['whoassigned']."'");
if(!($result7=pg_query($connection,$query7))){
	print("Failed query7: " . pg_last_error($connection));
	exit;
}
while($row7 = pg_fetch_array($result7)){
	$data7[]=$row7;
}
if($debug == "on"){
	echo "count data5 IS ".count($data5)."<br>";
	echo "count data6 IS ".count($data6)."<br>";
	echo "count data7 IS ".count($data7)."<br>";
}
require($PHPLIBDIR.'mailfunctions.php');
$headers=create_smtp_headers("A workorder for client ".$data6[0]['client']." that was assigned to ".$data5[0]['firstname']." ".$data5[0]['lastname']." was completed", "workorders@i2net.com", $data5[0]['email'], "Workorder System","","text/html");
$data=$headers. "<html>".
"<ul>".
"<li><b>Client Name:</b>".$data6[0]['client']."".
"<li><b>Type of Work:</b>".$data2[0]['description']."".
"<li><b>Total Hours:</b>".$data1[0]['hours']."".
"<li><b>Completion Date:</b>".date("m/d/Y")."".
"<li><b>Person that closed W/O:</b>".$_SESSION['firstname']." ".$_SESSION['lastname']."".
"<li><b>Person Assigned to:</b>".$data5[0]['firstname']." ".$data5[0]['lastname']."".
"<li><b>Job Notes:</b><br><hr width=\"50%\">".
"<p>".$data1[0]['jobnotes']."<hr width=\"50%\"><p>".
"</ul>".
"</html>";
$error="";
if((send_smtp("mail.i2net.com","workorders@i2net.com",$data5[0]['email'], $data)) === false){
	global $last_output;
	$error.="ERROR sending 1st message d00d. $last_output<br>";
	$exit=1;
}
if((send_smtp("mail.i2net.com","workorders@i2net.com",$data7[0]['email'], $data)) === false){
	global $last_output;
	$error.="ERROR sending 2nd message d00d. $last_output<br>";
	$exit=1;
}
if((send_smtp("mail.i2net.com","workorders@i2net.com",$_SESSION['email'], $data)) === false){
	global $last_output;
	$error.="ERROR sending 3rd message d00d. $last_output<br>";
	$exit=1;
}
if($exit==1){
	echo $error;
	exit;
}
header("location: ../index.php");
?>
