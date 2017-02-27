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
$filename 	= $_POST['filename'];

if(isset($_POST['tableid']) )
{
	$tableid	    = $_POST['tableid'];
	$type	    = $_POST['type'];
	$pid 	= $_POST['pid'];
	if(isset($_POST['db_id']))
		$db_id = $_POST['db_id'] ;
	$sql="";
	if(isset($_POST['formtype']) && $_POST['formtype'] =="element" && $tableid!='')
	{
		$sql = "Update tbl_prj_elements set pid =$pid ";
		if($type =="I")
		{
			$sql.=",image = null ";
		}
		else if($type =="F")
		{
			$sql.=",elementfile = null ";
		}
		
		$sql.=" where prj_element_id = $tableid";
	}
	elseif (isset($_POST['formtype']) && $_POST['formtype'] =="sample_upload" && $tableid!='')
	{
		$sql = "Delete from tbl_prjsample_uploads  where sample_id = $db_id " ;
	}
	else if($_POST['formtype'] =="upload" && $tableid!='')
	{	
		$sql = "Delete from tbl_prjimage_file  where \"prjimageId\" = $tableid " ;
	}
	if($sql !="")
	{
		if(!($result=pg_query($connection,$sql)))
		{
			print("Failed sql: " . pg_last_error($connection));
			exit;
		}

		if(file_exists($mydirectory."/uploadFiles/project_mgm/".stripslashes($filename))) {
			@ unlink($mydirectory."/uploadFiles/project_mgm/".stripslashes($filename));
		}
	}
	$return_arr['id'] = $pid;
}
if($_POST['formtype'] =="editTime" || $tableid =='' || $tableid ==0){
	if(file_exists($mydirectory."/uploadFiles/project_mgm/".stripslashes($filename))) {
		@ unlink($mydirectory."/uploadFiles/project_mgm/".stripslashes($filename));
		$return_arr['id'] = 0;
	}
}
echo json_encode($return_arr);
exit;
?>