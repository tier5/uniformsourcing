<?php
$appdir="../";
require('../../Application.php');
session_start();
$inv_pass="3264";
$mydirectory="..";
$upload_dir = $mydirectory."/uploadFiles/inventory/";
$upload_dir_image = $mydirectory."/uploadFiles/inventory/images/";
$upload_dir_general = $mydirectory."/uploadFiles/inventory/general/";
/*if(isset($_SESSION['perm_admin']) AND $_SESSION['perm_admin'] == "on"){
}else{
	require('../header.php');
	echo "<body bgcolor=\"#FFFFFF\">";
	echo "<br><br>";
	echo "<center>";
	echo "<font face=\"arial\">";
	echo "<b>The User ".$_SESSION['firstname']." ".$_SESSION['lastname']." does not have access to this area.</b>";
	echo "</font>";
	echo "</center>";
	echo "</body>";
	require('../trailer.php');
	exit;
}*/
if(isset($_POST['username']) OR $_POST['username'] != ""){
  $USERNAME = $username;
// set errors catch for non set password and username
if($username == "" AND $password == ""){
	header("location: ".${appdir}."../login.php?error=both");
	exit;
}elseif($username == ""){
	header("location: ../login.php?error=nu");
	exit;
}elseif($password == ""){
	header("location: ../login.php?error=np");
	exit;
}
// now that we are sure there was a value posted for username and password build query
$queryapp1=("SELECT e.\"employeeID\" as \"employeeID\", ".
			"e.\"username\" as \"username\", ".
			"e.\"password\" as \"password\", ".
			"e.\"firstname\" as \"firstname\", ".
			"e.\"lastname\" as \"lastname\", ".
			"e.\"email\" as \"email\",  ".
			"p.\"login\" as \"plogin\", ".
			"e.\"employeeType\" as \"employeeType\",  ".
			"e.employee_type_id as employee_type_id ".
			"FROM \"employeeDB\" e, \"permissions\" p ".
			"WHERE username = '$username' AND password = '$password' AND e.\"employeeID\" = p.\"employee\" AND p.\"login\" = 'on' ");
if(!($resultapp1=pg_query($connection,$queryapp1))){
	print("Failed resultapp1: " . pg_last_error($connection));
	exit;
}
while($rowapp1 = pg_fetch_array($resultapp1)){
	    $dataapp1[]=$rowapp1;
}
if(count($dataapp1) > 1){
	echo "Something went Wrong, there were more than 1 match for that username and password";
	exit;
}elseif(count($dataapp1) < 1){
	header("location: ".${appdir}."../login.php?error=both");
	exit;
}
// Set session variables if not set already
//if(!isset($_SESSION['EmployeeID'])) {
$_SESSION['employee_type_id']=$dataapp1[0]['employee_type_id'];
$_SESSION['employeeType']=$dataapp1[0]['employeeType'];
$_SESSION['employeeID']=$dataapp1[0]['employeeID'];
$_SESSION['username']=$dataapp1[0]['username'];
$_SESSION['password']=$dataapp1[0]['password'];
$_SESSION['firstname']=$dataapp1[0]['firstname'];
$_SESSION['lastname']=$dataapp1[0]['lastname'];
$_SESSION['email']=$dataapp1[0]['email'];
//}
if(isset($_SESSION['count'])){
	$_SESSION['count']++;
}else{
	$_SESSION['count']=1;
}
// set up query for permissions 
$eid=$_SESSION['employeeID'];
$queryapp=("SELECT \"ID\", \"employee\", \"admin\", \"accounting\", \"humanresources\", \"directory\", \"calendar\", \"production\", \"sales\", \"purchasing\", \"support\", \"operations\", \"external\", \"tracking\" ".
		 "FROM \"permissions\" ".
		 "WHERE \"employee\" = '$eid'");
if(!($resultapp=pg_query($connection,$queryapp))){
	print("Failed queryapp: " . pg_last_error($connection));
	exit;
}
if(count($resultapp) > 1 OR count($resultapp) < 1){
	echo "Something went wrong, there were more than 1 match for the permissions for". $_SESSION['username'] ."with EmployeeID ".$_SESSION['EmployeeID']." ";
	exit;
}
while($rowapp = pg_fetch_array($resultapp)){
	$dataapp[]=$rowapp;
}
$_SESSION['perm_ID']=$dataapp[0]['ID'];
$_SESSION['perm_accounting']=$dataapp[0]['accounting'];
$_SESSION['perm_admin']=$dataapp[0]['admin'];
$_SESSION['perm_humanresources']=$dataapp[0]['humanresources'];
$_SESSION['perm_directory']=$dataapp[0]['directory'];
$_SESSION['perm_calendar']=$dataapp[0]['calendar'];
$_SESSION['perm_production']=$dataapp[0]['production'];
$_SESSION['perm_sales']=$dataapp[0]['sales'];
$_SESSION['perm_purchasing']=$dataapp[0]['purchasing'];
$_SESSION['perm_support']=$dataapp[0]['support'];
$_SESSION['perm_operations']=$dataapp[0]['operations'];
$_SESSION['perm_external']=$dataapp[0]['external'];
$_SESSION['perm_tracking']=$dataapp[0]['tracking'];  
}
else{
    $_SESSION['employeeID']=0;
}
?>
