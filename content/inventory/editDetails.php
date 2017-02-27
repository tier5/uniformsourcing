<?php
require('Application.php');
require('../jsonwrapper/jsonwrapper.php');
if($debug == "on"){
	require('../header.php');
	foreach($_POST as $key=>$value) {
		if($key!="submit") { echo "$key = $value<br/>"; }
	}
}
$return_arr = array();
$return_arr['error'] ="";
//extract($_POST);
if(isset($_POST['type']) && ($_POST['id'] != ""))
{
	if($_POST['type'] == 'colorEdit')
	{
		$sql = "SELECT * from tbl_color where status=1 and \"colorID\" = '".$_POST['id']."'";
	}
	else if($_POST['type'] == 'garmentEdit' )
	{
		$sql = "SELECT * from tbl_garment where status=1 and \"garmentID\" = '".$_POST['id']."'";
	}
	else if($_POST['type'] == 'fabricEdit')
	{
		$sql = "SELECT * from tbl_fabrics where status=1 and \"fabricID\" = '".$_POST['id']."'";
	}
	else if($_POST['type'] == 'sizeEdit')
	{
		$sql = "SELECT * from tbl_size where status=1 and \"sizeID\" = '".$_POST['id']."'";
	}
	if($sql != "")
	{
		if(!($result1=pg_query($connection,$sql)))
		{
			//$return_arr['error'] = "Error while processing Color information!";			
			$error = pg_last_error($connection);
			echo json_encode($return_arr);			
			return;
		}
		while($row1 = pg_fetch_array($result1)){
			$return_arr=$row1;
		}
		pg_free_result($result1);
		$return_arr['error'] = "";
		echo json_encode($return_arr);
		return;
	}
	if($_POST['type'] == 'colorDel')
	{
		$sql = "Delete from tbl_color where \"colorID\" = '".$_POST['id']."'";
	}
	else if($_POST['type'] == 'garmentDel')
	{
		$sql = "Delete from tbl_garment where \"garmentID\" = '".$_POST['id']."'";
	}
	else if($_POST['type'] == 'fabricDel')
	{
		$sql = "Delete from tbl_fabrics where \"fabricID\" = '".$_POST['id']."'";
	}
	else if($_POST['type'] == 'sizeDel')
	{
		$sql = "Delete from tbl_size where \"sizeID\" = '".$_POST['id']."'";
	}
	if($sql != "")
	{
		if(!($result1=pg_query($connection,$sql)))
		{
			//$return_arr['error'] = "Error while processing Color information!";			
			$error = pg_last_error($connection);
			echo json_encode($return_arr);			
			return;
		}
		pg_free_result($result1);
		$return_arr['error'] = "";
		echo json_encode($return_arr);
		return;
	}
}
$return_arr['error'] = "Internal Error. Please consult your system administrator.";
echo json_encode($return_arr);
?>