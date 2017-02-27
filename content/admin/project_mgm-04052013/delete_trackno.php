<?php
require('Application.php');

$return_arr = array();
$return_arr['value'] = "";
$return_arr['error'] = "";

if(isset($_POST['id']))
{
	$id = $_POST['id'];
	$query = "Delete FROM tbl_prjorder_track_no WHERE track_id = $id";	
	if(!($result=pg_query($connection,$query))){
		$return_arr['error'] = pg_last_error($connection);
		echo json_encode($return_arr);
		return;
	}
	pg_free_result($result);
}
echo json_encode($return_arr);
exit;
?>