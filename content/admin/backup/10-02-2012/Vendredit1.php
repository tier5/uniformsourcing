<?php
require('Application.php');
if($debug == "on"){
	require('../../header.php');
	foreach($_POST as $key=>$value) {
		if($key!="submit") { echo "$key = $value<br/>"; }
	}
}
$vendorID=$_SESSION['vendorID'];
$vendorName=$_POST['vendorName'];
$city=$_POST['city'];
$state=$_POST['state'];
$address=$_POST['address'];
$zip=$_POST['zip'];
$country=$_POST['country'];
$account=$_POST['account'];
$phone=$_POST['phone'];
$pager=$_POST['pager'];
$alphapager=$_POST['alphapager'];
$cell=$_POST['cell'];
$email=$_POST['email'];
$username=$_POST['newusername'];
$password=$_POST['newpassword'];
$www=$_POST['www'];
$notes=$_POST['notes'];
//$vendorID=$_POST['vendorID'];
if(isset($_POST['EditVendors']))
{
		$query1="UPDATE \"vendor\" SET ";
		if($vendorName)	$query1.= "\"vendorName\" = '$vendorName', ";
		if($address)$query1.= "\"address\" = '$address', ";
		if($phone)$query1.= "\"phone\" = '$phone', ";
		if($pager)$query1.= "\"pager\" = '$pager', ";
		if($alphapager)$query1.= "\"alphapager\" = '$alphapager', ";
		if($cell)$query1.= "\"cell\" = '$cell', ";
		if($email)$query1.=  "\"email\" = '$email', ";
		if($city)$query1.="\"city\" = '$city', ";
	    if($state)$query1.= "\"state\" = '$state', ";
	    if($zip)$query1.="\"zip\" = '$zip', ";
		if($www)$query1.= "\"www\" = '$www', ";
		if($country)$query1.= "\"country\" = '$country', ";
		if($notes)$query1.= "\"notes\" = '$notes', ";
		if($account)$query1.= "\"accountNumber\" = '$account', ";
		if($username)$query1.= "\"username\" = '$username', ";
		if($password)$query1.= "\"password\" = '$password', ";
		$query1.= "\"active\" = 'yes' ";
		$query1.= "WHERE \"vendorID\" = $vendorID ";//echo $query1;
		if(!($result1=pg_query($connection,$query1))){
			print("Failed query1: " . pg_last_error($connection));
			exit;
		}
}
if(isset($_POST['EditVendors']))
header("location: editVendr.php");
require('../trailer.php');
?>