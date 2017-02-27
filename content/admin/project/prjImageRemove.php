<?php
require('Application.php');
$upload_dir			= "../../projectimages/";
require('../../jsonwrapper/jsonwrapper.php');
if($debug == "on"){
	require('../../header.php');
	foreach($_POST as $key=>$value) {
		if($key!="submit") { echo "$key = $value<br/>"; }
	}
}
$return_arr = array();

$return_arr['name'] = "";
$return_arr['error'] = "";

if(isset($_POST['imgName']) && !isset($_POST['pid']))
{
	$imgName 	= $_POST['imgName'];	
	
	if(file_exists("$upload_dir"."$imgName")) {
		@ unlink("$upload_dir"."$imgName");
	}
}
else if(isset($_POST['pid']))
{
	$imgName 	= $_POST['imgName'];
	$pid	    = $_POST['pid'];
	$imgColumn	    = $_POST['column_name'];
	
	$sql = "Update tbl_projects SET ".$imgColumn ."= null where pid=".$pid;
	if(!($result=pg_query($connection,$sql)))
	{
		print("Failed sql: " . pg_last_error($connection));
		exit;
	}
	if(file_exists("$upload_dir"."$imgName")) {
		@ unlink("$upload_dir"."$imgName");
	}
}
echo json_encode($return_arr);
exit;
?>