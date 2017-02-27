<?php 
require('Application.php');
require('../../jsonwrapper/jsonwrapper.php');
$return_arr = array();
extract($_POST);
$return_arr['name']="";
$return_arr['error'] = "";
$return_arr['qty'] = 0;
if(isset($_POST['type']))
{
	if(($storageID > 0) && ($invID >0))
	{
		$sql='select "wareHouseQty","otherQty","conveyorQty" from "tbl_invStorage" where "storageId" ='.$storageID.' and "invId" ='.$invID;
		if(!($result=pg_query($connection,$sql))){
			$return_arr['error'] = pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		}
		while($row = pg_fetch_array($result)){
		$storageData=$row;}
		pg_free_result($result);
		$sql='select quantity, "newQty" from tbl_inventory where "inventoryId" ='.$invID;
		if(!($result=pg_query($connection,$sql))){
			$return_arr['error'] = pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		}
		while($row = pg_fetch_array($result)){
		$data_invQty=$row;}
		pg_free_result($result);			
		switch($type)
		{
			case "c_delete":
			{
				if($qty >= $storageData['conveyorQty'])
				{
					$return_arr['qty'] = $qty - $storageData['conveyorQty'];
					$qty = $storageData['conveyorQty'];				
					if(($storageData['wareHouseQty']=="" || $storageData['wareHouseQty']==0)  && ($storageData['otherQty']=="" || $storageData['otherQty']==0))
					{
						$sql='delete from "tbl_invStorage" where "storageId" ='.$storageID.' and "invId" ='.$invID;
					}
					else
					{
						$sql='update "tbl_invStorage" set "conveyorQty"=null,"conveyorSlotId"=null where "storageId" ='.$storageID.' and "invId" ='.$invID;
					}
				}
				else
				{
					$qty = $storageData['conveyorQty'] - $qty;
					$sql='update "tbl_invStorage" set "conveyorQty"='.$qty.' where "storageId" ='.$storageID.' and "invId" ='.$invID;
				}
				if(!($result=pg_query($connection,$sql))){
					$return_arr['error'] = pg_last_error($connection);
					echo json_encode($return_arr);
					return;
					}
					pg_free_result($result);
				if($data_invQty['quantity']!="")
				{					
					$invQty = $data_invQty['quantity']-$qty;
					if($invQty <= 0)
					{
						$sql='delete from tbl_inventory where "inventoryId" ='.$invID;
					}
					else
					{
						if($data_invQty['newQty'] > 0 &&  $data_invQty['newQty'] >= $invQty)
							$sql='update tbl_inventory set quantity='.$invQty. ' , "newQty"=0 , "isStorage" = 1 where "inventoryId" ='.$invID;
						else
							$sql='update tbl_inventory set quantity='.$invQty. ' where "inventoryId" ='.$invID;
					}
					if(!($result=pg_query($connection,$sql))){
						$return_arr['error'] = pg_last_error($connection);
						echo json_encode($return_arr);
						return;
						}
						pg_free_result($result);
				}
				break;
			}			
			case "w_update":{
                            $sql_up="";
                        $sql='update "tbl_invStorage" set ';
                     if(isset($rack)&&$rack!="")
                     { if($sql_up!="") $sql_up.=",";
                        $sql_up.='"rack"=\''.pg_escape_string ($rack).'\'';}
                     if(isset($shelf)&&$shelf!="")
                          { if($sql_up!="") $sql_up.=",";
                        $sql_up.='"shelf"=\''.pg_escape_string ($shelf).'\'';}
                     if(isset($box)&&$box!="")
                          { if($sql_up!="") $sql_up.=",";
                        $sql_up.='"box"=\''.pg_escape_string ($box).'\'';}
                      if(isset($_POST['row'])&&$_POST['row']!="")
                           { if($sql_up!="") $sql_up.=",";
                        $sql_up.='"row"=\''.pg_escape_string ($_POST['row']).'\'';}
                        
                      if($sql_up!=""){
                        $sql.=$sql_up.' where "storageId" ='.$storageID.' and "invId" ='.$invID;
   
                        if(!($result=pg_query($connection,$sql))){
			$return_arr['error'] = pg_last_error($connection);
			echo json_encode($return_arr);
			return;
			}
			pg_free_result($result);
                      }$sql='';
                      
				if($qty > $storageData['wareHouseQty'])
				{
					$wareQty=$qty-$storageData['wareHouseQty'];
					if($data_invQty['quantity']!="")
					$updatedQty=$wareQty + $data_invQty['quantity'];
				}
				else if(($qty >=0) && ($qty < $storageData['wareHouseQty']))
				{
					$wareQty=$storageData['wareHouseQty']-$qty;
					if(($data_invQty['quantity']!="")&& ($data_invQty['quantity'] >= $wareQty))
					{
					$updatedQty=$data_invQty['quantity']- $wareQty;
					}
				}
					if(($qty==0) &&($storageData['conveyorQty']=="" || $storageData['conveyorQty']==0)  && ($storageData['otherQty']=="" || $storageData['otherQty']==0))
					{
						$sql='delete from "tbl_invStorage" where "storageId" ='.$storageID.' and "invId" ='.$invID;
					}
					else
					{
					$sql='update "tbl_invStorage" set "wareHouseQty"=\''.$qty.'\' where "storageId" ='.$storageID.' and "invId" ='.$invID;
					}
					if(!($result=pg_query($connection,$sql))){
						$return_arr['error'] = pg_last_error($connection);
						echo json_encode($return_arr);
						return;
						}
						pg_free_result($result);
					if($updatedQty > 0)
					{
						$sql='update tbl_inventory set quantity='.$updatedQty. ' where "inventoryId" ='.$invID;
					}
					else if($qty <= 0)
					{
						$sql='delete from tbl_inventory where "inventoryId" ='.$invID;
					}
					if(!($result=pg_query($connection,$sql))){
						$return_arr['error'] = pg_last_error($connection);
						echo json_encode($return_arr);
						return;
						}
						pg_free_result($result);
					break;
			}
			case "w_delete":
			{
				if($qty >= $storageData['wareHouseQty'])
				{
					$return_arr['qty'] = $qty - $storageData['wareHouseQty'];
					$qty = $storageData['wareHouseQty'];
					if(($storageData['conveyorQty']=="" || $storageData['conveyorQty']==0)  && ($storageData['otherQty']=="" || $storageData['otherQty']==0))
					{
						$sql='delete from "tbl_invStorage" where "storageId" ='.$storageID.' and "invId" ='.$invID;
					}
					else
					{
						$sql='update "tbl_invStorage" set room=null,"row"=null,rack=null,shelf=null,"wareHouseQty"=null where "storageId" ='.$storageID.' and "invId" ='.$invID;	
					}
				}
				else
				{
					$sql='update "tbl_invStorage" set "wareHouseQty"='.($storageData['wareHouseQty'] - $qty).' where "storageId" ='.$storageID.' and "invId" ='.$invID;
				}
				if(!($result=pg_query($connection,$sql))){
					$return_arr['error'] = pg_last_error($connection);
					echo json_encode($return_arr);
					return;
					}
					pg_free_result($result);
						if($data_invQty['quantity']!="")
						{
							$invQty = $data_invQty['quantity']-$qty;
							if($invQty <= 0)
							{
								$sql='delete from tbl_inventory where "inventoryId" ='.$invID;
							}
							else
							{
								
								if($data_invQty['newQty'] > 0 &&  $data_invQty['newQty'] >= $invQty)
									$sql='update tbl_inventory set quantity='.$invQty. ' , "newQty"=0, "isStorage" = 1 where "inventoryId" ='.$invID;
								else
									$sql='update tbl_inventory set quantity='.$invQty. ' where "inventoryId" ='.$invID;
							}
							if(!($result=pg_query($connection,$sql))){
								$return_arr['error'] = pg_last_error($connection);
								echo json_encode($return_arr);
								return;
								}
								pg_free_result($result);
						}
					break;
			}
			case "o_update" :
			{
				if($qty > $storageData['otherQty'])
				{
					$otherQty=$qty-$storageData['otherQty'];
					if($data_invQty['quantity']!="")
					$updatedQty=$otherQty + $data_invQty['quantity'];
				}
				else if(($qty >=0) && ($qty < $storageData['otherQty']))
				{
					$otherQty=$storageData['otherQty']-$qty;
					if(($data_invQty['quantity']!="")&& ($data_invQty['quantity'] >= $otherQty))
					{
					$updatedQty=$data_invQty['quantity']- $otherQty;
					}
				}
					if(($qty==0)&&($storageData['wareHouseQty']=="" || $storageData['wareHouseQty']==0)  && ($storageData['conveyorQty']=="" || $storageData['conveyorQty']==0))
					{
						$sql='delete from "tbl_invStorage" where "storageId" ='.$storageID.' and "invId" ='.$invID;
					}
					else
					{
						$sql='update "tbl_invStorage" set "otherQty"=\''.$qty.'\' where "storageId" ='.$storageID.' and "invId" ='.$invID;
					}
					if(!($result=pg_query($connection,$sql))){
						$return_arr['error'] = pg_last_error($connection);
						echo json_encode($return_arr);
						return;
						}
						pg_free_result($result);
					if($updatedQty > 0)
					{
						$sql='update tbl_inventory set quantity='.$updatedQty. ' where "inventoryId" ='.$invID;
					}
					else
					{
						$sql='delete from tbl_inventory where "inventoryId" ='.$invID;
					}
					if(!($result=pg_query($connection,$sql))){
						$return_arr['error'] = pg_last_error($connection);
						echo json_encode($return_arr);
						return;
						}
						pg_free_result($result);
					break;
			}
			case "o_delete" :
			{
				if($qty >= $storageData['otherQty'])
				{
					$return_arr['qty'] = $qty - $storageData['otherQty'];
					$qty = $storageData['otherQty'];
					if(($storageData['wareHouseQty']=="" || $storageData['wareHouseQty']==0)  && ($storageData['otherQty']=="" || $storageData['otherQty']==0))
					{
						$sql='delete from "tbl_invStorage" where "storageId" ='.$storageID.' and "invId" ='.$invID;
					}
					else
					{
						$sql='update "tbl_invStorage" set "otherQty"=null,"location"=null where "storageId" ='.$storageID.' and "invId" ='.$invID;
					}
				}
				else
				{
					$sql='update "tbl_invStorage" set "otherQty"='.($storageData['otherQty'] - $qty).' where "storageId" ='.$storageID.' and "invId" ='.$invID;
				}
				if(!($result=pg_query($connection,$sql))){
					$return_arr['error'] = pg_last_error($connection);
					echo json_encode($return_arr);
					return;
					}
					pg_free_result($result);
						if($data_invQty['quantity']!="")
						{
							$invQty = $data_invQty['quantity']-$qty;
							if($invQty <= 0)
							{
								$sql='delete from tbl_inventory where "inventoryId" ='.$invID;
							}
							else
							{
								if($data_invQty['newQty'] > 0 &&  $data_invQty['newQty'] >= $invQty)
									$sql='update tbl_inventory set quantity='.$invQty. ' , "newQty"=0, "isStorage" = 1 where "inventoryId" ='.$invID;
								else
									$sql='update tbl_inventory set quantity='.$invQty. ' where "inventoryId" ='.$invID;
							}
							if(!($result=pg_query($connection,$sql))){
								$return_arr['error'] = pg_last_error($connection);
								echo json_encode($return_arr);
								return;
								}
								pg_free_result($result);
						}
					break;
			}
		}
	}
}
echo json_encode($return_arr);
exit;
?>