<?php
require('Application.php');
if($_POST['prj_manager'] && $_POST['client']=="")
{
	$prj_manager=$_POST['prj_manager'];
	
	$sql="select DISTINCT(c.client),c.\"ID\" from \"clientDB\" as c inner join tbl_newproject as p on p.client=c.\"ID\" left join tbl_prjpurchase as prch on prch.pid = p.pid where p.project_manager='$prj_manager' and prch.purchaseorder IS NOT NULL and p.status =1";
	//echo $sql;
	if(!($result=pg_query($connection,$sql))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
	echo '<option value="">-----Select------</option>';
	while($row=pg_fetch_array($result))
	{
		$id=$row['ID'];
		$data=$row['client'];
		echo '<option value="'.$id.'">'.$data.'</option>';
	}
}
if($_POST['prj_manager'] && $_POST['client']!="")
{
		$ID=$_POST['client'];
		$prj_manager=$_POST['prj_manager'];
		if($_POST['prj_manager'])
		{
			$innerJOIN="inner join \"employeeDB\" as e on e.\"employeeID\"=p.project_manager";
			$joinLINK="and p.project_manager=".$_POST['prj_manager'];
		}
		$sql="select Distinct(p.projectname),p.pid from tbl_newproject as p ".$innerJOIN." inner join \"clientDB\" as c on 
		c.\"ID\"=p.client left join tbl_prjpurchase as prch on prch.pid = p.pid where p.client='$ID' and prch.purchaseorder IS NOT NULL and p.status =1 ".$joinLINK." ";		  
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