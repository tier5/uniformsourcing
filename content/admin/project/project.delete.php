<?php
require('Application.php');

$ID=$_GET['ID'];
$query1=("UPDATE \"tbl_projects\" ".
		"SET ".
		"\"status\" = '0', \"modifiedDate\" = '".date('U')."' ".
		" WHERE \"pid\" = '$ID'");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}

header("location: ../project/project.list.php");
?>
