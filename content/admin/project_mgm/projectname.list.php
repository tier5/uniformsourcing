<?php
require('Application.php');
if($_POST['clientid'])
{
$clientid=$_POST['clientid'];
$status=1;
if(isset($_POST['status']))
$status=$_POST['status'];
if(isset($_POST['list_type']))
{
	$listType =$_POST['list_type'];
	if($listType == 1)
	{
		$sql="select p.projectname, p.pid,c.client,c.\"ID\" from tbl_newproject_closed p inner join \"clientDB\" c on  p.client=c.\"ID\" inner join tbl_prjpurchase_closed as prch on prch.pid = p.pid where p.client='$clientid' and prch.purchaseorder IS NOT NULL ";
	}
	else if($listType == 0)
	{
	//	$sql="select p.projectname, p.pid,c.client,c.\"ID\" from tbl_newproject p left join \"clientDB\" c on  p.client=c.\"ID\"  where p.client='$clientid' and p.status=$status ";
$sql="select p.projectname, p.pid,c.client,c.\"ID\" from tbl_newproject_closed p inner join \"clientDB\" c on  p.client=c.\"ID\"  where p.client='$clientid' ";             
                echo $sql;
	}
}
if(!($result=pg_query($connection,$sql))){
	print("Failed query: " . pg_last_error($connection));
	exit;
}
	echo '<option value="">Select</option>';
	while($row=pg_fetch_array($result))
	{ //print_r($row);
		$id=$row['pid'];
		$data=$row['projectname'];
		echo '<option value="'.$id.'">'.$data.'</option>';
	}
}

?>