<?php require('Application.php');
	//require('../../jsonwrapper/jsonwrapper.php');
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
					$query = "SELECT * from \"tbl_invColor\" where \"styleId\"='$styleId'";
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
return;
?>