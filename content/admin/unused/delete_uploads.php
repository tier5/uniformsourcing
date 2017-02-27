<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
if($debug == "on"){
	require('../../header.php');
	foreach($_POST as $key=>$value) {
		if($key!="submit") { echo "$key = $value<br/>"; }
	}
}
$return_arr = array();
$return_arr['id'] = "";
$return_arr['name'] = "";
$return_arr['error'] = "";

if(isset($_POST['tableid']) )
{
	$filename 	= $_POST['filename'];
	$tableid	    = $_POST['tableid'];
	$type	    = $_POST['type'];
	$id 	= $_POST['sample_id'];
	$sql="";
	
	$sql = "Delete from tbl_sample_uploads  where uploadid = $tableid " ;
	if($sql !="")
	{
		if(!($result=pg_query($connection,$sql)))
		{
			print("Failed sql: " . pg_last_error($connection));
			exit;
		}

		if(file_exists($mydirectory."/projectimages/".stripslashes($filename))) {
		@ unlink($mydirectory."/projectimages/".stripslashes($filename));
		}
	}
	$return_arr['id'] = $id;
}
echo json_encode($return_arr);
exit;
?>