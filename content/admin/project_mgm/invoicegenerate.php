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

if($_POST['type'] && $_POST['type']!="")
{
	$type = $_POST['type'];
	if($type == 'internal_po')
	{
		$query = "Select nextval(('invoice'::text)::regclass) as nextinvoice ";	
	}
	else if($type == 'generate_po')
	{
		$query = "Select nextval(('po_sequence'::text)::regclass) as nextpo ";	
	}
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
	if($type == 'internal_po')
	{
		$return_arr['value'] = $data['nextinvoice'];
	}
	else if($type == 'generate_po')
	{
		$return_arr['value'] = $data['nextpo'];
	}
	pg_free_result($result);	
}
echo json_encode($return_arr);
exit;
?>