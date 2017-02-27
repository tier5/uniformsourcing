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
extract($_POST);
$return_arr['name'] = "";
$return_arr['error'] = "";
function cleanupArray($arr, $non_zero_start = 0)
{
	$new_arr = array();
	foreach ($arr as $key => $value) 
	{
		if (!empty($value))
		{
			if (is_array($value))
			{
				$value = cleanupArray($value, $non_zero_start);	
				if (count($value) == 0)
				{
					continue;
				}
			}
			else 
			{
				$value = trim(strip_tags($value));
				if (empty($value))
				{
					continue;
				}
			}
			$new_arr[$key] = $value;
		}
	}
	// Reordering elements
	if (!empty($new_arr)) 
	{
		if (!empty($non_zero_start))
		{
			$new_arr = array_merge_recursive(array("") + $new_arr);
	
	// We don't need an empty first element.
	// This was used to shift other elements to start from 1 instead of 0
			unset($new_arr[0]);
		} 
		else
		{
			$new_arr = array_merge_recursive($new_arr);
		}
	}
	return $new_arr;
}

if(isset($_POST['type']))
{
	if($scaleName == "")
	{		
		$return_arr['name'] = "Please Enter Scale Name before submiting.";
	}
	else
	{
		$scaleName=pg_escape_string($scaleName);		
		$opt1Name=pg_escape_string($opt1Name);
		$opt2Name=pg_escape_string($opt2Name);
		$query = "";
		switch ($type)			
		{
			case "a":
			case "A":
			{
				$query1 = "select count(1) from \"tbl_invScaleName\" where \"scaleName\" = '".$scaleName."' and \"isActive\"=1";
				if(!($result1=pg_query($connection,$query1))){
					$return_arr['error'] = "Error while processing project information!";
					echo json_encode($return_arr);			
					return;
				}
				$row = pg_fetch_row($result1);
				pg_free_result($result1);			
				if($row[0] != 0)
				{
					$return_arr['error'] = "Scale Name you entered already exist in Database.";
					echo json_encode($return_arr);			
					return;
				}
				$query_Name="INSERT INTO \"tbl_invScaleName\" (\"scaleName\" ";
				if($opt1Name != "") $query_Name.=" , \"opt1Name\" ";
				if($opt2Name != "") $query_Name.=" , \"opt2Name\" ";
				$query_Name.=")";
				$query_Name.=" VALUES ('$scaleName'";				
				if($opt1Name != "") $query_Name.=", '$opt1Name' ";
				if($opt2Name != "") $query_Name.=", '$opt2Name' ";				
				$query_Name.=")";
				if(!($result=pg_query($connection,$query_Name))){
					$return_arr['error'] = pg_last_error($connection);
					echo json_encode($return_arr);
					return;
				}
				pg_free_result($result);
				$numRecords = 0;
				$sql='select "scaleId" from "tbl_invScaleName" where "scaleName" =\''.$scaleName.'\'';
				if(!($result_cnt=pg_query($connection,$sql))){
					$return_arr['error'] = pg_last_error($connection);
					echo json_encode($return_arr);
					return;
				}
				while($row_cnt = pg_fetch_array($result_cnt)){
					$data_cnt=$row_cnt;
				}
				pg_free_result($result_cnt);
				if(! $data_cnt['scaleId']) { $data_cnt['scaleId']=1; }
				$scaleId = $data_cnt['scaleId'];
				
				for(;$numRecords < count($scaleSize); $numRecords++)
				{
					$query_Size = "INSERT INTO \"tbl_invScaleSize\" (\"scaleId\" ";
					if($scaleSize[$numRecords] != "")
					{
						$query_Size.=" , \"scaleSize\" ";
						if($mainOrder[$numRecords]!="")
						$query_Size .=",\"mainOrder\" ";	
					}
					if((count($opt1Size) > $numRecords) && ($opt1Size[$numRecords] != ""))
					{
						$query_Size.=" , \"opt1Size\" ";
						if($opt1Order[$numRecords]!="")
						$query_Size.=" , \"opt1Order\" ";
					}
					if((count($opt2Size) > $numRecords) && ($opt2Size[$numRecords] != ""))
					{
						$query_Size.=" , \"opt2Size\" ";
						if($opt2Order[$numRecords]!="")
						$query_Size.=" , \"opt2Order\" ";
					}
					$query_Size.=")";
					$query_Size.=" VALUES ('$scaleId'";
					if($scaleSize[$numRecords] != "")
					{
						$query_Size.= ", '$scaleSize[$numRecords]' ";
						if($mainOrder[$numRecords]!="")
						$query_Size .=",'$mainOrder[$numRecords]' ";	
					}
					if((count($opt1Size) > $numRecords) && ($opt1Size[$numRecords] != ""))
					{
						$query_Size.= ", '$opt1Size[$numRecords]' ";
						if($opt1Order[$numRecords]!="")
						$query_Size.=" , '$opt1Order[$numRecords]' ";
					}
					if((count($opt2Size) > $numRecords) && ($opt2Size[$numRecords] != ""))
					{
						$query_Size.= ", '$opt2Size[$numRecords]' ";
						if($opt2Order[$numRecords]!="")
						$query_Size.=" , '$opt2Order[$numRecords]' ";
					}
					$query_Size.=")";
					if(!($result=pg_query($connection,$query_Size))){
						$return_arr['error'] = pg_last_error($connection);
						echo json_encode($return_arr);
						return;
					}
					pg_free_result($result);
				}
				for(;$numRecords < count($opt1Size); $numRecords++)
				{
					$query_Size = "INSERT INTO \"tbl_invScaleSize\" (\"scaleId\" ";
					if($opt1Size[$numRecords] != "")
					{
						$query_Size.=" , \"opt1Size\" ";
						if($opt1Order[$numRecords]!="")
						$query_Size.=" , \"opt1Order\" ";
					}
					if((count($opt2Size) > $numRecords) && ($opt2Size[$numRecords] != ""))
					{
						$query_Size.=" , \"opt2Size\" ";
						if($opt2Order[$numRecords]!="")
						$query_Size.=" , \"opt2Order\" ";
					}
					$query_Size.=")";
					$query_Size.=" VALUES ('$scaleId'";
					if($opt1Size[$numRecords] != "")
					{
						$query_Size.= ", '$opt1Size[$numRecords]' ";
						if($opt1Order[$numRecords]!="")
						$query_Size.=" , '$opt1Order[$numRecords]' ";
					}
					if((count($opt2Size) > $numRecords) && ($opt2Size[$numRecords] != ""))
					{
						$query_Size.= ", '$opt2Size[$numRecords]' ";
						if($opt2Order[$numRecords]!="")
						$query_Size.=" , '$opt2Order[$numRecords]' ";
					}
					$query_Size.=")";
					if(!($result=pg_query($connection,$query_Size))){
						$return_arr['error'] = pg_last_error($connection);
						echo json_encode($return_arr);
						return;
					}
					pg_free_result($result);
				}
				for(;$numRecords < count($opt2Size); $numRecords++)
				{
					$query_Size = "INSERT INTO \"tbl_invScaleSize\" (\"scaleId\" ";		
					if($opt2Size[$numRecords] != "")
					{
						$query_Size.=" , \"opt2Size\" ";
						if($opt2Order[$numRecords]!="")
						$query_Size.=" , \"opt2Order\" ";
					}
					$query_Size.=")";
					$query_Size.=" VALUES ('$scaleId'";						
					if($opt2Size[$numRecords] != "")
					{
						$query_Size.= ", '$opt2Size[$numRecords]' ";
						if($opt2Order[$numRecords]!="")
						$query_Size.=" , '$opt2Order[$numRecords]' ";
					}
					$query_Size.=")";
					if(!($result=pg_query($connection,$query_Size))){
						$return_arr['error'] = pg_last_error($connection);
						echo json_encode($return_arr);
						return;
					}
					pg_free_result($result);
				}																			
				break;
			}
			case "e":
			case "E":
			{
				if($scaleId > 0)
				{	
					$query_Name  = "UPDATE \"tbl_invScaleName\" SET ";
					$query_Name .= " \"scaleName\" = '$scaleName'";
					$query_Name .= ", \"opt1Name\" = '$opt1Name'";
					$query_Name .= ", \"opt2Name\" = '$opt2Name'";
					$query_Name .= "where \"scaleId\"='$scaleId'";
					if(!($result=pg_query($connection,$query_Name))){
						$return_arr['error'] = pg_last_error($connection);
						echo json_encode($return_arr);
						return;
					}
					pg_free_result($result);
					$query_Size = "select * from \"tbl_invScaleSize\" where \"scaleId\"='$scaleId'  order by \"sizeScaleId\", \"scaleId\"";			
					if(!($result=pg_query($connection,$query_Size))){
						$return_arr['error'] = pg_last_error($connection);
						echo json_encode($return_arr);
						return;
					}
					while($row = pg_fetch_array($result)){
						$data[]=$row;
					}
					
					$query_Size = "";
					$upd = "";
					$index = 0;
					pg_free_result($result);
					
					if(count($scaleSizeId) >= count($opt1SizeId) && count($scaleSizeId) >= count($opt2SizeId))
					{						
						$sizeId = $scaleSizeId;
					}
					else if(count($opt1SizeId) > count($scaleSizeId) && count($opt1SizeId) >= count($opt2SizeId))
					{
							$sizeId = $opt1SizeId;
					}
					else
					{						
							$sizeId = $opt2SizeId;
					}	
					$del = 1;
					$sql = "";
					$count = 0;
					$mainSizeCount = 0;
					$rowSizeCount = 0;
					$columnSizeCount = 0;
					for(;$index < count($sizeId);$index++, $mainSizeCount++, $rowSizeCount++, $columnSizeCount++)
					{						
						if($index < count($data))
						{
							if($index > count($scaleSizeId) || strlen($scaleSize[$index]) == 0)
							{								
								if($index > count($opt1SizeId) || strlen($opt1Size[$index]) == 0)
								{
									if($index > count($opt2SizeId) || strlen($opt2Size[$index]) == 0)
									{
										$sql .= "DELETE from \"tbl_invScaleSize\" where \"sizeScaleId\"='".$data[$index]['sizeScaleId']."';";
										if(!($result=pg_query($connection,$sql)))
										{
											$return_arr['error'] = pg_last_error($connection);
											echo json_encode($return_arr);
											return;
										}
										pg_free_result($result);
										$count++;
										$sql = "";
										continue;
									}
								}
							}
							$query_Size  = "UPDATE \"tbl_invScaleSize\" SET \"scaleId\"='$scaleId' ";
							$mainUpdate = "";
							$opt1Update = "";
							$opt2Update = "";
							$sql = "";
							if($mainSizeCount < count($scaleSize))
							{
								for(;$mainSizeCount < count($scaleSize); $mainSizeCount++)
								{
									if(strlen($scaleSize[$mainSizeCount]) > 0)
									{
										break;
									}
									else if($scaleSizeId[$mainSizeCount] > 0)
									{
										$sql .= 'update tbl_inventory SET "isActive"=0 where "sizeScaleId" ='.$scaleSizeId[$mainSizeCount].';';
									}
								}
								if($mainSizeCount < count($scaleSize))
								{
									if($sizeId[$index] >0)
									{
										if($scaleSizeId[$mainSizeCount] > 0)
										{
											$sql .= 'Update "tbl_invStorage" set "sizeScaleId" = '.$sizeId[$index].' where "sizeScaleId" = '.$scaleSizeId[$mainSizeCount].' ;';
											$sql .= 'Update tbl_inventory set "mainSize" = \''.$scaleSize[$mainSizeCount].'\' ,"sizeScaleId" = '.$sizeId[$index].' where "sizeScaleId" = '.$scaleSizeId[$mainSizeCount].' and "isActive"=1  ;';
										}
									}
									$mainUpdate .= ", \"scaleSize\" = '$scaleSize[$mainSizeCount]'";
									if(strlen($mainOrder[$mainSizeCount]) > 0)
										$mainUpdate .= ", \"mainOrder\" = '$mainOrder[$mainSizeCount]'";										
									else
										$mainUpdate .= ", \"mainOrder\" = null";
								}
								else
								{
									//$sql .= 'Update tbl_inventory set "mainSize" = null ,"sizeScaleId" = '.$scaleSizeId[$index].' where "sizeScaleId" = '.$data[$index]['sizeScaleId'].' ;';
									$mainUpdate .= ", \"scaleSize\" = null";
									$mainUpdate .= ", \"mainOrder\" = null";
								}
							}
							else
							{
								$mainUpdate .= ", \"scaleSize\" = null";
								$mainUpdate .= ", \"mainOrder\" = null";
							}
							if($rowSizeCount < count($opt1Size))
							{
								for(;$rowSizeCount < count($opt1Size); $rowSizeCount++)
								{
									if(strlen($opt1Size[$rowSizeCount]) > 0)
									{
										break;
									}
									else if($opt1SizeId[$rowSizeCount] > 0)
									{
										$sql .= 'update tbl_inventory SET "isActive"=0 where "opt1ScaleId" ='.$opt1SizeId[$rowSizeCount].' ;';
									}
								}
								if($rowSizeCount < count($opt1Size))
								{
									if($sizeId[$index] >0)
									{
										if($opt1SizeId[$rowSizeCount] > 0)
										{
											$sql .= 'Update "tbl_invStorage" set "opt1ScaleId" = '.$sizeId[$index].' where "opt1ScaleId" = '.$opt1SizeId[$rowSizeCount].' ;';
											$sql .= 'Update tbl_inventory set "rowSize" = \''.$opt1Size[$rowSizeCount].'\' ,"opt1ScaleId" = '.$sizeId[$index].' where "opt1ScaleId" = '.$opt1SizeId[$rowSizeCount].' and "isActive"=1 ;';
										}
									}
									$opt1Update .= ", \"opt1Size\" = '$opt1Size[$rowSizeCount]'";
									if(strlen($opt1Order[$rowSizeCount])>0)
										$opt1Update .= ", \"opt1Order\" = '$opt1Order[$rowSizeCount]'";
									else
										$opt1Update .= ", \"opt1Order\" = null";
								}
								else
								{
									//$sql .= 'Update tbl_inventory set "rowSize" = null,"opt1ScaleId" = '.$opt1SizeId[$index].' where "opt1ScaleId" = '.$data[$index]['sizeScaleId'].' ;';
									$opt1Update .= ", \"opt1Size\" = null";
									$opt1Update .= ", \"opt1Order\" = null";
								}
								
							}
							else 
							{
								$opt1Update .= ", \"opt1Size\" = null";
								$opt1Update .= ", \"opt1Order\" = null";
							}
							if($columnSizeCount < count($opt2Size))
							{
								for(;$columnSizeCount < count($opt2Size); $columnSizeCount++)
								{
									if(strlen($opt2Size[$columnSizeCount]) > 0)
										break;
								}
								if($columnSizeCount < count($opt2Size))
								{								
									if($sizeId[$index] >0)
									{
										if($opt2SizeId[$columnSizeCount] > 0)
										{
											$sql .= 'Update "tbl_invStorage" set "opt2ScaleId" = '.$sizeId[$index].' where "opt2ScaleId" = '.$opt2SizeId[$columnSizeCount].' ;';	
											$sql .= 'Update tbl_inventory set "columnSize" = \''.$opt2Size[$index].'\', "opt2ScaleId" = '.$sizeId[$index].' where "opt2ScaleId" = '.$opt2SizeId[$columnSizeCount].' and "isActive"=1  ;';
										}
									}	
									$opt2Update .= ", \"opt2Size\" = '$opt2Size[$columnSizeCount]'";
									if(strlen($opt2Order[$columnSizeCount]) > 0)
										$opt2Update .= ", \"opt2Order\" = '$opt2Order[$columnSizeCount]'";
									else
										$opt2Update .= ", \"opt2Order\" = null";
								}
								else 
								{
									$opt2Update .= ", \"opt2Size\" = null";
									$opt2Update .= ", \"opt2Order\" = null";
								}
								
							}
							else 
							{
								$opt2Update .= ", \"opt2Size\" = null";
								$opt2Update .= ", \"opt2Order\" = null";
							}
							$query_Size .= "$mainUpdate $opt1Update $opt2Update where \"sizeScaleId\"='".$data[$index]['sizeScaleId']."' ;";
							$mainUpdate ="";
							$opt1Update ="";
							$opt2Update ="";
							
							if($index < count($opt2Size) && strlen($opt2Size[$index]) <= 0)
							{
								if($opt2SizeId[$index] > 0)
								{
									$sql .= 'Update tbl_inventory set "columnSize" = null, "opt2ScaleId" = null where "opt2ScaleId" = '.$opt2SizeId[$index].' ;';
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
								$sql = "";
							}
							
						}				
						else
						{
							$query_Size = "INSERT INTO \"tbl_invScaleSize\" (\"scaleId\" ";
							if((count($scaleSize) > $index) && $scaleSize[$index] != "")
							{
								$query_Size.=" , \"scaleSize\" ";
								if($mainOrder[$index] != "")$query_Size.=" , \"mainOrder\" ";
							}
							if((count($opt1Size) > $index) && ($opt1Size[$index] != ""))
							{
								$query_Size.=" , \"opt1Size\" ";
								if($opt1Order[$index] != "")$query_Size.=" , \"opt1Order\" ";
							}
							if((count($opt2Size) > $index) && ($opt2Size[$index] != ""))
							{
								$query_Size.=" , \"opt2Size\" ";
								if($opt2Order[$index] != "")$query_Size.=" , \"opt2Order\" ";
							}
							$query_Size.=")";
							$query_Size.=" VALUES ('$scaleId'";
							if((count($scaleSize) > $index) && $scaleSize[$index] != "")
							{
								$query_Size.= ", '$scaleSize[$index]' ";
								if($mainOrder[$index] != "")$query_Size.=" , '$mainOrder[$index]' ";
							}
							if((count($opt1Size) > $index) && ($opt1Size[$index] != ""))
							{
								$query_Size.= ", '$opt1Size[$index]' ";
								if($opt1Order[$index] != "")$query_Size.=" , '$opt1Order[$index]' ";
							}
							if((count($opt2Size) > $index) && ($opt2Size[$index] != ""))
							{
								$query_Size.= ", '$opt2Size[$index]' ";
								if($opt2Order[$index] != "")$query_Size.=" , '$opt2Order[$index]' ";
							}
							$query_Size.=")";
						}
						if($query_Size!="")
						{
							//echo $query_Size."<br/>";
							if(!($result=pg_query($connection,$query_Size)))
							{
								$return_arr['error'] = pg_last_error($connection);
								echo json_encode($return_arr);
								return;
							}
							pg_free_result($result);
							$query_Size="";
						}
						if($sql!="")
						{
							if(!($result=pg_query($connection,$sql)))
							{
								$return_arr['error'] = pg_last_error($connection);
								echo json_encode($return_arr);
								return;
							}
							pg_free_result($result);
							$sql="";
						}
						
					}
					$sql ="";
					$round = round($index/10);
					$del = $round >0 ? $round : 1;
					for(;$index < count($data); $index++)
					{
						$sql .= "DELETE from \"tbl_invScaleSize\" where \"sizeScaleId\"='".$data[$index]['sizeScaleId']."';";
						if($index > ($del*10))
						{
							if(!($result=pg_query($connection,$sql)))
							{
								$return_arr['error'] = pg_last_error($connection);
								echo json_encode($return_arr);
								return;
							}
							pg_free_result($result);
							$sql = "";
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
						$sql = "";
					}
				}
				break;
			}
		}
		$return_arr['id'] = $scaleId;
	}
}
	echo json_encode($return_arr);
	exit;
?>