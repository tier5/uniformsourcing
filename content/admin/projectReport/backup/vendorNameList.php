<?php
require('Application.php');
if($_POST['clientid'])
{
	$projet_cid=$_POST['clientid'];
	
	$sql="select DISTINCT(v.\"vendorName\"),v.\"vendorID\" from vendor v inner join tbl_prjvendor  pv on pv.vid=v.\"vendorID\" inner 
join tbl_newproject as p on p.pid = pv.pid inner join \"clientDB\" c on c.\"ID\"=p.client where p.client='$projet_cid' and p.status =1";
	echo $sql;
	if(!($result=pg_query($connection,$sql))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
	echo '<option value="">-----Select------</option>';
	while($row=pg_fetch_array($result))
	{
		$id=$row['vendorID'];
		$data=$row['vendorName'];
		echo '<option value="'.$id.'">'.$data.'</option>';
	}
}
if($_POST['vendorid'])
{
		$ID=$_POST['vendorid'];
		$clientID=$_POST['client'];
		if($_POST['client'])
		{
			
			$innerJOIN="inner join \"clientDB\" c on c.\"ID\"=p.client";
			$joinLINK="and p.client=".$_POST['client'];
		}
		$sql="select p.pid,p.projectname from tbl_newproject as p ".$innerJOIN." left join tbl_prjvendor pv on 
		pv.pid=p.pid where pv.vid='$ID' ".$joinLINK." ";		  
		if(!($result=pg_query($connection,$sql))){
			print("Failed query: " . pg_last_error($connection));
			exit;
		}
		echo '<option value="">-----Select------</option>';
		while($row=pg_fetch_array($result))
		{
			$id=$row['pid'];
			$data=$row['projectname'];
			echo '<option value="'.$id.'">'.$data.'</option>';
		}
}
?>