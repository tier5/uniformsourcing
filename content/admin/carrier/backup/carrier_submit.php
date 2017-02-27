<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
$return_arr = array();
extract($_POST);
$return_arr['msg'] = "";
$return_arr['error'] = "";
$return_arr['name'] = "";
$cname = pg_escape_string($cname);
$cweblink = pg_escape_string($cweblink);
if($carrier_id >0)
{
	$sql ="Update tbl_carriers SET status ='1'";
	if($cname!="")$sql .=",carrier_name='$cname'";
	if($cweblink!="")$sql .= ",weblink='$cweblink' ";
	$sql .= "where carrier_id = $carrier_id";
	if(!($result=pg_query($connection,$sql))){
		$return_arr['error'] = pg_last_error($connection);
		echo json_encode($return_arr);
		return;
	}
	pg_free_result($result);
}
else
{
	$sql="SELECT carrier_id FROM tbl_carriers WHERE carrier_name = '$cname' and weblink='$cweblink'";
	if(!($result=pg_query($connection,$sql))){
		$return_arr['error'] = pg_last_error($connection);
		echo json_encode($return_arr);
		return;
	}
	while($row = pg_fetch_array($result)){
			$data[]=$row;
	}
	pg_free_result($result);
	if(count($data)>0)
	{
		$return_arr['error'] = "Carrier already exists";
		echo json_encode($return_arr);
		return;
	}
	else
	{
		$query="insert into tbl_carriers (";
		if($cname!="")$query.="carrier_name";
		if($cweblink!="")$query.=",weblink";
		$query.=",status";
		$query.=") Values (";
		if($cname!="")$query.="'$cname'";
		if($cweblink!="")$query.=",'$cweblink'";
		$query.=",'1'";
		$query.=")";
		if(!($result=pg_query($connection,$query))){
			$return_arr['error'] = pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		}
		pg_free_result($result);
	}
}

echo json_encode($return_arr);
return;
?>