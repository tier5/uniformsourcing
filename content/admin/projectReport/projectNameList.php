<?php
require('Application.php');
if($_POST['clientid'])
{
	if($_POST['vendorid'])
		{
			$innerJOIN="left join tbl_prjvendor as v  on v.pid=p.pid";
			$joinLINK="and v.vid=".$_POST['vendorid'];
		}
	$clientid=$_POST['clientid'];
	$sql="select p.pid,p.projectname from tbl_newproject as p inner join \"clientDB\" c
	on p.client=c.\"ID\" ".$innerJOIN." where p.client='$clientid' and p.status ='1' ".$joinLINK."";
if(!($result=pg_query($connection,$sql))){
	print("Failed query: " . pg_last_error($connection));
	exit;
}
	echo '<option value="">-----Select-----</option>';
	while($row=pg_fetch_array($result))
	{
		$id=$row['pid'];
		$data=$row['projectname'];
		echo '<option value="'.$id.'">'.$data.'</option>';
	}
}
if($_POST['manager_id'])
{
	$manager_id=$_POST['manager_id'];
	$sql="select distinct(p.projectname),p.pid from tbl_newproject as p inner join \"employeeDB\" as e on p.project_manager=e.\"employeeID\" inner join \"clientDB\" c on p.client=c.\"ID\" where p.project_manager='$manager_id' and p.status ='1' ";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
	echo '<option value="">-----Select-----</option>';
	while($row=pg_fetch_array($result))
	{
		$id=$row['pid'];
		$data=$row['projectname'];
		echo '<option value="'.$id.'">'.$data.'</option>';
	}
}


?>