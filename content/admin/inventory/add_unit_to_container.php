<?php
require('Application.php');
$return_arr[0]['name'] = "";
$return_arr[0]['error'] = "";
$return_arr[0]['flag'] = 0;
extract($_POST);

$location_details_id = $containerId;



// var_dump($_POST);
// exit();
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
pg_free_result($data_opt1Size);

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


$fetch_loc;
$query='select * from "tbl_invLocation" where "locationId"=\''.$locationId.'\'';
// echo json_encode($query);
// exit();

if(!($result=pg_query($connection,$query))){
    $return_arr[0]['error'] = pg_last_error($connection);
    echo json_encode($return_arr);
    return;
}
while($row = pg_fetch_array($result)){
    $fetch_loc=$row;}
pg_free_result($result);


$fetch_wh;
$query='select container from "locationDetails" where "locationId"=\''.$locationId.'\' and container != \'null\'';
if(!($result=pg_query($connection,$query))){
    $return_arr[0]['error'] = pg_last_error($connection);
    echo json_encode($return_arr);
    return;
}
while($row = pg_fetch_array($result)){
    $fetch_wh=$row;}
pg_free_result($result);



$new_unit = $fetch_loc['identifier'].'_'.$fetch_wh['container'].'_'.$unit;



$sql = 'select count(*) from "tbl_invStorage" where unit=\''.$new_unit.'\'';
if(!($result=pg_query($connection,$sql))){
    $err = pg_last_error($connection);
    echo json_encode($err);
    exit();
}
$row = pg_fetch_array($result);
pg_free_result($result);

if($row['count']>0)
{
    echo json_encode("box not available");
    exit();
}





$locArr = array();
if($data_style['locationIds'] != "")
{
    $locArr = explode(",",$data_style['locationIds']);
}
$notes = 'auto inventory';
$query = '';
$query = "INSERT INTO \"tbl_inventory\" (";
$query .=" \"styleId\" ";
$query .=" ,\"styleNumber\" ";
$query .=" ,\"scaleId\" ";
$query .=", \"locationId\" ";
$query .=", \"location_details_id\" ";
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
$query .=" ,'".$locationId."' ";
$query .=" , '".$location_details_id."' ";
$query .=" ,0 ";
$query .=" ,0 ";
if($k < count($data_mainSize))$query .=", '".$data_mainSize[0]['mainSizeId']."' ";
$query .=", '$colorId' ";
if($j < count($data_opt1Size))$query .=", '".$data_opt1Size[0]['opt1SizeId']."' ";
if($k < count($data_opt2Size))$query .=", '".$data_opt2Size[0]['opt2SizeId']."' ";
$query .=" ,'$notes' ";
if($k < count($data_mainSize))$query .=", '".$data_mainSize[0]['scaleSize']."' ";
if($j < count($data_opt1Size))$query .=", '".$data_opt1Size[0]['opt1Size']."' ";
if($k < count($data_opt2Size))$query .=", '".$data_opt2Size[0]['opt2Size']."' ";
$query .=" ,1 ";
$query .=" ,'".$_SESSION['employeeID']."' ";
$query .=" ,'".$_SESSION['employeeID']."' ";
$query .=" ,'".date('U')."' ";
$query .=" ,'".date('U')."' ";
$query .=" )  returning \"inventoryId\" ";


if(!($result=pg_query($connection,$query))){
    $return_arr[0]['error'] = pg_last_error($connection);
    echo json_encode($return_arr);
    return;
}
$id = array();
while($row = pg_fetch_array($result)){
    $id[]=$row;
}





pg_free_result($result);

$query = '';
$query = "INSERT INTO \"tbl_invStorage\" (";
$query .= " \"invId\" ";
$query .= " ,\"styleId\" ";
$query .= " ,\"colorId\" ";
$query .= " ,\"locationId\" ";
if ($unit != "") $query .= " ,\"unit\" "; // for the unit field
if ($type != "") $query .= " ,\"type\" ";
$query .= " ,\"wareHouseQty\" ";
$query .= " ,\"createdBy\" ";
$query .= " ,\"updatedBy\" ";
$query .= " ,\"createdDate\" ";
$query .= " ,\"updatedDate\" ";
$query .= ")";
$query .= " VALUES (";
$query .= " '" . $id[0]['inventoryId'] . "' ";
$query .= " ,'" . $styleId . "' ";
$query .= " ,'" . $colorId . "' ";
$query .= " ,'" . $locationId . "' ";
if ($unit != "") $query .= " ,'" . $new_unit . "' "; // for the unit field
if ($type != "") $query .= " ,'" . $type . "' ";
$query .= " ,0 ";
$query .= " ,'" . $_SESSION['employeeID'] . "' ";
$query .= " ,'" . $_SESSION['employeeID'] . "' ";
$query .= " ,'" . date('U') . "' ";
$query .= " ,'" . date('U') . "' ";
$query .= " )";

if (!($resultProduct = pg_query($connection, $query))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}


$sql = '';
$sql = "INSERT INTO \"audit_logs\" (";
$sql .= " \"inventory_id\", \"employee_id\", \"updated_time\",";
$sql .= " \"log\") VALUES (";
$sql .= " '" . $id[0]['inventoryId'] . "' ";
$sql .= ", '". $_SESSION['employeeID'] ."'";
$sql .= ", '". date('U') ."'";
$sql .= ", 'created new box:  ".$new_unit."'";
$sql .= ")";
if(!($audit = pg_query($connection,$sql))){
    $return_arr['error'] = pg_last_error($connection);
}

echo json_encode($new_unit);
exit;
?>