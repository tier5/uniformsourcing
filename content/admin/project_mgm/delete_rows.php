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
$return_arr['id'] = "";
$return_arr['name'] = "";
$return_arr['error'] = "";

if(isset($_POST['id']) && isset($_POST['table_type']))
{
	$id = $_POST['id'];
	$pid = $_POST['pid'];
	$type = trim($_POST['table_type']);
	$table_link = "";
	$table_id =0;
	switch($type)
	{
		case 'carrier':
			$table_link = 'tbl_prjshipping_carrier';
			$table_id = 'carrier_id';
			break;
		
		case 'track':
			$table_link = 'tbl_prjshipping_track';
			$table_id = 'track_id';
			break;
		
		case 'shipped':
			$table_link = 'tbl_prjshipping_shippedon';
			$table_id = 'shipped_id';
			break;
		
		case 'shipping_notes':
			$table_link = 'tbl_prjshipping_notes';
			$table_id = 'ship_notes_id';
			break;
		
		case 'prjstyle':
			$table_link = 'tbl_prj_style';
			$table_id = 'prj_style_id';
			break;
			
		case 'order_shipping':
			$table_link = 'tbl_prjorder_shipping';
			$table_id = 'shipping_id';
			break;
				
		default:
		break;
		
	}
	$sql="";
	
	if($table_link !="" && $table_id !="" && $id!="" )$sql = "Delete from $table_link where  $table_id = $id";
        if($type=='prjstyle')
         $sql.=';Delete from tbl_qty_shipped where pid='.$pid.' and prj_style_id='.$id;   

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
}
echo json_encode($return_arr);
exit;
?>