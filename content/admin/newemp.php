<?php
require('Application.php');
if(!isset($_POST['monthhirednew']) OR $_POST['monthhirednew'] == "month"){
	$error="You forgot to enter the Month Hired.<br>";
}
if(!isset($_POST['dayhirednew']) OR $_POST['dayhirednew'] == "day"){
	$error.="You forgot to enter the Day Hired.<br>";
}
if(!isset($_POST['yearhirednew']) OR $_POST['yearhirednew'] == "year"){
	$error.="You forgot to enter the Year Hired.<br>";
}
if(!isset($_POST['firstnamenew']) OR $_POST['firstnamenew'] == ""){
	$error.="You forgot to enter the First Name of the person.<br>";
}
if(!isset($_POST['lastnamenew']) OR $_POST['lastnamenew'] == "") {
   $error.="You forgot to enter the Last Name of the person.<br>";
}
if(!isset($_POST['addressnew']) OR $_POST['addressnew'] == ""){
	$error.="You forgot to enter the Address of the person.<br>";
}
if(!isset($_POST['citynew']) OR $_POST['citynew'] == ""){
	$error.="You forgot to enter the city of the person.<br>";
}
if(!isset($_POST['statenew']) OR $_POST['statenew'] == ""){
	$error.="You forgot to enter the State of the person.<br>";
}
if(!isset($_POST['zipnew']) OR $_POST['zipnew'] == ""){
	$error.="You forgot to enter the Zip of the person.<br>";
}
if(!isset($_POST['phonenew']) OR $_POST['phonenew'] == ""){
	$error.="You forgot to enter the Phone of the person.<br>";
}
if(!isset($_POST['cellnew']) OR $_POST['cellnew'] == ""){
	$error.="You forgot to enter the Cell of the person.<br>";
}
if(!isset($_POST['emailnew']) OR $_POST['emailnew'] == ""){
	$error.="You forgot to enter the email address of the person.<br>";
}
if(!isset($_POST['newusername']) OR $_POST['newusername'] == ""){
	$error.="You forgot to enter the username of the person.<br>";
}
if(!isset($_POST['newpassword']) OR $_POST['newpassword'] == ""){
	$error.= "You forgot to enter the password of the person.<br>";
}
if(isset($error)){
	require('error.php');
	exit;
}
extract($_POST);
/*$monthhired=$_POST['monthhirednew'];
$dayhired=$_POST['dayhirednew'];
$yearhired=$_POST['yearhirednew'];
$firstname1=$_POST['firstnamenew'];
$lastname1=$_POST['lastnamenew'];
$title=$_POST['titlenew'];
$address=$_POST['addressnew'];
$city=$_POST['citynew'];
$state=$_POST['statenew'];
$zip=$_POST['zipnew'];
$phone=$_POST['phonenew'];
$pager=$_POST['pagernew'];
$alphapager=$_POST['alphapagernew'];
$cell=$_POST['cellnew'];
$email=$_POST['emailnew'];
$salary=$_POST['salarynew'];
$wage=$_POST['wagenew'];
$usernamenew=$_POST['newusername'];
$passwordnew=$_POST['newpassword'];
$poppassword=$_POST['newpoppassword'];*/
$datehired=mktime(0, 0, 0, $monthhired, $dayhired, $yearhired);
$check=("SELECT \"username\" ".
		"FROM \"employeeDB\" ".
		"WHERE \"username\" = '$newusername' ");
