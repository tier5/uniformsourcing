<?php
require('Application.php');
require('../jsonwrapper/jsonwrapper.php');
if($debug == "on"){
	require('../header.php');
	foreach($_POST as $key=>$value) {
		if($key!="submit") { echo "$key = $value<br/>"; }
	}
}
$return_arr = array();

$return_arr['name'] = "";
$return_arr['error'] = "";

if(isset($_POST['colorId']))
{
	$colorId = $_POST['colorId'];
	$imgName 	= $_POST['imgName'];	
	
	if($colorId > 0)
	{
		$query = "DELETE from \"tbl_invColor\" where \"colorId\"='$colorId'";	
		if(!($result=pg_query($connection,$query))){
			$return_arr['error'] = pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		}
		pg_free_result($result);		
	}
	if(file_exists("$upload_dir_image"."$imgName")) {
		@ unlink("$upload_dir_image"."$imgName");
	}
}
echo json_encode($return_arr);
exit;
?>