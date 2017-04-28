<?php require('Application.php');
	require('../../jsonwrapper/jsonwrapper.php');
	if($debug == "on"){
	require('../../header.php');
	foreach($_POST as $key=>$value) {
		if($key!="submit") { echo "$key = $value<br/>"; }
	}
}
	$return_arr = array();
	extract($_POST);
	$return_arr['name'] = "";
	$return_arr['error'] = "";
	$return_arr['id'] = "";
	//print_r($_POST['styleId']);
if(isset($_POST['type']))
{
	if($styleNumber == "" && $type!='e')
	{		
		$return_arr['name'] = "Please Enter Style Number before submiting.";
	}
	else if($multipleLocation == "" || $multipleLocation == 0)
	{
		$return_arr['name'] = "Please select atleast one location";
	}
	if($return_arr['name'] !="")
	{
		echo json_encode($return_arr);			
		return;
	}
	if(count($imageName) >0 )
	{
		for($i=0; $i < count($clolorName); $i++)
		{
			if($clolorName[$i] =="")
			{
				$return_arr['name'] = "Please enter color name before submitting";
				echo json_encode($return_arr);			
				return;
			}
		}
	}
	else
	{
		$return_arr['name'] = "Please enter color name before submitting";
		echo json_encode($return_arr);			
		return;
	}
		switch ($type)			
		{
			case "a":
			{
				$styleNumber=pg_escape_string($styleNumber);
				$query1 = "select count(1) from \"tbl_invStyle\" where \"styleNumber\" = '$styleNumber'";
				if(!($result1=pg_query($connection,$query1))){
					$return_arr['error'] = "Error while processing style information!";
					echo json_encode($return_arr);			
					return;
				}
				$row = pg_fetch_row($result1);
				pg_free_result($result1);			
				if($row[0] != 0)
				{
					$return_arr['error'] = "Style Number you entered already exist in Database.";
					echo json_encode($return_arr);			
					return;
				}
                                if(trim($barcode_name)!=""){
                               
                                 $newname=pathinfo($barcode_name,PATHINFO_FILENAME).".".strtolower(pathinfo($barcode_name, PATHINFO_EXTENSION));
                                //$newname=$styleNumber.".".$path_parts['extension'];
                              //  copy($mydirectory."/uploadFiles/inventory/images/".$barcode_name,$mydirectory."/uploadFiles/inventory/images/".$newname);
                               // copy($mydirectory."/uploadFiles/inventory/imagesthumbs".$barcode_name,$mydirectory."/uploadFiles/inventory/images/thumbs".$newname);
                               // unlink($mydirectory."/uploadFiles/inventory/images/".$barcode_name);
                                }
				$query1="INSERT INTO \"tbl_invStyle\" (";
				$query1.=" \"styleNumber\" ";
                                $query1.=" ,\"barcode\" ";
				$query1.=" ,\"scaleNameId\" ";
			    $query1.=", \"garmentId\" ";
				$query1.=", \"fabricId\" ";
				$query1.=", \"sex\" ";
				if($price) $query1.=", \"price\" ";
				$query1.=", \"locationIds\" ";
				if($client) $query1.=", \"clientId\" ";
				if($notes) $query1.=", \"notes\" ";
				$query1.=")";
				$query1.=" VALUES (";
				$query1.=" '$styleNumber' ";
                                if($newname!="") $query1.=", '".pg_escape_string($newname)."' ";
					else $query1.=", null ";
				$query1.=", '$sizeScale' ";
				if($garment>0) $query1.=", '$garment' ";
				else $query1.=", '0' ";
				if($fabric>0) $query1.=" ,'$fabric' ";
				else $query1.=", '0' ";
				if($sex!="") $query1.=" ,'$sex' ";
				else $query1.=", '0' ";
				if($price) $query1.=" ,'$price' ";
				if($multipleLocation>0) $query1.=" ,'$multipleLocation' ";
				else $query1.=", '0' ";
				if($client) $query1.=" ,'$client' ";
				if($notes) $query1.=" ,'".pg_escape_string($notes)."' ";
				$query1.=" )";
                                //echo $query1;
				if(!($result=pg_query($connection,$query1))){
					$return_arr['error'] ="Error while storing style information to database!";	
					echo json_encode($return_arr);
					return;
				}
				pg_free_result($result); 
				$sql='Select "styleId" from "tbl_invStyle" where "styleNumber"=\''.$styleNumber.'\'';
				if(!($result_sql=pg_query($connection,$sql))){
					$return_arr['error'] ="Error while storing style information to database!";	
					echo json_encode($return_arr);
					return;
				}
				$row=pg_fetch_array($result_sql);
				$return_arr['id'] = $row['styleId'];
				pg_free_result($result_sql);
				
				if($return_arr['id'] != "")
				{
					for($i=0; $i < count($clolorName); $i++)
					{ $imageName[$i]=  pg_escape_string($imageName[$i]);
						$check = stristr($imageName[$index],"_".$return_arr['id']);
                                                
						if(!$check)
						{
                                                   
                                                    
							$fileName = $imageName[$i];
							$imageName[$i] = substr($imageName[$i],0,(strlen($imageName[$i])-4))."_".$return_arr['id'].substr($imageName[$i],(strlen($imageName[$i])-4));
							if(file_exists("$upload_dir_image"."$fileName")) {
								@ rename("$upload_dir_image"."$fileName", "$upload_dir_image"."$imageName[$i]");
							}	
                                                        if(file_exists("$upload_dir_image"."thumbs/$fileName")) {
								@ rename("$upload_dir_image"."thumbs/$fileName", "$upload_dir_image"."thumbs/$imageName[$i]");
							}
						}						
						$query1="INSERT INTO \"tbl_invColor\" (";
						$query1.=" \"styleId\"";
						if($clolorName[$i] != "") $query1.= ", \"name\"";
						$query1.=", \"image\" ) VALUES(";
						$query1.=" '".$return_arr['id']."' ";
						if($clolorName[$i] != "")$query1.=", '".pg_escape_string($clolorName[$i])."' ";
						$query1.=", '".pg_escape_string($imageName[$i])."')";						
						if(!($result=pg_query($connection,$query1))){
							$return_arr['error'] ="Error while storing style information to database!";	
							echo json_encode($return_arr);
							return;
						}
						pg_free_result($result);
					}
				}
				break;
			}
			case "e":
			{
				if($styleId > 0)
				{
					$sql='select * from "tbl_invStyle" where "styleId"='.$styleId;
					if(!($result=pg_query($connection,$sql))){
					print("Failed query1: " . pg_last_error($connection));
					exit;
					}
					while($row = pg_fetch_array($result)){
					$data_style=$row;
					}
					pg_free_result($result);
					$queryx = "SELECT * from \"tbl_invColor\" where \"styleId\"='$styleId' order by \"colorId\"  asc";
					if(!($resultx=pg_query($connection,$queryx))){
						
					}
					while($rowx = pg_fetch_array($resultx)){
						$data_colorx[] = $rowx;
					}
					pg_free_result($resultx);

                                    if(trim($barcode_name)!=""){
                            
                                     $newname=pathinfo($barcode_name,PATHINFO_FILENAME).".".strtolower(pathinfo($barcode_name, PATHINFO_EXTENSION));
                                     //echo $newname;
                                    }
					$return_arr['id'] = $styleId;
					$query_Name="UPDATE \"tbl_invStyle\" SET  \"isActive\"=1 ";
					if($styleNumber)$query_Name.=" ,\"styleNumber\" = '$styleNumber'";
					if($sizeScale)$query_Name.=", \"scaleNameId\" = '$sizeScale' ";
					$query_Name.=", \"garmentId\" = '$garment' ";
					if($newname != '')
                                        $query_Name.=", \"barcode\" = '".pg_escape_string($newname)."' ";
					//else $query_Name.=", \"barcode\" = null ";
					$query_Name.=", \"fabricId\" = '$fabric' ";
					$query_Name.=", \"sex\" = '$sex' ";
					$query_Name.=", \"price\" = '$price' ";
					$query_Name.=", \"locationIds\" = '$multipleLocation' ";
					$query_Name.=", \"clientId\" = '$client' ";
					$query_Name.=", \"notes\" = '".pg_escape_string($notes)."' ";			
					$query_Name.="  where \"styleId\"='$styleId' ";
					if(!($result=pg_query($connection,$query_Name))){
						$return_arr['error'] = pg_last_error($connection);
						echo json_encode($return_arr);
						
						return;
					}
					pg_free_result($result);
					$location = explode(',',$multipleLocation);
					$search_query= "";
					for($i=0; $i< count($location); $i++)
					{
						$search_query.=' and "locationId" <> \''.$location[$i].'\'';
					}
					$sql ='select "inventoryId" from tbl_inventory where "isActive" =1 and "styleId"='.$styleId.$search_query;
					if(!($result=pg_query($connection,$sql)))
					{
						$return_arr['error'] = pg_last_error($connection);
						echo json_encode($return_arr);
						return;
					}
					while($row = pg_fetch_array($result))
					{
						$data_inv[] = $row;
					}
					pg_free_result($result);
					$query = "";
					$del = 1;
					for($k=0; $k < count($data_inv); $k++)
					{
						$query .='delete from "tbl_invStorage" where "invId"='.$data_inv[$k]['inventoryId'].';';							
						$query .='delete from tbl_inventory where "inventoryId" ='.$data_inv[$k]['inventoryId'].';';	
						if($k > ($del*10))
						{
							if(!($result=pg_query($connection,$query)))
							{
								$return_arr['error'] = pg_last_error($connection);
								echo json_encode($return_arr);
								return;
							}
								pg_free_result($result);
								$query = "";
								$del +=1;
						}
					}
					if($query !="")
					{
						if(!($result=pg_query($connection,$query)))
						{
							$return_arr['error'] = pg_last_error($connection);
							echo json_encode($return_arr);
							return;
						}
						pg_free_result($result);
					}
					$query = "SELECT * from \"tbl_invColor\" where \"styleId\"='$styleId' order by \"colorId\"  asc";
					if(!($result=pg_query($connection,$query))){
						$return_arr['error'] = pg_last_error($connection);
						echo json_encode($return_arr);
						return;
					}
					while($row = pg_fetch_array($result)){
						$data_color[] = $row;
					}
					pg_free_result($result);
					$index=0;
					for(; $index < count($clolorName); $index++)
					{
						$check = stristr($imageName[$index],"_".$styleId);
						if(!$check)
						{
							$fileName = $imageName[$index];
							$imageName[$index] = substr($imageName[$index],0,(strlen($imageName[$index])-4))."_".$styleId.substr($imageName[$index],(strlen($imageName[$index])-4));
							if(file_exists("$upload_dir_image"."$fileName")) {
								@ rename("$upload_dir_image"."$fileName", "$upload_dir_image"."$imageName[$index]");
							}
                                                        if(file_exists("$upload_dir_image"."thumbs/{$fileName}")) {
								@ rename("$upload_dir_image"."thumbs/{$fileName}", "$upload_dir_image"."thumbs/{$imageName[$index]}");                                                               
							}
						}
						if($index < count($data_color))
						{
                                                     $newname=pathinfo($imageName[$index],PATHINFO_FILENAME).".".strtolower(pathinfo($imageName[$index], PATHINFO_EXTENSION));
							$query = "UPDATE \"tbl_invColor\" SET \"styleId\"='$styleId'";
							$query .= ", \"name\" = '".pg_escape_string($clolorName[$index])."' ";
							$query .= ", \"image\" = '".pg_escape_string($newname)."' ";
							$query .= "where \"colorId\"='".pg_escape_string($data_color[$index]['colorId'])."' ";							
						}
						else
						{
                                                    $newname=pathinfo($imageName[$index],PATHINFO_FILENAME).".".strtolower(pathinfo($imageName[$index], PATHINFO_EXTENSION));
							$query="INSERT INTO \"tbl_invColor\" (";
							$query.=" \"styleId\"";
							if($clolorName[$index] != "") $query.= ", \"name\"";
							$query.=", \"image\" ) VALUES(";
							$query.=" '$styleId' ";
							if($clolorName[$index] != "")$query.=", '".pg_escape_string($clolorName[$index])."' ";
							$query.=", '".pg_escape_string($newname)."')";
						}	
                                               // echo $query;
						if(!($result=pg_query($connection,$query))){
							$return_arr['error'] = pg_last_error($connection);
							echo json_encode($return_arr);
							return;
						}
					}
					for(; $index < count($data_color); $index++)
					{
						$query = "DELETE from \"tbl_invColor\" where \"colorId\"='".$data_color[$index]['colorId']."'";
						if(!($result=pg_query($connection,$query))){
							$return_arr['error'] = pg_last_error($connection);
							echo json_encode($return_arr);
							return;
						}
					}
				}
				break;
			}
		}
}
echo json_encode($return_arr);
if($return_arr['error']=="" && $type=="e"){
		$sql='select * from "tbl_invStyle" where "styleId"='.$styleId;
		if(!($result=pg_query($connection,$sql))){
		}
		while($row = pg_fetch_array($result)){
		$data_style_new=$row;
		}
		pg_free_result($result);
		$style_array=array();
		if($data_style['styleNumber']!=$data_style_new['styleNumber']){
			$style_array['Style Number'][0]=$data_style['styleNumber'];
			$style_array['Style Number'][1]=$data_style_new['styleNumber'];
		}
		if($data_style['scaleNameId']!=$data_style_new['scaleNameId']){
			if($data_style['scaleNameId']!=""){
				$query1='Select Distinct "scaleName","scaleId" from "tbl_invScaleName" where "scaleId"='.$data_style['scaleNameId'];
				if(!($result_cnt=pg_query($connection,$query1))){	
				}
				while($row_cnt = pg_fetch_array($result_cnt)){
					$style_array['Scale Name'][0]=$row_cnt['scaleName'];
				}
				pg_free_result($result_cnt);
			}else{
				$style_array['Scale Name'][0]="";
			}
			if($data_style_new['scaleNameId']!=""){
				$query1='Select Distinct "scaleName","scaleId" from "tbl_invScaleName" where "scaleId"='.$data_style_new['scaleNameId'];
				if(!($result_cnt=pg_query($connection,$query1))){
				}
				while($row_cnt = pg_fetch_array($result_cnt)){
					$style_array['Scale Name'][1]=$row_cnt['scaleName'];
				}
				pg_free_result($result_cnt);
			}else{
				$style_array['Scale Name'][1]="";
			}	
		}
		if($data_style['garmentId']!=$data_style_new['garmentId']){
			if($data_style['garmentId']!=""){
				$query1='Select "garmentID","garmentName" from "tbl_garment" where "garmentID"='.$data_style['garmentId'];
				if(!($result_cnt=pg_query($connection,$query1))){
				}
				while($row_cnt = pg_fetch_array($result_cnt)){
					$style_array['Garment Name'][0]=$row_cnt['garmentName'];
				}
				pg_free_result($result_cnt);
			}else{
				$style_array['Garment Name'][0]="";
			}
			if($data_style_new['garmentId']!=""){
				$query1='Select "garmentID","garmentName" from "tbl_garment" where "garmentID"='.$data_style_new['garmentId'];
				if(!($result_cnt=pg_query($connection,$query1))){
				}
				while($row_cnt = pg_fetch_array($result_cnt)){
					$style_array['Garment Name'][1]=$row_cnt['garmentName'];
				}
				pg_free_result($result_cnt);
			}else{
				$style_array['Garment Name'][1]="";
			}
		}
		if($data_style['fabricId']!=$data_style_new['fabricId']){
			if($data_style['fabricId']!=""){
				$query1='Select "fabricID","fabName" from "tbl_fabrics" where "fabricID"='.$data_style['fabricId'];
				if(!($result_cnt=pg_query($connection,$query1))){
				}
				while($row_cnt = pg_fetch_array($result_cnt)){
					$style_array['Fabric Name'][0]=$row_cnt['fabName'];
				}
				pg_free_result($result_cnt);
			}else{
				$style_array['Fabric Name'][0]="";
			}
			if($data_style_new['fabricId']!=""){
				$query1='Select "fabricID","fabName" from "tbl_fabrics" where "fabricID"='.$data_style_new['fabricId'];
				if(!($result_cnt=pg_query($connection,$query1))){
				}
				while($row_cnt = pg_fetch_array($result_cnt)){
					$style_array['Fabric Name'][1]=$row_cnt['fabName'];
				}
				pg_free_result($result_cnt);
			}else{
				$style_array['Fabric Name'][1]="";
			}
		}
		if($data_style['sex']!=$data_style_new['sex']){
			$style_array['Sex'][0]=$data_style['sex'];
			$style_array['Sex'][1]=$data_style_new['sex'];
		}
		if($data_style['price']!=$data_style_new['price']){
			$style_array['Price'][0]=$data_style['price'];
			$style_array['Price'][1]=$data_style_new['price'];
		}
		if($data_style['notes']!=$data_style_new['notes']){
			$style_array['Note'][0]=$data_style['notes'];
			$style_array['Note'][1]=$data_style_new['notes'];
		}
		if($data_style['barcode']!=$data_style_new['barcode']){
			$style_array['Barcode'][0]=$data_style['barcode'];
			$style_array['Barcode'][1]=$data_style_new['barcode'];
		}
		$queryy = "SELECT * from \"tbl_invColor\" where \"styleId\"='$styleId' order by \"colorId\"  asc";
					if(!($resulty=pg_query($connection,$queryy))){	
					}
					while($rowy = pg_fetch_array($resulty)){
						$data_colory[] = $rowy;
					}
					pg_free_result($resulty);
					foreach ($data_colory as $new_key => $color_new) {
						foreach ($data_colorx as $old_key => $color_old) {
							if($color_new['name']==$color_old['name'] && $color_new['image']==$color_old['image']){
								unset($data_colory[$new_key]);
								unset($data_colorx[$old_key]);
							}
						}
					}
					$color_array[0]=$data_colorx;
					$color_array[1]=$data_colory;
					if(!empty($style_array)){
						$json_array['style']=$style_array;
					}if(!empty($data_colorx) || !empty($data_colory)){
						$json_array['color'][0]=$data_colorx;
						$json_array['color'][1]=$data_colory;
					}
					if(!empty($json_array)){
						$json_array=json_encode($json_array);
						// print_r($json_array);
						// print_r($_SESSION['employeeID']);
						$sql = '';
						$sql = "INSERT INTO \"tbl_log_updates\" (";
						$sql .= " \"styleId\", \"createdBy\", \"createdDate\", \"updatedDate\", \"previous\", \"present\" ";
						$sql .= " ) VALUES (";
						$sql .= " '" . $styleId . "' ";
						$sql .= ", '". $_SESSION['employeeID'] ."'";
						$sql .= ", '". date('U') ."'";
						$sql .= ", '". date('U') ."'";
						$sql .= ", '".$json_array."'";
						$sql .= ", 'style'";
						$sql .= ")";
						//echo $sql;
						if(!($audit = pg_query($connection,$sql))){
						$return_arr['error'] = pg_last_error($connection);
						}		
					}			
}
return;
?>