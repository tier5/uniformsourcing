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

switch ($_POST['number']) {
    case 0:

if($_POST['prj_manager']!="" )
{
	if($_POST['client'])
	{
		$joinLINK="and c.\"ID\"=".$_POST['client'];
	}
	$prj_manager=$_POST['prj_manager'];
	$sql="select distinct(p.projectname),p.pid from tbl_newproject as p inner join \"employeeDB\" as e 
	on p.project_manager=e.\"employeeID\" inner join \"clientDB\" as c  on c.\"ID\"=p.client left join tbl_prjpurchase as prch on prch.pid = p.pid ".$emp_join." where p.project_manager='$prj_manager' and prch.purchaseorder IS NULL and p.status ='1'".$emp_sql." and p.projectname <> '' ".$joinLINK." order by p.projectname";
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
	pg_free_result($result);
}






if($_POST['client']!="" && $_POST['prj_manager']=="")
{
		$ID=$_POST['client'];
		$sql="select Distinct(p.projectname),p.pid from tbl_newproject as p ".$innerJOIN." inner join \"clientDB\" as c on 
		c.\"ID\"=p.client left join tbl_prjpurchase as prch on prch.pid = p.pid ".$emp_join." where p.client='$ID' and prch.purchaseorder IS NULL and p.status =1 ".$emp_sql." order by p.projectname";
		//echo $sql;
		if(!($result=pg_query($connection,$sql))){
			print("Failed query: " . pg_last_error($connection));
			exit;
		}
		echo '<option value="">-----Select-------</option>';
		while($row=pg_fetch_array($result))
		{
			$id=$row['pid'];
			$data=$row['projectname'];
			echo '<option value="'.$id.'">'.$data.'</option>';
		}
		pg_free_result($result);
}
break;


  case 1:

if($_POST['prj_manager1']!="" )
{
	if($_POST['client'])
	{
		$joinLINK="and c.\"ID\"=".$_POST['client'];
	}
	$prj_manager1=$_POST['prj_manager1'];
	$sql="select distinct(p.projectname),p.pid from tbl_newproject as p inner join \"employeeDB\" as e 
	on p.project_manager1=e.\"employeeID\" inner join \"clientDB\" as c  on c.\"ID\"=p.client left join tbl_prjpurchase as prch on prch.pid = p.pid ".$emp_join." where p.project_manager1='$prj_manager1' and prch.purchaseorder IS NULL and p.status ='1'".$emp_sql." and p.projectname <> '' ".$joinLINK." order by p.projectname";
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
	pg_free_result($result);
}






if($_POST['client']!="" && $_POST['prj_manager1']=="")
{
		$ID=$_POST['client'];
		$sql="select Distinct(p.projectname),p.pid from tbl_newproject as p ".$innerJOIN." inner join \"clientDB\" as c on 
		c.\"ID\"=p.client left join tbl_prjpurchase as prch on prch.pid = p.pid ".$emp_join." where p.client='$ID' and prch.purchaseorder IS NULL and p.status =1 ".$emp_sql." order by p.projectname";
		//echo $sql;
		if(!($result=pg_query($connection,$sql))){
			print("Failed query: " . pg_last_error($connection));
			exit;
		}
		echo '<option value="">-----Select-------</option>';
		while($row=pg_fetch_array($result))
		{
			$id=$row['pid'];
			$data=$row['projectname'];
			echo '<option value="'.$id.'">'.$data.'</option>';
		}
		pg_free_result($result);
}
break;


  case 2:

if($_POST['prj_manager2']!="" )
{
	if($_POST['client'])
	{
		$joinLINK="and c.\"ID\"=".$_POST['client'];
	}
	$prj_manager2=$_POST['prj_manager2'];
	$sql="select distinct(p.projectname),p.pid from tbl_newproject as p inner join \"employeeDB\" as e 
	on p.project_manager2=e.\"employeeID\" inner join \"clientDB\" as c  on c.\"ID\"=p.client left join tbl_prjpurchase as prch on prch.pid = p.pid ".$emp_join." where p.project_manager2='$prj_manager2' and prch.purchaseorder IS NULL and p.status ='1'".$emp_sql." and p.projectname <> '' ".$joinLINK." order by p.projectname";
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
	pg_free_result($result);
}






if($_POST['client']!="" && $_POST['prj_manager2']=="")
{
		$ID=$_POST['client'];
		$sql="select Distinct(p.projectname),p.pid from tbl_newproject as p ".$innerJOIN." inner join \"clientDB\" as c on 
		c.\"ID\"=p.client left join tbl_prjpurchase as prch on prch.pid = p.pid ".$emp_join." where p.client='$ID' and prch.purchaseorder IS NULL and p.status =1 ".$emp_sql." order by p.projectname";
		//echo $sql;
		if(!($result=pg_query($connection,$sql))){
			print("Failed query: " . pg_last_error($connection));
			exit;
		}
		echo '<option value="">-----Select-------</option>';
		while($row=pg_fetch_array($result))
		{
			$id=$row['pid'];
			$data=$row['projectname'];
			echo '<option value="'.$id.'">'.$data.'</option>';
		}
		pg_free_result($result);
}
break;
}
?>