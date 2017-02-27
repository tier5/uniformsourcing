<?php
require('Application.php');
extract($_POST);
$search_sql ="";
$eid =0;
if(isset($_POST['eid'])&& $_POST['eid']>0)
{
	$eid = $_POST['eid'];
}
if(isset($_POST['delete_date']) && $_POST['delete_date']!="")
{
	$delete_date = $_POST['delete_date'].' 23:59:59';
	$date = strtotime($delete_date);
	$current_day = date('m/d/Y',date('U')).' 23:59:59';
	$current= strtotime($current_day);
	if($eid> 0)
		$search_sql = ' and employee_id ='.$eid;
	$sql="delete from tbl_change_record where created_date <= ".$date.$search_sql;
	//echo $sql;
	if(!($result1=pg_query($connection,$sql)))
	{
		print("Failed Delete query: " . pg_last_error($connection));
		exit;
	}
	pg_free_result($result1);
	$sql="";
	if($eid == 0)
	{
		if($date == $current)
		{
			$sql="ALTER SEQUENCE tbl_change_record_id_seq RESTART WITH 1;";
			if(!($result1=pg_query($connection,$sql)))
			{
				print("Failed alter query: " . pg_last_error($connection));
				exit;
			}
			pg_free_result($result1);
			$sql="";
		}
	}
}
if($eid>0)
	header('Location:track_log.php?eid='.$eid);
else 
 	header('Location:track_log.php');
?>