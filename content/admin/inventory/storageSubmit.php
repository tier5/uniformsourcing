<?php
require('Application.php');
require('../../jsonwrapper/jsonwrapper.php');
if($debug == "on"){
	require('../../header.php');
	foreach($_POST as $key=>$value) {
		if($key!="submit") { echo "$key = $value<br/>"; }
	}
}
$return_arr = array();
extract($_POST);
$wareHouseRow = $row;
$return_arr['name']="";
$return_arr['error'] = "";
$return_arr['type'] = "";
if(isset($invId) && isset($formId))
{
	//$itemIndex = substr($formId,5);	
	//$itemIndex--;
	$sql = 'select "inventoryId", "styleId", "colorId", "sizeScaleId", price, "locationId","opt1ScaleId", "opt2ScaleId", quantity, "mainSize", "rowSize", "newQty" from "tbl_inventory" where "inventoryId"='.$invId.' and "isStorage"=0';
	if(!($result=pg_query($connection,$sql))){
		print("Failed StorageData: " . pg_last_error($connection));
		exit;
	}
	$row = pg_fetch_array($result);
	$data_inv=$row;
	pg_free_result($result);
	if($data_inv['inventoryId'] != "")
	{
		$sql = 'select "storageId", "invId", "sizeScaleId", "opt1ScaleId", "opt2ScaleId", "locationId", "conveyorSlotId", "conveyorQty", room, "row", rack, shelf, box, "wareHouseQty", "location", "otherQty" from "tbl_invStorage" where "invId"='.$invId.' order by "storageId"';
		
		if(!($result=pg_query($connection,$sql))){
			print("Failed Data_invQuery: " . pg_last_error($connection));
			exit;
		}
		while($row = pg_fetch_array($result)){
			$data_storage[]=$row;
		}
		pg_free_result($result);
		
		$totalStorageQty = 0;
		$totalConveyorQty = 0;
		$totalWarehouseQty = 0;
		$totalOtherQty = 0;
		for($i = 0; $i < count($data_storage); $i++)
		{
			if($data_storage[$i]['conveyorQty'] != "" && $data_storage[$i]['conveyorQty'] > 0)
				$totalConveyorQty += $data_storage[$i]['conveyorQty'];
			if($data_storage[$i]['wareHouseQty'] != "" && $data_storage[$i]['wareHouseQty'] > 0)
				$totalWarehouseQty += $data_storage[$i]['wareHouseQty'];
			if($data_storage[$i]['otherQty'] != "" && $data_storage[$i]['otherQty'] > 0)
				$totalOtherQty += $data_storage[$i]['otherQty'];
		}
		$totalStorageQty = $totalConveyorQty + $totalWarehouseQty + $totalOtherQty;	
		$type = substr($formId,0,1);
		switch($type)
		{
			case 'c':
			{
				$qty = 0;
				$i = 0;
				for($index = 0; $index < count($slotId);$index++)
				{
					if($slotId[$index] == "" || $slotId[$index] == null)
					{
						$return_arr['error'] = "All Slot Id fields are required. Please fill in empty solt Id fields.";
						echo json_encode($return_arr);
						return;
					}
				}
				for($index = 0; $index < count($slotId);$index++)
				{
				
					$found = 0;
					$query = "";
					for(; $i < count($data_storage); $i++)
					{
						if($data_storage[$i]['conveyorQty'] == "" || $data_storage[$i]['conveyorQty'] == 0)
						{
							$found = 1;
							break;	
						}
					}
					if($found)
					{
						$query = "UPDATE \"tbl_invStorage\" SET ";
						$query .="\"conveyorSlotId\" = '".$slotId[$index]."' ";
						$query .=",\"conveyorQty\" = 1 ";
						$query .=",\"updatedBy\" = '".$_SESSION['employeeID']."' ";
						$query .=",\"updatedDate\" = '".date('U')."' ";
						$query .="  where \"storageId\"='".$data_storage[$i]['storageId']."' ";
						$i++;
					}
					else
					{
						$query = "INSERT INTO \"tbl_invStorage\" (";
						$query .=" \"invId\" ";
						$query .=" ,\"styleId\" ";
						$query .=" ,\"colorId\" ";
						if($data_inv['sizeScaleId'] != "")$query .=" ,\"sizeScaleId\" ";
						if($data_inv['opt1ScaleId'] != "")$query .=" ,\"opt1ScaleId\" ";
						if($data_inv['opt2ScaleId'] != "")$query .=" ,\"opt2ScaleId\" ";
						$query .=" ,\"locationId\" ";
						$query .=" ,\"conveyorSlotId\" ";
						$query .=" ,\"conveyorQty\" ";
						$query .=" ,\"createdBy\" ";
						$query .=" ,\"updatedBy\" ";
						$query .=" ,\"createdDate\" ";					
						$query .=" ,\"updatedDate\" ";
						$query .=")";
						$query .=" VALUES (";
						$query .=" '".$data_inv['inventoryId']."' ";
						$query .=" ,'".$data_inv['styleId']."' ";
						$query .=" ,'".$data_inv['colorId']."' ";
						if($data_inv['sizeScaleId'] != "")$query .=" ,'".$data_inv['sizeScaleId']."' ";
						if($data_inv['opt1ScaleId'] != "")$query .=" ,'".$data_inv['opt1ScaleId']."' ";
						if($data_inv['opt2ScaleId'] != "")$query .=" ,'".$data_inv['opt2ScaleId']."' ";
						$query .=" ,'".$data_inv['locationId']."' ";
						$query .=" ,'".$slotId[$index]."' ";
						$query .=" ,'1' ";
						$query .=" ,'".$_SESSION['employeeID']."' ";
						$query .=" ,'".$_SESSION['employeeID']."' ";
						$query .=" ,'".date('U')."' ";
						$query .=" ,'".date('U')."' ";							
						$query .=" )";	
					}
					if($query != "")
					{						
						$return_arr['type'] = "conveyor";
						$qty++;
						if(!($result=pg_query($connection,$query))){
							$return_arr['error'] = pg_last_error($connection);
							echo json_encode($return_arr);
							return;
						}
						pg_free_result($result);
						$query = "";
					}
				}
				if($qty > 0)
				{
					if($data_inv['quantity'] != "" && $data_inv['quantity'] > 0)
						$invQty = $data_inv['quantity'] + $qty;
					else
						$invQty = $qty;
					$query = "UPDATE \"tbl_inventory\" SET ";
					$query .="\"quantity\" = '".$invQty."' ";
					$query .=",\"newQty\" = '0' ";
					$query .=",\"isStorage\" = 1 ";
					$query .=",\"updatedBy\" = '".$_SESSION['employeeID']."' ";
					$query .=",\"updatedDate\" = '".date('U')."' ";
					$query .="  where \"inventoryId\"='".$data_inv['inventoryId']."' ";
					if(!($result=pg_query($connection,$query))){
						$return_arr['error'] = pg_last_error($connection);
						echo json_encode($return_arr);
						return;
					}
					pg_free_result($result);
					$query = "";
				}			
				break;
			}
			case 'w':
			{
				$found = 0;
				$query = "";
				$i = 0;
				if($box == "")
				{
					$return_arr['error'] = "Please fill Box number before sumbimiting.";
					echo json_encode($return_arr);
					return;
				}
				if($data_inv['quantity'] != "" && $data_inv['quantity'] > 0)
					$qty = $data_inv['newQty'] - $data_inv['quantity'];
				else
					$qty = $data_inv['newQty'];
				for(; $i < count($data_storage); $i++)
				{
					if($data_storage[$i]['wareHouseQty'] == "" || $data_storage[$i]['wareHouseQty'] == 0)
					{
						$found = 1;
						break;	
					}
				}
				if($found)
				{
					$query = "UPDATE \"tbl_invStorage\" SET ";
					$query .="\"room\" = '".$room."' ";
					$query .=",\"row\" = '".$wareHouseRow."' ";
					$query .=",\"rack\" = '".$rack."' ";
					$query .=",\"shelf\" = '".$shelf."' ";
					$query .=",\"box\" = '".$box."' ";
					$query .=",\"wareHouseQty\" = '".$qty."' ";
					$query .=",\"updatedBy\" = '".$_SESSION['employeeID']."' ";
					$query .=",\"updatedDate\" = '".date('U')."' ";
					$query .="  where \"storageId\"='".$data_storage[$i]['storageId']."' ";
					$i++;
				}
				else
				{
					$query = "INSERT INTO \"tbl_invStorage\" (";
					$query .=" \"invId\" ";
					$query .=" ,\"styleId\" ";
					$query .=" ,\"colorId\" ";
					if($data_inv['sizeScaleId'] != "")$query .=" ,\"sizeScaleId\" ";
					if($data_inv['opt1ScaleId'] != "")$query .=" ,\"opt1ScaleId\" ";
					if($data_inv['opt2ScaleId'] != "")$query .=" ,\"opt2ScaleId\" ";
					$query .=" ,\"locationId\" ";
					if($room!="") $query .=" ,\"room\" ";
					if($wareHouseRow!="")$query .=" ,\"row\" ";
					if($rack!="")$query .=" ,\"rack\" ";
					if($shelf!="")$query .=" ,\"shelf\" ";
					if($box!="")$query .=" ,\"box\" ";
					$query .=" ,\"wareHouseQty\" ";					
					$query .=" ,\"createdBy\" ";
					$query .=" ,\"updatedBy\" ";
					$query .=" ,\"createdDate\" ";					
					$query .=" ,\"updatedDate\" ";
					$query .=")";
					$query .=" VALUES (";
					$query .=" '".$data_inv['inventoryId']."' ";
					$query .=" ,'".$data_inv['styleId']."' ";
					$query .=" ,'".$data_inv['colorId']."' ";
					if($data_inv['sizeScaleId'] != "")$query .=" ,'".$data_inv['sizeScaleId']."' ";
					if($data_inv['opt1ScaleId'] != "")$query .=" ,'".$data_inv['opt1ScaleId']."' ";
					if($data_inv['opt2ScaleId'] != "")$query .=" ,'".$data_inv['opt2ScaleId']."' ";
					$query .=" ,'".$data_inv['locationId']."' ";
					if($room!="")$query .=" ,'".$room."' ";
					if($wareHouseRow!="")$query .=" ,'".$wareHouseRow."' ";
					if($rack!="")$query .=" ,'".$rack."' ";
					if($shelf!="")$query .=" ,'".$shelf."' ";
					if($box!="")$query .=" ,'".$box."' ";
					$query .=" ,'".$qty."' ";					
					$query .=" ,'".$_SESSION['employeeID']."' ";
					$query .=" ,'".$_SESSION['employeeID']."' ";
					$query .=" ,'".date('U')."' ";
					$query .=" ,'".date('U')."' ";							
					$query .=" )";	
				}
				if($query != "")
				{
					$return_arr['type'] = "warehouse";
					if($data_inv['quantity'] != "" && $data_inv['quantity'] > 0)
						$qty += $data_inv['quantity'];
					if(!($result=pg_query($connection,$query))){
						$return_arr['error'] = pg_last_error($connection);
						echo json_encode($return_arr);
						return;
					}
					pg_free_result($result);
					$query = "";
				}			
				if($qty > 0)
				{
					$query = "UPDATE \"tbl_inventory\" SET ";
					$query .="\"quantity\" = '".$qty."' ";
					$query .=",\"newQty\" = '0' ";
					$query .=",\"isStorage\" = 1 ";
					$query .=",\"updatedBy\" = '".$_SESSION['employeeID']."' ";
					$query .=",\"updatedDate\" = '".date('U')."' ";
					$query .="  where \"inventoryId\"='".$data_inv['inventoryId']."' ";
					if(!($result=pg_query($connection,$query))){
						$return_arr['error'] = pg_last_error($connection);
						echo json_encode($return_arr);
						return;
					}
					pg_free_result($result);
					$query = "";
				}
				break;
			}
			case 'o':
			{
				$i = 0;
				if($location=="" || strlen($location) < 4)
				{
					$return_arr['error'] = "Location field should have minimum 4 characters.";
					echo json_encode($return_arr);
					return;
				}							 
				$found = 0;
				$query = "";
				if($data_inv['quantity'] != "" && $data_inv['quantity'] > 0)
					$qty = $data_inv['newQty'] - $data_inv['quantity'];
				else
					$qty = $data_inv['newQty'];
				for(; $i < count($data_storage); $i++)
				{
					if($data_storage[$i]['otherQty'] == "" || $data_storage[$i]['otherQty'] == 0)
					{
						$found = 1;
						break;	
					}
				}
				if($found)
				{
					$query = "UPDATE \"tbl_invStorage\" SET ";
					$query .="\"location\" = '".$location."' ";					
					$query .=",\"otherQty\" = '".$qty."' ";
					$query .=",\"updatedBy\" = '".$_SESSION['employeeID']."' ";
					$query .=",\"updatedDate\" = '".date('U')."' ";
					$query .="  where \"storageId\"='".$data_storage[$i]['storageId']."' ";
					$i++;
				}
				else
				{
					$query = "INSERT INTO \"tbl_invStorage\" (";
					$query .=" \"invId\" ";
					$query .=" ,\"styleId\" ";
					$query .=" ,\"colorId\" ";
					if($data_inv['sizeScaleId'] != "")$query .=" ,\"sizeScaleId\" ";
					if($data_inv['opt1ScaleId'] != "")$query .=" ,\"opt1ScaleId\" ";
					if($data_inv['opt2ScaleId'] != "")$query .=" ,\"opt2ScaleId\" ";
					$query .=" ,\"locationId\" ";					
					$query .=" ,\"location\" ";
					$query .=" ,\"otherQty\" ";					
					$query .=" ,\"createdBy\" ";
					$query .=" ,\"updatedBy\" ";
					$query .=" ,\"createdDate\" ";					
					$query .=" ,\"updatedDate\" ";
					$query .=")";
					$query .=" VALUES (";
					$query .=" '".$data_inv['inventoryId']."' ";
					$query .=" ,'".$data_inv['styleId']."' ";
					$query .=" ,'".$data_inv['colorId']."' ";
					if($data_inv['sizeScaleId'] != "")$query .=" ,'".$data_inv['sizeScaleId']."' ";
					if($data_inv['opt1ScaleId'] != "")$query .=" ,'".$data_inv['opt1ScaleId']."' ";
					if($data_inv['opt2ScaleId'] != "")$query .=" ,'".$data_inv['opt2ScaleId']."' ";
					$query .=" ,'".$data_inv['locationId']."' ";					
					$query .=" ,'".$location."' ";
					$query .=" ,'".$qty."' ";					
					$query .=" ,'".$_SESSION['employeeID']."' ";
					$query .=" ,'".$_SESSION['employeeID']."' ";
					$query .=" ,'".date('U')."' ";
					$query .=" ,'".date('U')."' ";							
					$query .=" )";	
				}
				if($query != "")
				{
					$return_arr['type'] = "other";
					if($data_inv['quantity'] != "" && $data_inv['quantity'] > 0)
						$qty += $data_inv['quantity'];
					if(!($result=pg_query($connection,$query))){
						$return_arr['error'] = pg_last_error($connection);
						echo json_encode($return_arr);
						return;
					}
					pg_free_result($result);
					$query = "";
				}			
				if($qty > 0)
				{
					$query = "UPDATE \"tbl_inventory\" SET ";
					$query .="\"quantity\" = '".$qty."' ";
					$query .=",\"newQty\" = '0' ";
					$query .=",\"isStorage\" = 1 ";
					$query .=",\"updatedBy\" = '".$_SESSION['employeeID']."' ";
					$query .=",\"updatedDate\" = '".date('U')."' ";
					$query .="  where \"inventoryId\"='".$data_inv['inventoryId']."' ";
					if(!($result=pg_query($connection,$query))){
						$return_arr['error'] = pg_last_error($connection);
						echo json_encode($return_arr);
						return;
					}
					pg_free_result($result);
					$query = "";
				}
				break;
			}
		}
	}
	else
	{
		$return_arr['name'] = "Storage information is already updated...";
	}
}
echo json_encode($return_arr);
exit;
?>