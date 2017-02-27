<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
$return_arr = array();

$return_arr['value'] = "";
$return_arr['error'] = "";
$return_arr['sample_client_shipper'] ="";
if(isset($_POST['client_id']))
{
	$id = $_POST['client_id'];
	$query = "SELECT shipperno FROM \"clientDB\" WHERE \"active\" = 'yes' and  \"ID\" = $id";	
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
	$return_arr['sample_client_shipper'] = $data['shipperno']; 
	pg_free_result($result);		
}
echo json_encode($return_arr);
exit;
?>