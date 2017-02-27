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
if(isset($_POST['client_val']))
{
	$clientval = $_POST['client_val'];
	$query = "SELECT \"ID\", \"clientID\", \"client\",shipperno, \"active\" FROM \"clientDB\" WHERE \"active\" = 'yes' and  \"ID\" = $clientval";	
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
	$return_arr['value'] = $data['shipperno'];
	pg_free_result($result);		
}
echo json_encode($return_arr);
exit;
?>