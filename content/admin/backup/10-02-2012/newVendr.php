<?php
require('Application.php');
if(!isset($_POST['vendorName']) OR $_POST['vendorName'] == "") {
   $error.="You forgot to enter the Vendor Name .<br>";
}
if(!isset($_POST['addressnew']) OR $_POST['addressnew'] == ""){
	$error.="You forgot to enter the Address of Vendor.<br>";
}
if(!isset($_POST['citynew']) OR $_POST['citynew'] == ""){
	$error.="You forgot to enter the city of Vendor.<br>";
}
if(!isset($_POST['statenew']) OR $_POST['statenew'] == ""){
	$error.="You forgot to enter the State of Vendor.<br>";
}
if(!isset($_POST['zipnew']) OR $_POST['zipnew'] == ""){
	$error.="You forgot to enter the Zip of Vendor.<br>";
}
if(!isset($_POST['country']) OR $_POST['country'] == ""){
	$error.="You forgot to enter the Country of Vendor.<br>";
}
if(!isset($_POST['phonenew']) OR $_POST['phonenew'] == ""){
	$error.="You forgot to enter the Phone of Vendor.<br>";
}
if(!isset($_POST['cellnew']) OR $_POST['cellnew'] == ""){
	$error.="You forgot to enter the Cell of Vendor.<br>";
}
if(!isset($_POST['emailnew']) OR $_POST['emailnew'] == ""){
	$error.="You forgot to enter the email address of Vendor.<br>";
}
if(!isset($_POST['newusername']) OR $_POST['newusername'] == ""){
	$error.="You forgot to enter the Username.<br>";
}
if(!isset($_POST['newpassword']) OR $_POST['newpassword'] == ""){
	$error.="You forgot to enter the Password.<br>";
}
if(isset($error)){
	require('error.php');
	exit;
}
$vendorName=$_POST['vendorName'];
$address=$_POST['addressnew'];
$city=$_POST['citynew'];
$state=$_POST['statenew'];
$zip=$_POST['zipnew'];
$country=$_POST['country'];
$account=$_POST['account'];
$phone=$_POST['phonenew'];
$pager=$_POST['pagernew'];
$alphapager=$_POST['alphapagernew'];
$cell=$_POST['cellnew'];
$email=$_POST['emailnew'];
$username=$_POST['newusername'];
$password=$_POST['newpassword'];
$www=$_POST['www'];
$notes=$_POST['notes'];

$check=("SELECT \"vendorName\",\"email\" ".
		"FROM \"vendor\" ".
		"WHERE \"vendorName\" = '$vendorName' and \"email\" = '$email'");
		
if(!($checkresult=pg_query($connection,$check))){
	print("Failed check: " . pg_last_error($connection));
	exit;
}
while($rowcheck = pg_fetch_array($checkresult)) {
	$datacheck[]=$rowcheck;
}
if(count($datacheck) > 0){
	require('../header.php');
	echo "The Vendor Name And Vendor Email already in use. Please go back and choose another.<br>";
	echo "Vendor name : ". $datacheck[0]['vendorName']. "<br>";
	echo "Vendor Email : ".$datacheck[0]['email'];
	require('../trailer.php');
	exit;
}
if(!count($datacheck)>0)
{
		$querya=("INSERT INTO \"vendor\" "."(  \"address\", \"phone\", \"pager\", \"alphapager\", \"cell\", \"email\",\"city\", \"state\", \"zip\",                  \"active\",\"vendorName\",\"www\",\"country\",\"notes\",\"accountNumber\",\"username\",\"password\") "."VALUES ". "(  '$address', '$phone',                   '$pager','$alphapager', '$cell','$email','$city','$state',                                                                             '$zip','yes','$vendorName','$www','$country','$notes','$account','$username' ,'$password')"); 
																																																																						                                                                    
		if(!($resulta=pg_query($connection,$querya))){
			print("Failed querya: " . pg_last_error($connection));
			exit;
			}
}
//require('../header.php');
header("location: ./index.php");
//require('../trailer.php');
?>
