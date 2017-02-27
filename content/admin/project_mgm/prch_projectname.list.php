<?php
require('Application.php');
$emp_join = '';
if(isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] >0 && $_SESSION['employeeType'] == 1))
{
	$emp_id =  $_SESSION['employee_type_id'];
	$emp_join = ' inner join tbl_prjvendor pv on pv.pid=p.pid left join vendor v on v."vendorID"=pv.vid';
	$emp_sql = ' and v."vendorID" ='.$emp_id;
}
else if(isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] >0 && $_SESSION['employeeType'] == 2))
{
	$emp_id =$_SESSION['employee_type_id'];
	$emp_sql = ' and c."ID" ='.$emp_id;
}
$innerJOIN = '';
$joinLINK = '';
if(isset($_POST['prj_manager']) && $_POST['prj_manager'] > 0)
{
	
	if(isset($_POST['client']) && $_POST['client'] > 0)
	{
		$innerJOIN.=" left join \"clientDB\" as c  on c.\"ID\"=p.client";
		$joinLINK.=" and c.\"ID\"=".$_POST['client'];
	}
	if(isset($_POST['vendor']) && $_POST['vendor'] > 0)
	{
		$innerJOIN.=" left join tbl_prjvendor as pv  on pv.pid=p.pid";
		$joinLINK.=" and pv.vid=".$_POST['vendor'];
	}
	
	$prj_manager=$_POST['prj_manager'];
	$sql="select distinct(p.projectname),p.pid from tbl_newproject as p inner join \"employeeDB\" as e
	on p.project_manager=e.\"employeeID\" left join tbl_prjpurchase as prch on prch.pid = p.pid ".$emp_join.$innerJOIN." where p.project_manager='$prj_manager' and prch.purchaseorder IS NOT NULL and p.status ='1' ".$emp_sql." and p.projectname <> '' ".$joinLINK." order by p.projectname";
	//echo $sql;
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
else if(isset($_POST['client']) && $_POST['client'] > 0)
{
	$ID=$_POST['client'];
	$sql="select Distinct(p.projectname),p.pid from tbl_newproject as p ".$innerJOIN." inner join \"clientDB\" as c on 
	c.\"ID\"=p.client left join tbl_prjpurchase as prch on prch.pid = p.pid ".$emp_join." where p.client='$ID' and prch.purchaseorder IS NOT NULL and p.status =1 ".$emp_sql." order by p.projectname ";
	//echo $sql;
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
else if(isset($_POST['vendor']) && $_POST['vendor'] > 0)
{
	$ID=$_POST['vendor'];
	$sql="select Distinct(p.projectname),p.pid from tbl_newproject as p inner join tbl_prjvendor pv on pv.pid=p.pid left join tbl_prjpurchase as prch on prch.pid = p.pid ".$emp_join." where pv.vid='$ID' and prch.purchaseorder IS NOT NULL and p.status =1 ".$emp_sql." order by p.projectname ";
	//echo $sql;
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