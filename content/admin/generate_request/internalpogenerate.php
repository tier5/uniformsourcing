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

$return_arr['value'] = "";
$return_arr['error'] = "";

$query = "Select nextval(('request_internalpo'::text)::regclass) as nextinternal_po ";	
	if(!($result=pg_query($connection,$query))){
		$return_arr['error'] = pg_last_error($connection);
		echo json_encode($return_arr);
		return;
	}
	$data= '';
	while ($row = pg_fetch_array($result))
	{
		$data=$row;
	}
	$return_arr['value'] = $data['nextinternal_po'];
	pg_free_result($result);		
echo json_encode($return_arr);
exit;
?>