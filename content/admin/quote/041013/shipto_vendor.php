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
$return_arr['type'] = "vendor";
if(isset($_POST['id']))
{
	$id = $_POST['id'];
	$query = "SELECT address,city,state,zip FROM vendor WHERE active = 'yes' and  \"vendorID\" = $id";	
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
	$shipto = "";
	if($data['address'] !="")
	{
		$shipto .=$data['address'];
	}
	if($data['city'] !="")
	{
		$shipto .="\n".$data['city'];
	}
	if($data['state'] !="")
	{
		$shipto .=",".$data['state'];
	}
	if($data['zip'] !="")
	{
		$shipto .="\n".$data['zip'];
	}
	$return_arr['value'] = $shipto;
	pg_free_result($result);		
}
echo json_encode($return_arr);
exit;
?>