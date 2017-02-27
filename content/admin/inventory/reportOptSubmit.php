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

$return_arr['styleId'] = "";
$return_arr['colorId'] = "";
$return_arr['error'] = "";

if(isset($_POST['StyleId']))
{
	$return_arr['styleId'] 	= $_POST['StyleId'];
	$return_arr['colorId'] 	= $_POST['colorId'];
}
else
	$return_arr['error'] = "ERROR";
echo json_encode($return_arr);
return;
?>