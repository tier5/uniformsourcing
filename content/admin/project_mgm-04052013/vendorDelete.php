<?php
require('Application.php');
require('../../jsonwrapper/jsonwrapper.php');
if($debug == "on"){
	require('../../header.php');
	foreach($_POST as $key=>$value) {
		if($key!="submit") { echo "$key = $value<br/>"; }
	}
}
$return_arr = array();

$return_arr['name'] = "";
$return_arr['error'] = "";

if(isset($_POST['vendorId']) && isset($_POST['pid']))
{
	$vendorId = $_POST['vendorId'];	
	$pid = $_POST['pid'];
	if($vendorId > 0)
	{
		$query = "DELETE from tbl_prjvendor where tbl_vendorid='$vendorId' and pid = $pid";	
		if(!($result=pg_query($connection,$query))){
			$return_arr['error'] = pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		}
		pg_free_result($result);		
	}
}
echo json_encode($return_arr);
exit;
?>