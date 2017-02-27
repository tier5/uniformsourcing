<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');

$return_arr = array();

$return_arr['weblink'] = "";
$return_arr['index'] = "";
$return_arr['error'] = "";

if(isset($_POST['carrier_id']))
{
	$carrier_id = $_POST['carrier_id'];
	$return_arr['index'] = $_POST['index'];
	
	if($carrier_id > 0)
	{
		$sql = "select weblink  from  tbl_carriers where status=1 and carrier_id=$carrier_id";
		if(!($result=pg_query($connection,$sql))){
			print("Failed weblink: " . pg_last_error($connection));
			exit;
		}
		while($row = pg_fetch_array($result)){
			$data_carrier=$row;
		}
		pg_free_result($result);
		if(isset($data_carrier) && $data_carrier['weblink'] != "")
			$return_arr['weblink'] = $data_carrier['weblink'];
	}
}
echo json_encode($return_arr);
exit;
?>