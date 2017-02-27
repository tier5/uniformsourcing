<?php
require('Application.php');
require('../header.php');
$accounting=$_POST['accounting'];
$admin=$_POST['admin'];
$humanresources=$_POST['humanresources'];
$directory=$_POST['directory'];
$calendar=$_POST['calendar'];
$operations=$_POST['operations'];
$sales=$_POST['sales'];
$support=$_POST['support'];
$production=$_POST['production'];
$purchasing=$_POST['purchasing'];
$external=$_POST['external'];
$login=$_POST['login'];
$pid=$_POST['ID'];
$eid=$_POST['employeeID'];
if($debug == "on"){
	echo "accounting IS $accounting<br>";
	echo "admin IS $admin<br>";
	echo "humanresources IS $humanresources<br>";
	echo "directory IS $directory<br>";
	echo "calendar IS $calendar<br>";
	echo "operations IS $operations<br>";
	echo "sales IS $sales<br>";
	echo "support IS $support<br>";
	echo "production IS $production<br>";
	echo "purchasing IS $purchasing<br>";
	echo "external IS $external<br>";
	echo "login IS $login<br>";
	echo "pid IS $pid<br>";
	echo "eid IS $eid<br>";
}
if($accounting == "on"){
}else{
	$accounting="off";
}
if($admin == "on"){
}else{
	$admin="off";
}
if($humanresources == "on"){
}else{
	$humanresources="off";
}
if($directory == "on"){
}else{
	$directory="off";
}
if($calendar == "on"){
}else{
	$calendar="off";
}
if($operations == "on"){
}else{
	$operations="off";
}
if($sales == "on"){
}else{
	$sales="off";
}
if($support == "on"){
}else{
	$support="off";
}
if($production == "on"){
}else{
	$production="off";
}
if($purchasing == "on"){
}else{
	$purchasing="off";
}
if($external == "on"){
}else{
	$external="off";
}
if($login == "on"){
}else{
	$login="off";
}
$query1=("SELECT \"employeeID\", \"firstname\", \"lastname\" ".
		 "FROM \"employeeDB\" ".
		 "WHERE \"employeeID\" = '$eid' ");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1= pg_fetch_array($result1)){
	$data1[]=$row1;
}
$sql="Select count(0) from \"permissions\" where \"employee\"='$eid'";
if(!($result1=pg_query($connection,$sql))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1= pg_fetch_array($result1))
{
	$data=$row1;
}
pg_free_result($result1);
	if($data['count']>0)
	{
         $query2=("UPDATE \"permissions\" ".
		 "SET ".
		 "\"accounting\" = '$accounting', ".
		 "\"admin\" = '$admin', ".
		 "\"humanresources\" = '$humanresources', ".
		 "\"directory\" = '$directory', ".
		 "\"calendar\" = '$calendar', ".
		 "\"operations\" = '$operations', ".
		 "\"sales\" = '$sales', ".
		 "\"support\" = '$support', ".
		 "\"production\" = '$production', ".
		 "\"purchasing\" = '$purchasing', ".
		 "\"external\" = '$external', ".
		 "\"login\" = '$login', ".
		 "\"employee\" = '$eid' ".
		 "WHERE \"ID\" = '$pid' ");
	}
	else
	{
		$query2=("INSERT INTO \"permissions\" ".
		 "(\"accounting\", \"admin\", \"humanresources\", \"directory\", \"calendar\", \"operations\", \"sales\", \"support\", \"production\", \"purchasing\", \"external\",          \"employee\", \"login\") ".
		 "VALUES ".
		 "('$accounting', '$admin', '$humanresources', '$directory', '$calendar', '$operations', '$sales', '$support', '$production', '$purchasing', '$external', '$eid',         '$login')");
	}
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
echo "Thanks For Updating ".$data1[0]['firstname']." ".$data1[0]['lastname']."'s Account";
require('../trailer.php');
?>
