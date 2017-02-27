<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
$return_arr = array();
$return_arr['id'] = "";
$return_arr['sid'] = "";
$return_arr['name'] = "";
$return_arr['error'] = "";
if(isset($_POST['id']) && isset($_POST['pid']))
{
	$id = $_POST['id'];
	$sql ="Delete from tbl_prj_sample_po_items where sample_id =".$id.";";
	$sql .="Delete from tbl_prj_sample_po where sample_id =".$id.";";
	$sql .="Delete from tbl_prjsample_uploads where sample_id =".$id.";";
	$sql .="Delete from tbl_prjsample_notes where sample_id =".$id.";";
	$sql .="Delete from tbl_prjsample_notes where sample_id =".$id.";";
	$sql .= "Delete from tbl_prj_sample where  prj_element_id = $id";
	//echo $sql;
	if($sql !="")
	{
		if(!($result=pg_query($connection,$sql)))
		{
			print("Failed sql: " . pg_last_error($connection));
			exit;
		}
	}
	$return_arr['sid'] = $id;
}
echo json_encode($return_arr);
exit;
?>