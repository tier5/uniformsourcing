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

$return_arr[0]['name'] = "";
$return_arr[0]['error'] = "";
$return_arr[0]['flag'] = 0;

if(isset($_POST['type']) && $_POST['type'] == "e")
{
	extract($_POST);
	if($colorId > 0)
	{
		$search = " and \"colorId\"=$colorId ";		
	}	
	if($styleId != "" && $scaleNameId != "")
	{
		$sql ='select * from "tbl_invStyle" where "styleId"='.$styleId;
		if(!($result=pg_query($connection,$sql))){
			$return_arr[0]['error'] = pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		}
		$row = pg_fetch_array($result);
		$data_style=$row;
		pg_free_result($result);

		$query2='Select "sizeScaleId" as "mainSizeId", "scaleSize" from "tbl_invScaleSize" where "scaleId"='.$data_style['scaleNameId'].' and "scaleSize" IS NOT NULL and "scaleSize" <>\'\' order by "mainOrder","sizeScaleId"';
	if(!($result2=pg_query($connection,$query2))){
		print("Failed OptionQuery: " . pg_last_error($connection));
		exit;
	}
	while($row2 = pg_fetch_array($result2)){
		$data_mainSize[]=$row2;}
	pg_free_result($result2);
	
	$query2='Select "sizeScaleId" as "opt1SizeId", "opt1Size" from "tbl_invScaleSize" where "scaleId"='.$data_style['scaleNameId'].' and "opt1Size" IS NOT NULL and "opt1Size" <>\'\' order by "opt1Order","sizeScaleId"';
	if(!($result2=pg_query($connection,$query2))){
		print("Failed OptionQuery: " . pg_last_error($connection));
		exit;
	}
	while($row2 = pg_fetch_array($result2)){
		$data_opt1Size[]=$row2;}
	pg_free_result($result2);
	
	$query2='Select "sizeScaleId" as "opt2SizeId", "opt2Size" from "tbl_invScaleSize" where "scaleId"='.$data_style['scaleNameId'].' and "opt2Size" IS NOT NULL and "opt2Size" <>\'\' order by "opt2Order","sizeScaleId"';
	if(!($result2=pg_query($connection,$query2))){
		print("Failed OptionQuery: " . pg_last_error($connection));
		exit;
	}
	while($row2 = pg_fetch_array($result2)){
		$data_opt2Size[]=$row2;}
	pg_free_result($result2);
		if($colorId > 0)
		{
			$query='select "sizeScaleId", price, "locationId","opt1ScaleId", "opt2ScaleId", quantity from "tbl_inventory" where "styleId"='.$data_style['styleId'].' and "isActive"=1'.$search.' order by "inventoryId"';
		}
		else
		{
		$query='select "sizeScaleId", price, "locationId","opt1ScaleId", "opt2ScaleId", quantity from "tbl_inventory" where "styleId"='.$data_style['styleId'].' and "colorId"='.$data_color[0]['colorId'].'  and "isActive"=1 order by "inventoryId"';
		}
		if(!($result=pg_query($connection,$query))){
			$return_arr[0]['error'] = pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		}
		while($row = pg_fetch_array($result)){
			$data_inv[]=$row;}
		pg_free_result($result);
		
		$query='select * from "tbl_invLocation" order by "locationId"';
		if(!($result=pg_query($connection,$query))){
			$return_arr[0]['error'] = pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		}
		while($row = pg_fetch_array($result)){
			$data_loc[]=$row;}
		pg_free_result($result);
		
		$locArr = array();
		if($data_style['locationIds'] != "")
		{
			$locArr = explode(",",$data_style['locationIds']);
		}
		for($i=0;$i<$locCount;$i++)
		{
			for($j=0; $j < $rowCount; $j++)
			{
				for($k = 0; $k < $mainCount; $k++)
				{
					if($hdnqty[$i][$j][$k] != $qty[$i][$j][$k])
					{
					    //print_r($invId[$i][$j][$k]);
                        $sql = '';
                        $sql = "SELECT * FROM \"tbl_inventory\" WHERE ";
                        $sql .= " \"styleId\"='".$data_style['styleId']."'";
                        $sql .= " and \"styleNumber\"='".$data_style['styleNumber']."'";
                        $sql .= " and \"scaleId\"='".$data_style['scaleNameId']."'";
                        if($k < count($data_mainSize))$sql .=" and \"sizeScaleId\"= '".$data_mainSize[$k]['mainSizeId']."'";
                        if($j < count($data_opt1Size))$sql .=" and \"opt1ScaleId\"= '".$data_opt1Size[$j]['opt1SizeId']."'";
                        if($k < count($data_opt2Size))$sql .=" and \"opt2ScaleId\"= '".$data_opt2Size[$k]['opt2SizeId']."'";
                        if($k < count($data_mainSize))$sql .=" and \"mainSize\"= '".$data_mainSize[$k]['scaleSize']."'";
                        if($j < count($data_opt1Size))$sql .=" and \"rowSize\"= '".$data_opt1Size[$j]['opt1Size']."'";
                        if($k < count($data_opt2Size))$sql .=" and \"columnSize\"= '".data_opt2Size[$k]['opt2Size']."'";
                        $sql .= " and \"isStorage\"='1'";
                        if(!($result=pg_query($connection,$sql))){
                            $return_arr[0]['error'] = pg_last_error($connection);
                            echo json_encode($return_arr);
                            return;
                        }
                        $row = pg_fetch_array($result);
                        $inv=$row;
                        pg_free_result($result);
                        if($inv != null){
                            $query = "";
                            $query = "UPDATE \"tbl_inventory\" SET ";
                            $query .="\"newQty\" = '".$qty[$i][$j][$k]."' ";
                            $query .=",\"isStorage\" = 0 ";
                            $query .=",\"updatedBy\" = '".$_SESSION['employeeID']."' ";
                            $query .=",\"updatedDate\" = '".date('U')."' ";
                            $query .="  where \"inventoryId\"='".$inv['inventoryId']."' ";
                        } else {
                            $notes = 'auto inventory';
                            $query = "";
                            $query = "INSERT INTO \"tbl_inventory\" (";
                            $query .=" \"styleId\" ";
                            $query .=" ,\"styleNumber\" ";
                            $query .=" ,\"scaleId\" ";
                            if($price[$k] != "") $query .=", \"price\" ";
                            $query .=", \"locationId\" ";
                            $query .=", \"quantity\" ";
                            $query .=", \"newQty\" ";
                            if($k < count($data_mainSize))$query .=", \"sizeScaleId\" ";
                            $query .=", \"colorId\" ";
                            if($j < count($data_opt1Size))$query .=", \"opt1ScaleId\" ";
                            if($k < count($data_opt2Size))$query .=", \"opt2ScaleId\" ";
                            $query .=", \"notes\" ";
                            if($k < count($data_mainSize))$query .=", \"mainSize\" ";
                            if($j < count($data_opt1Size)) $query .=", \"rowSize\" ";
                            if($k < count($data_opt2Size))$query .=", \"columnSize\" ";
                            $query .=", \"isStorage\" ";
                            $query .=", \"createdBy\" ";
                            $query .=", \"updatedBy\" ";
                            $query .=", \"createdDate\" ";
                            $query .=", \"updatedDate\" ";
                            $query .=")";
                            $query .=" VALUES (";
                            $query .=" '".$data_style['styleId']."' ";
                            $query .=" ,'".$data_style['styleNumber']."' ";
                            $query .=", '".$data_style['scaleNameId']."' ";
                            if($price[$k] != "") $query .=" ,'".$price[$k]."' ";
                            $query .=" ,'".$locArr[$i]."' ";
                            $query .=" ,0 ";
                            $query .=" ,'".$qty[$i][$j][$k]."' ";
                            if($k < count($data_mainSize))$query .=", '".$data_mainSize[$k]['mainSizeId']."' ";
                            $query .=", '$colorId' ";
                            if($j < count($data_opt1Size))$query .=", '".$data_opt1Size[$j]['opt1SizeId']."' ";
                            if($k < count($data_opt2Size))$query .=", '".$data_opt2Size[$k]['opt2SizeId']."' ";
                            $query .=" ,'$notes' ";
                            if($k < count($data_mainSize))$query .=", '".$data_mainSize[$k]['scaleSize']."' ";
                            if($j < count($data_opt1Size))$query .=", '".$data_opt1Size[$j]['opt1Size']."' ";
                            if($k < count($data_opt2Size))$query .=", '".$data_opt2Size[$k]['opt2Size']."' ";
                            $query .=" ,0 ";
                            $query .=" ,'".$_SESSION['employeeID']."' ";
                            $query .=" ,'".$_SESSION['employeeID']."' ";
                            $query .=" ,'".date('U')."' ";
                            $query .=" ,'".date('U')."' ";
                            $query .=" )";
                        }
                        if($query != "")
                        {
                            $return_arr[0]['flag'] = 1;
                            if(!($result=pg_query($connection,$query))){
                                $return_arr[0]['error'] = pg_last_error($connection);
                                echo json_encode($return_arr);
                                return;
                            }
                            pg_free_result($result);
                            $query = "";
                        }
                        /*$query = "";
						if((is_numeric($qty[$i][$j][$k])) && ($hdnqty[$i][$j][$k] == 0 && $hdnNewQty[$i][$j][$k] == 0 ))
						{
                            if($invId[$i][$j][$k] > 0){
                                //echo "if".$invId[$i][$j][$k];
                                $query = "UPDATE \"tbl_inventory\" SET ";
                                $query .="\"newQty\" = '".$qty[$i][$j][$k]."' ";
                                $query .=",\"isStorage\" = 0 ";
                                $query .=",\"updatedBy\" = '".$_SESSION['employeeID']."' ";
                                $query .=",\"updatedDate\" = '".date('U')."' ";
                                $query .="  where \"inventoryId\"='".$invId[$i][$j][$k]."' ";
                            } else {
                                $notes = 'auto inventory';
                                $query = "INSERT INTO \"tbl_inventory\" (";
                                $query .=" \"styleId\" ";
                                $query .=" ,\"styleNumber\" ";
                                $query .=" ,\"scaleId\" ";
                                if($price[$k] != "") $query .=", \"price\" ";
                                $query .=", \"locationId\" ";
                                $query .=", \"quantity\" ";
                                $query .=", \"newQty\" ";
                                if($k < count($data_mainSize))$query .=", \"sizeScaleId\" ";
                                $query .=", \"colorId\" ";
                                if($j < count($data_opt1Size))$query .=", \"opt1ScaleId\" ";
                                if($k < count($data_opt2Size))$query .=", \"opt2ScaleId\" ";
                                $query .=", \"notes\" ";
                                if($k < count($data_mainSize))$query .=", \"mainSize\" ";
                                if($j < count($data_opt1Size)) $query .=", \"rowSize\" ";
                                if($k < count($data_opt2Size))$query .=", \"columnSize\" ";
                                $query .=", \"isStorage\" ";
                                $query .=", \"createdBy\" ";
                                $query .=", \"updatedBy\" ";
                                $query .=", \"createdDate\" ";
                                $query .=", \"updatedDate\" ";
                                $query .=")";
                                $query .=" VALUES (";
                                $query .=" '".$data_style['styleId']."' ";
                                $query .=" ,'".$data_style['styleNumber']."' ";
                                $query .=", '".$data_style['scaleNameId']."' ";
                                if($price[$k] != "") $query .=" ,'".$price[$k]."' ";
                                $query .=" ,'".$locArr[$i]."' ";
                                $query .=" ,0 ";
                                $query .=" ,'".$qty[$i][$j][$k]."' ";
                                if($k < count($data_mainSize))$query .=", '".$data_mainSize[$k]['mainSizeId']."' ";
                                $query .=", '$colorId' ";
                                if($j < count($data_opt1Size))$query .=", '".$data_opt1Size[$j]['opt1SizeId']."' ";
                                if($k < count($data_opt2Size))$query .=", '".$data_opt2Size[$k]['opt2SizeId']."' ";
                                $query .=" ,'$notes' ";
                                if($k < count($data_mainSize))$query .=", '".$data_mainSize[$k]['scaleSize']."' ";
                                if($j < count($data_opt1Size))$query .=", '".$data_opt1Size[$j]['opt1Size']."' ";
                                if($k < count($data_opt2Size))$query .=", '".$data_opt2Size[$k]['opt2Size']."' ";
                                $query .=" ,0 ";
                                $query .=" ,'".$_SESSION['employeeID']."' ";
                                $query .=" ,'".$_SESSION['employeeID']."' ";
                                $query .=" ,'".date('U')."' ";
                                $query .=" ,'".date('U')."' ";
                                $query .=" )";
                            }
						}
						else if(is_numeric($qty[$i][$j][$k]) && $invId[$i][$j][$k] > 0)
						{
							$query = "UPDATE \"tbl_inventory\" SET ";
							$query .="\"newQty\" = '".$qty[$i][$j][$k]."' ";
							$query .=",\"isStorage\" = 0 ";
							$query .=",\"updatedBy\" = '".$_SESSION['employeeID']."' ";
							$query .=",\"updatedDate\" = '".date('U')."' ";
							$query .="  where \"inventoryId\"='".$invId[$i][$j][$k]."' ";
						}*/
						/*else if($invId[$i][$j][$k] > 0)
						{
							$query = "DELETE FROM \"tbl_inventory\" where \"inventoryId\"='".$invId[$i][$j][$k]."' ";
						}*/
						/*else
						{
							$return_arr[0]['error'] = "Invalid quantity entered for some locations and are not updated !! ";
						}
						if($query != "")
						{
							$return_arr[0]['flag'] = 1;
							if(!($result=pg_query($connection,$query))){
								$return_arr[0]['error'] = pg_last_error($connection);
								echo json_encode($return_arr);
								return;
							}
							pg_free_result($result);
							$query = "";
						}
						
					}
					else if($invId[$i][$j][$k] > 0)
					{
						if($hdnNewQty[$i][$j][$k] > 0)
							$return_arr[0]['flag'] = 1;
						if($price[$k] != "")
						{
							$query = "UPDATE \"tbl_inventory\" SET ";
							$query .="\"price\" = '".$price[$k]."' ";
							$query .=",\"updatedBy\" = '".$_SESSION['employeeID']."' ";
							$query .=",\"updatedDate\" = '".date('U')."' ";
							$query .="  where \"inventoryId\"='".$invId[$i][$j][$k]."' ";
							if(!($result=pg_query($connection,$query))){
								$return_arr[0]['error'] = pg_last_error($connection);
								echo json_encode($return_arr);
								return;
							}
							pg_free_result($result);
							$query = "";
						}*/
					}
				}
			}
		}
	}		
}
echo json_encode($return_arr);
exit;
?>