if(!($checkresult=pg_query($connection,$check))){
	print("Failed check: " . pg_last_error($connection));
	exit;
}
while($rowcheck = pg_fetch_array($checkresult)) {
	$datacheck[]=$rowcheck;
}
$_SESSION['newusername']=$_POST['newusername'];
if(count($datacheck) > 0){
	require('../header.php');
	echo "The username you chose is already in use. Please go back and choose another.<br>";
	echo $datacheck[0]['username'];
	require('../trailer.php');
	exit;
}
$titlenew=pg_escape_string($titlenew);
$addressnew=pg_escape_string($addressnew);
$querya="INSERT INTO \"employeeDB\" ".
		 "(\"firstname\", \"lastname\", \"title\", \"address\", \"phone\", \"pager\", \"alphapager\", \"cell\", \"email\", \"username\",
		   \"password\", \"city\", \"state\", \"zip\", \"datehired\"";
		  if($salarynew!="") $querya.=",\"salary\" ";
		  if($newpoppassword!="") $querya.=",\"poppassword\" ";
		  if($wagenew!="") $querya.=",\"wage\" ";
		  if($wagenew!="") $querya.=",\"wage\" ";
		  $querya.=",\"active\"";
		 if($employeeType!="")$querya.=", \"employeeType\" ";
		 if($employeeType !="" && $employeeType == 1)$querya.=", employee_type_id ";
		 else if($employeeType !="" && $employeeType == 2)$querya.=", employee_type_id ";
		  $querya.=")";
		 $querya.="VALUES ".
		 "('$firstnamenew', '$lastnamenew', '$titlenew', '$addressnew', '$phonenew', '$pagernew', '$alphapagernew','$cellnew','$emailnew','$newusername',
		   '$newpassword', '$citynew', '$statenew', '$zipnew', '$datehired'";
		if($salarynew!="")$querya.=", '$salarynew'";
		if($wagenew!="")$querya.=", '$wagenew'";
		if($newpoppassword!="")$querya.=",'$newpoppassword'";
		$querya.=",'yes'";
		if($employeeType!="") $querya.=",'$employeeType'";
		if($employeeType !="" && $employeeType == 1) $querya.=",'$vendorName'";
		else if($employeeType !="" && $employeeType == 2) $querya.=",'$clientname'";
		$querya.=") ";
	//	 echo $querya;
if(!($resulta=pg_query($connection,$querya))){
	print("Failed querya: " . pg_last_error($connection));
	exit;
}
require('../header.php');
echo "<form action=\"newemp1.php\" method=\"post\">";
echo "<table width=\"80%\">";
echo "<tr>";
echo "<td colspan=\"5\" bgcolor=\"white\"><b>$firstnamenew $lastnamenew's Permissions</b></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font face=\"arial\" size=\"-1\">Accounting</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">Administration</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">Human Resources</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">Internal Directory</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">Office Calendar</font></td>";
echo "</tr>";
echo "<tr>";
echo "<td><input type=\"Checkbox\" name=\"accounting\"></td>";
echo "<td><input type=\"Checkbox\" name=\"admin\"></td>";
echo "<td><input type=\"Checkbox\" name=\"humanresources\"></td>";
echo "<td><input type=\"Checkbox\" name=\"internaldirectory\"></td>";
echo "<td><input type=\"Checkbox\" name=\"calendar\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font face=\"arial\" size=\"-1\">Operations</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">Sales</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">Support</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">Production</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">Purchasing</font></td>";
echo "</tr>";
echo "<tr>";
echo "<td><input type=\"Checkbox\" name=\"operations\"></td>";
echo "<td><input type=\"Checkbox\" name=\"sales\"></td>";
echo "<td><input type=\"Checkbox\" name=\"support\"></td>";
echo "<td><input type=\"Checkbox\" name=\"production\"></td>";
echo "<td><input type=\"Checkbox\" name=\"purchasing\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font face=\"arial\" size=\"-1\">External User</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">Able to Login</font></td>";
echo "</tr>";
echo "<tr>";
echo "<td><input type=\"Checkbox\" name=\"external\"></td>";
echo "<td><input type=\"Checkbox\" name=\"login\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td colspan=5 align=\"center\">";
echo "<br><br>";
echo "<input type=\"hidden\" name=\"usernamenew\" value=\"$usernamenew\">";
echo "<INPUT TYPE=\"Submit\" VALUE=\"  Enter Employee Permissions   \"></td>";
echo "</tr>";
echo "</table>";
require('../trailer.php');
?>
