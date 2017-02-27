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
if(isset($_POST['item_id']))
{
	$id = $_POST['item_id'];
	$amountSubTotal = $_POST['amountSubTotal'];
	$taxSubTotal = $_POST['taxSubTotal'];
	$total = $_POST['total'];
	$po_id = $_POST['po_id'];
	$query = "Delete FROM tbl_prj_sample_po_items WHERE id = $id";	
	if(!($result=pg_query($connection,$query))){
		$return_arr['error'] = pg_last_error($connection);
		echo json_encode($return_arr);
		return;
	}
	else{
		$query = '';
		$query ="Update tbl_prj_sample_po SET ";
		$query.=" amount_sub_total =$amountSubTotal";
		$query.=", tax_sub_total ='$taxSubTotal'";
		$query.=", total ='$total'";
		$query.=" where id=".$po_id;
	}
	pg_free_result($result);	
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