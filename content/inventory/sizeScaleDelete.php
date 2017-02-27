<?php
require('Application.php');
require('../jsonwrapper/jsonwrapper.php');
if($debug == "on"){
	require('../header.php');
	foreach($_POST as $key=>$value) {
		if($key!="submit") { echo "$key = $value<br/>"; }
	}
}
$return_arr = array();

$return_arr['name'] = "";
$return_arr['error'] = "";
if(isset($_GET['type']) && $_GET['type'] == 'all' && $_GET['id'] !="")
{
	$scaleId = $_GET['id'];
	$sql = 'select "styleId" from "tbl_invStyle" where "scaleNameId"='.$scaleId;
	if(!($result=pg_query($connection,$sql))){
		$return_arr['error'] = pg_last_error($connection);
		echo json_encode($return_arr);
		return;
	}
	while($row = pg_fetch_array($result))
	{
		$style_data[]=$row;
	}
	pg_free_result($result);
	$sql = "";
	$del = 1;
	for($i=0; $i < count($style_data); $i++)
	{
		$sql .= 'delete from "tbl_invStorage" where "styleId" = '.$style_data[$i]['styleId'].';';
		$sql .= 'delete from tbl_inventory where "styleId" = '.$style_data[$i]['styleId'].';';
		$sql .= 'delete from "tbl_invColor" where "styleId" = '.$style_data[$i]['styleId'].';';
		if($i > ($del*10))
		{
			if(!($result=pg_query($connection,$sql)))
			{
				$return_arr['error'] = pg_last_error($connection);
				echo json_encode($return_arr);
				return;
			}
			pg_free_result($result);
			$sql = "";
			$del ++;
		}
	}
	if($sql !="")
	{
		if(!($result=pg_query($connection,$sql)))
		{
			$return_arr['error'] = pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		}
	}
	$sql = 'delete from "tbl_invStyle" where "scaleNameId" ='.$scaleId.';';
	$sql .='delete from "tbl_invScaleSize" where "scaleId" ='.$scaleId.';';
	$sql .='delete from "tbl_invScaleName" where "scaleId" ='.$scaleId.';';
	if(!($result=pg_query($connection,$sql)))
		{
			$return_arr['error'] = pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		}
		pg_free_result($result);
		$sql = "";
		header('location:sizeScaleList.php');
}
else if(isset($_POST['type']))
{
	$sizeId = $_POST['sizeId'];
	$size 	= $_POST['size'];
	$type 	= $_POST['type'];
	$size=pg_escape_string($size);	
	$query_Size="select * from \"tbl_invScaleSize\" where \"sizeScaleId\"='".$sizeId."'";
	if(!($result=pg_query($connection,$query_Size))){
		$return_arr['error'] = pg_last_error($connection);
		echo json_encode($return_arr);
		return;
	}
	while($row = pg_fetch_array($result)){
		$data=$row;
	}
	$query_Size = "";
	pg_free_result($result);
	$sql = "";
	switch ($type)			
	{	
		case "scale":		
		{
			$sql = 'select "inventoryId" from tbl_inventory where "sizeScaleId" ='.$data['sizeScaleId'];
			if(!($result=pg_query($connection,$sql))){
				$return_arr['error'] = pg_last_error($connection);
				echo json_encode($return_arr);
				return;
			}
			while($row = pg_fetch_array($result))
			{
				$inv_size1[]=$row;
			}
			$sql = "";
			$del = 1;
			for($i=0; $i< count($inv_size1); $i++)
			{
				$sql .= 'delete from "tbl_invStorage" where "invId" ='.$inv_size1[$i]['inventoryId'].';';
				$sql .= 'delete from tbl_inventory where "inventoryId" ='.$inv_size1[$i]['inventoryId'].';';
				if($i > ($del * 10))
				{
					if(!($result=pg_query($connection,$sql)))
					{
						$return_arr['error'] = pg_last_error($connection);
						echo json_encode($return_arr);
						return;
					}
					pg_free_result($result);
					$sql = "";
					$del += 1;
					
				}
			}
			if($sql != "")
			{
				if(!($result=pg_query($connection,$sql)))
				{
					$return_arr['error'] = pg_last_error($connection);
					echo json_encode($return_arr);
					return;
				}
				pg_free_result($result);
			}
			if($data['opt1Size'] == "" && $data['opt2Size'] == "")
				$query = "DELETE from \"tbl_invScaleSize\" where \"sizeScaleId\"='$sizeId'";
			else
			{
				$query = "UPDATE \"tbl_invScaleSize\" SET \"scaleSize\" = '' where \"sizeScaleId\"='$sizeId' ";
			}													
			break;
		}
		case "opt1":		
		{	
			$sql = 'select "inventoryId" from tbl_inventory where "opt1ScaleId" ='.$data['sizeScaleId'];
			if(!($result=pg_query($connection,$sql))){
				$return_arr['error'] = pg_last_error($connection);
				echo json_encode($return_arr);
				return;
			}
			while($row = pg_fetch_array($result))
			{
				$inv_size2[]=$row;
			}
			pg_free_result($result);
			$sql = "";
			$del = 1;
			for($j=0; $j< count($inv_size2); $j++)
			{
				$sql .= 'delete from "tbl_invStorage" where "invId" ='.$inv_size2[$j]['inventoryId'].';';
				$sql .= 'delete from tbl_inventory where "inventoryId" ='.$inv_size2[$j]['inventoryId'].';';
				if($j > ($del * 10))
				{
					if(!($result=pg_query($connection,$sql)))
					{
						$return_arr['error'] = pg_last_error($connection);
						echo json_encode($return_arr);
						return;
					}
					pg_free_result($result);
					$sql = "";
					$del += 1;
					
				}
			}
			if($sql != "")
			{
				if(!($result=pg_query($connection,$sql)))
				{
					$return_arr['error'] = pg_last_error($connection);
					echo json_encode($return_arr);
					return;
				}
				pg_free_result($result);
			}
			if($data['scaleSize'] == "" && $data['opt2Size'] == "")
				$query = "DELETE from \"tbl_invScaleSize\" where \"sizeScaleId\"='$sizeId'";
			else
			{
				$query = "UPDATE \"tbl_invScaleSize\" SET \"opt1Size\" = '' where \"sizeScaleId\"='$sizeId' ";
			}													
			break;
		}
		case "opt2":		
		{	
			$sql = 'select "inventoryId" from tbl_inventory where "opt2ScaleId" ='.$data['sizeScaleId'];
			if(!($result=pg_query($connection,$sql))){
				$return_arr['error'] = pg_last_error($connection);
				echo json_encode($return_arr);
				return;
			}
			while($row = pg_fetch_array($result))
			{
				$inv_size3[]=$row;
			}
			pg_free_result($result);
			$sql = "";
			$del = 1;
			for($k=0; $k< count($inv_size3); $k++)
			{
				$sql .= 'Update "tbl_invStorage" set "opt2ScaleId" = null where "invId" ='.$inv_size3[$k]['inventoryId'].';';
				$sql .= 'Update tbl_inventory set "opt2ScaleId" = null where "inventoryId" ='.$inv_size3[$k]['inventoryId'].';';
				if($j > ($del * 10))
				{
					if(!($result=pg_query($connection,$sql)))
					{
						$return_arr['error'] = pg_last_error($connection);
						echo json_encode($return_arr);
						return;
					}
					pg_free_result($result);
					$sql = "";
					$del += 1;
					
				}
			}
			if($sql != "")
			{
				if(!($result=pg_query($connection,$sql)))
				{
					$return_arr['error'] = pg_last_error($connection);
					echo json_encode($return_arr);
					return;
				}
				pg_free_result($result);
			}
			if($data['scaleSize'] == "" && $data['opt1Size'] == "")
				$query = "DELETE from \"tbl_invScaleSize\" where \"sizeScaleId\"='$sizeId'";
			else
			{
				$query = "UPDATE \"tbl_invScaleSize\" SET \"opt2Size\" = '' where \"sizeScaleId\"='$sizeId' ";
			}													
			break;
		}
	}
	if(!($result=pg_query($connection,$query))){
		$return_arr['error'] = pg_last_error($connection);
		echo json_encode($return_arr);
		return;
	}
	pg_free_result($result);	
}
echo json_encode($return_arr);
exit;
?>