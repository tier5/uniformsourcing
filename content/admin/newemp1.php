<?php
require('Application.php');
if(isset($_POST['accounting'])){
	$accounting=$_POST['accounting'];
}else{
	$accounting="off";
}
if(isset($_POST['admin'])){
	$admin=$_POST['admin'];
}else{
	$admin="off";
}
if(isset($_POST['humanresources'])){
	$humanresources=$_POST['humanresources'];
}else{
	$humanresources="off";
}
if(isset($_POST['internaldirectory'])){
	$directory=$_POST['internaldirectory'];
}else{
	$directory="off";
}
if(isset($_POST['calendar'])){
	$calendar=$_POST['calendar'];
}else{
	$calendar="off";
}
if(isset($_POST['operations'])){
	$operations=$_POST['operations'];
}else{
	$operations="off";
}
if(isset($_POST['sales'])){
	$sales=$_POST['sales'];
}else{
	$sales="off";
}
if(isset($_POST['support'])){
	$support=$_POST['support'];
}else{
	$support="off";
}
if(isset($_POST['production'])){
	$production=$_POST['production'];
}else{
	$production="off";
}
if(isset($_POST['purchasing'])){
	$purchasing=$_POST['purchasing'];
}else{
	$purchasing="off";
}
if(isset($_POST['external'])){
	$external=$_POST['external'];
}else{
	$external="off";
}
if(isset($_POST['login'])){
	$login=$_POST['login'];
}else{
	$login="off";
}
extract($_POST);
$usernamenew=$_SESSION['newusername'];
$queryget=("SELECT \"employeeID\", \"username\", \"firstname\", \"lastname\" ".
		   "FROM \"employeeDB\" ".
		   "WHERE \"username\" = '$usernamenew' ");
if(!($resultget=pg_query($connection,$queryget))){
	print("Failed queryget: " . pg_last_error($connection));
	exit;
}
while($rowget = pg_fetch_array($resultget)){
	$dataget[]=$rowget;
}
if(count($dataget) != 1){
	require('Application.php');
	require('../header.php');
	echo "For some reason I could not find the username you just entered. That is weird!!!!";
	require('../trailer.php');
	exit;
}
require('../header.php');
$empid=$dataget[0]['employeeID'];
$firstname=$dataget[0]['firstname'];
$lastname=$dataget[0]['lastname'];
$queryp=("INSERT INTO \"permissions\" ".
		 "(\"accounting\", \"admin\", \"humanresources\", \"directory\", \"calendar\", \"operations\", \"sales\", \"support\", \"production\", \"purchasing\", \"external\", \"employee\", \"login\") ".
		 "VALUES ".
		 "('$accounting', '$admin', '$humanresources', '$directory', '$calendar', '$operations', '$sales', '$support', '$production', '$purchasing', '$external', '$empid', '$login')");
if(!($resultp=pg_query($connection,$queryp))){
	print("Failed queryp: " . pg_last_error($connection));
	exit;
}
echo "$firstname $lastname's account has been entered. Thank You";
require('../trailer.php');
?>
