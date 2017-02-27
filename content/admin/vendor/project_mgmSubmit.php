<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
if($debug == "on"){
	require('../../header.php');
	foreach($_POST as $key=>$value) {
		if($key!="submit") { echo "$key = $value<br/>"; }
	}
}
$error = "";
$msg = "";
$return_arr = array();
extract($_POST);
$return_arr['error'] = "";
$return_arr['name'] = "";
$return_arr['id'] = "";
$projectName=pg_escape_string($projectName);
$style=pg_escape_string($style);
$color=pg_escape_string($color);
$materialtype=pg_escape_string($materialtype);
$projectnotes=pg_escape_string($projectnotes);
$purchaseOrder=pg_escape_string($purchaseOrder);
$sizeNeeded=pg_escape_string($sizeNeeded);
$garDescription=pg_escape_string($garDescription);

$elementstyle=pg_escape_string($elementstyle);
$elementcolor=pg_escape_string($elementcolor);
if(isset($_POST['pid']))
{
	$pid = $_POST['pid'];
	
	
	$query_Name = "";
	if($pid == 0)
	{
		$query1="INSERT INTO tbl_newproject (";
		$query1.=" ,client ";
		if($color!="")$query1.=", color ";
		if($style!="")$query1.=", style ";
		if($materialtype!="")$query1.=", materialtype ";
	 	$query1.=", status ";
		$query1.=", createddate ";
		$query1.=", updateddate ";
		$query1.=", created_date ";
		$query1.=", createdby ";
		$query1.=")";
		$query1.=" VALUES (";
		$query1.=", '$clientID' ";
		if($color!="")$query1.=", '$color' ";
		if($style!="")$query1.=", '$style' ";
		if($materialtype!="")$query1.=" ,'$materialtype' ";
		$query1.=" ,1 ";
		$query1.=" ,'".date('U')."' ";
		$query1.=" ,'".date('U')."'";
		$query1.=" ,'".date('U')."'";
		$query1.=" ,".$_SESSION["employeeID"];
		$query1.=" )";
		if(!($result=pg_query($connection,$query1))){
			$return_arr['error'] ="Error while storing project information to database!";	
			echo json_encode($return_arr);
			return;
		}
		pg_free_result($result);
		$sql='Select pid from tbl_newproject where projectname=\''.$projectName.'\'';
		if(!($result_sql=pg_query($connection,$sql))){
			$return_arr['error'] ="Error while storing style information to database!";	
			echo json_encode($return_arr);
			return;
		}
		$row=pg_fetch_array($result_sql);
		 $return_arr['id'] = $row['pid'];
		pg_free_result($result_sql);		
	}
	$query_Name ="";
	if($pid > 0 && $return_arr['id']=="")
	{
		$query_Name.="UPDATE tbl_newproject SET  status= 1 ";
		if($style!="")$query_Name.=", style = '$style' ";
		else $query_Name.=", style = null ";
		if($materialtype!="")$query_Name.=", materialtype = '$materialtype' ";
		else $query_Name.=", materialtype =  null ";
		$query_Name.=", updateddate = ".date('U');
		$query_Name.=", createdby = ".$_SESSION["employeeID"];
		$query_Name.="  where pid='$pid'".";";
		
		if($query_Name !="")
		{
			//echo $query_Name;
			if(!($result=pg_query($connection,$query_Name)))
			{
				$return_arr['error'] = pg_last_error($connection);
				echo json_encode($return_arr);
				return;
			}
			pg_free_result($result);
			$query_Name ="";
		}
	}
	else 
	{
		$pid = $return_arr['id'];
	}
	
	/*New query for estimated unit cost*/
	
	$query_Name  = "";
	if(count($textAreaName) > 0)
	{
		for($i=0; $i<count($textAreaName); $i++)
		{
			$query_Name.="Insert into tbl_mgt_notes (";
			if($textAreaName[$i]!="") $query_Name.="notes ,";
			$query_Name.=" pid" ;
			$query_Name .=", \"createdDate\"";
			$query_Name .=", \"createdTime\"";
			$query_Name .=", \"createdBy\"";
			$query_Name .=" )Values(";
			if($textAreaName!="") $query_Name .=" '".pg_escape_string($textAreaName[$i])."',";
			$query_Name .=" $pid";
			$query_Name .=", ".date("U");
			$query_Name .=", ".date("U");
			$query_Name .=", ".$_SESSION["employeeID"]."";
			$query_Name .=" );";
		}
	}
	if($query_Name !="")
	{
		//echo $query_Name;
		if(!($result=pg_query($connection,$query_Name)))
		{
			$return_arr['error'] ="Error while storing project notes information to database!";
			echo json_encode($return_arr);
			return;
		}
		$query_Name = "";
		pg_free_result($result);
	}
	$query_Name ="";
	$return_arr['id'] = $pid;
	
}

echo json_encode($return_arr);
return;
?>