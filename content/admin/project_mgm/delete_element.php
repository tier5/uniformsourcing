<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
$return_arr = array();
$return_arr['id'] = "";
$return_arr['eid'] = "";
$return_arr['name'] = "";
$return_arr['error'] = "";
if(isset($_POST['id']) && isset($_POST['pid']))
{
	$id = $_POST['id'];
	$pid = $_POST['pid'];
	$sql = "Delete from tbl_prj_elements where  prj_element_id = $id";
	//echo $sql;
	if($sql !="")
	{
		if(!($result=pg_query($connection,$sql)))
		{
			print("Failed sql: " . pg_last_error($connection));
			exit;
		}
	}
	$return_arr['id'] = $pid;
	$return_arr['eid'] = $id;
}
echo json_encode($return_arr);
exit;
?>