<?php
require('Application.php');
$articleID-$_POST['articleID'];
if($debug == "on"){
	require('../../header.php');
	echo "articleID IS $articleID<br>";
	require('../../trailer.php');
	exit;
}
$query1=("DELETE FROM \"knowledgebase\" ".
		"WHERE \"articleID\" = '$articleID'");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
header("location: index.php");
?>
