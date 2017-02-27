<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
if(isset($_GET['submitType']) && $_GET['submitType'] =="del")
{
	$styleId = $_GET['ID'];
	$sql = 'delete from "tbl_invStorage" where "styleId" ='.$styleId.';';
	
	$sql .= 'select "colorId",name,image from "tbl_invColor" where "styleId" ='.$styleId.';';
	if(!($result=pg_query($connection,$sql)))
	{
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result))
	{
		$data_color[] = $row;
	}
	pg_free_result($result);
	$del = 1;
	$sql ="";
	for($i=0; $i <count($data_color); $i++)
	{
		$imageName = $data_color[$i]['image'];
		$sql .= "DELETE from \"tbl_invColor\" where \"colorId\"='".$data_color[$i]['colorId']."';";		
		if( $i > ($del*10))
		{
			if(!($result=pg_query($connection,$sql)))
			{
				print("Failed query1: " . pg_last_error($connection));
				exit;
			}
			pg_free_result($result);
			$sql = "";
			$del += 1; 
		}
		if(file_exists("$upload_dir_image"."$imageName"))
		{
			@ unlink("$upload_dir_image"."$imageName");
		}
	}
	if($sql != "")
	{
		if(!($result=pg_query($connection,$sql)))
		{
			print("Failed query1: " . pg_last_error($connection));
			exit;
		}
		pg_free_result($result);
		$sql = "";
	}
	
	$sql .= 'delete from tbl_inventory where "styleId" ='.$styleId.';';
	
	$sql .= 'delete from "tbl_invStyle" where "styleId" ='.$styleId.';';
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	pg_free_result($result);
}
header("location: reports.php");
?>