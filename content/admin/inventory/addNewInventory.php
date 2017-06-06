<?php
require('Application.php');
$return_arr[0]['name'] = "";
$return_arr[0]['error'] = "";
$return_arr[0]['flag'] = 0;
extract($_POST);

// echo json_encode($_POST);
// exit();

$location_details_id = $warehouse;

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
    $data_inv[]=$row;
}
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
$query='select * from "tbl_invLocation" where "locationId"='.$location;
if(!($result=pg_query($connection,$query))){
    $return_arr[0]['error'] = pg_last_error($connection);
    echo json_encode($return_arr);
    return;
}
while($row = pg_fetch_array($result)){
    $fetch_loc=$row;}
pg_free_result($result);







// $fetch_wh;
// $query='select * from "warehouse" where "locationId"='.$location;
// if(!($result=pg_query($connection,$query))){
//     $return_arr[0]['error'] = pg_last_error($connection);
//     echo json_encode($return_arr);
//     return;
// }
// while($row = pg_fetch_array($result)){
//     $fetch_wh=$row;}
// pg_free_result($result);


$fetch_wh;
$query='select warehouse from "locationDetails" where "locationId"=\''.$location.'\' and warehouse != \'null\'';
if(!($result=pg_query($connection,$query))){
    $return_arr[0]['error'] = pg_last_error($connection);
    echo json_encode($return_arr);
    return;
}
while($row = pg_fetch_array($result)){
    $fetch_wh=$row;}
pg_free_result($result);



$new_unit = $fetch_loc['identifier'].'_'.$fetch_wh['warehouse'].'_'.$unit;
// echo json_encode($new_unit);
// exit();


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
    echo "box not available";
    exit();
}



//update tbl_invStyle

if($data_style['locationIds'] != "")
{
    $all_locations = explode(",",$data_style['locationIds']);
}


$flag =0;
foreach ($all_locations as $key => $value) 
{
    if($value == $location)
    {
        $flag =1;
    }
}

if($flag == 0)
{
    $new_location_ids = $data_style['locationIds'].','.$location;
    // echo json_encode($new_location_ids);
    // exit();
    $x = $data_style['styleId'];
    $y="'".$new_location_ids."'";
    
    $sql = 'UPDATE "tbl_invStyle" SET "locationIds"= '.$y.' where "styleId" ='.$x;
    // echo json_encode($sql);
    // exit();
    if(!($result=pg_query($connection,$sql)))
    {
        $return_arr[0]['error'] = pg_last_error($connection);
        echo json_encode($return_arr);
        exit();
    }

}

//done








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
$query .=" ,'".$location."' ";
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

// var_dump($query);
// exit();
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
if ($room != "") $query .= " ,\"room\" ";
if ($_POST['row'] != "") $query .= " ,\"row\" ";
if ($rack != "") $query .= " ,\"rack\" ";
if ($shelf != "") $query .= " ,\"shelf\" ";
if ($unit != "") $query .= " ,\"unit\" ";
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
$query .= " ,'" . $location . "' ";
if ($room != "") $query .= " ,'" . $room . "' ";
if ($_POST['row'] != "") $query .= " ,'" . $_POST['row'] . "' ";
if ($rack != "") $query .= " ,'" . $rack . "' ";
if ($shelf != "") $query .= " ,'" . $shelf . "' ";
if ($unit != "") $query .= " ,'" . $new_unit . "' ";
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
$sql .= ", 'created new box:  ".$new_slot."'";
$sql .= ")";
if(!($audit = pg_query($connection,$sql))){
    $return_arr['error'] = pg_last_error($connection);
}


echo $new_unit;
exit;
?>