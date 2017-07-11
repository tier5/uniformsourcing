<?php
require('Application.php');
function MainSize($arrMain,$keyMain)
{
    foreach ($arrMain as $valueMain) {
        if($valueMain['scaleSize'] == $keyMain){
            return $valueMain['mainSizeId'];
        }
    }
    return 0;
}
function Opt1Size($arrOpt,$keyOpt)
{
    foreach ($arrOpt as $valueOpt1) {
        if(strtolower($valueOpt1['opt1Size']) == strtolower($keyOpt)){
            return $valueOpt1['opt1SizeId'];
        }
    }
    return 0;
}
$return_arr['name'] = "";
$return_arr['error'] = "";
$return_arr['flag'] = 0;
$return_arr['conflict'] = "";
function locationDetails($locId,$connection){
    $sql = '';
    $sql = 'SELECT name from "tbl_invLocation" WHERE "locationId"='.$locId;
    if(!($result=pg_query($connection,$sql))){
        $return_arr['error'] = pg_last_error($connection);
        echo json_encode($return_arr);
        return;
    }
    $row = pg_fetch_array($result);
    $location=$row['name'];
    return $location;
}
extract($_POST);
if($type == 'conveyor'){
    $boxType = $slot;
} else {
    $boxType = $box;
}
$sql = '';
$sql = "SELECT * FROM \"tbl_invStorage\" WHERE SPLIT_PART(unit,'_',3) = '". $boxType."' or unit='".$boxType."'";
if(!($result=pg_query($connection,$sql))){
    $return_arr['error'] = pg_last_error($connection);
    echo json_encode($return_arr);
    return;
}
$id = array();
while($row = pg_fetch_array($result)){
    $id[]=$row;
}
if(count($id) > 0){
    foreach ($id as $key=>$value){
        $loc[$key]['location'] = locationDetails($value['locationId'],$connection);
        $typeLoc = explode('_',$value['unit']);
        $loc[$key]['type'] =$typeLoc[1];
    }
    $locOrg = array_unique($loc);
    $return_arr['name'] = $locOrg;
    $return_arr['error'] = 1;
    $return_arr['conflict'] = 1;
    echo json_encode($return_arr);
    return;
} else {
    $sql = '';
    $sql ="select * from \"tbl_invStyle\" where \"styleId\"='".$styleId."'";
    if(!($result=pg_query($connection,$sql))){
        $return_arr['error'] = pg_last_error($connection);
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
        $data_opt2Size[]=$row2;
    }
    pg_free_result($result2);
    $query='select "sizeScaleId", price, "locationId","opt1ScaleId", "opt2ScaleId", quantity from "tbl_inventory" where "styleId"='.$data_style['styleId'].' and "colorId"='.$colorId.'  and "isActive"=1 order by "inventoryId"';
    if(!($result=pg_query($connection,$query))){
        $return_arr['error'] = pg_last_error($connection);
        echo json_encode($return_arr);
        return;
    }
    while($row = pg_fetch_array($result)){
        $data_inv[]=$row;
    }
    $query='select * from "tbl_invLocation" order by "locationId"';
    if(!($result=pg_query($connection,$query))){
        $return_arr['error'] = pg_last_error($connection);
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
    $typeLoc = explode('_',$location);
    $sql = '';
    $sql = "SELECT \"locationId\" FROM \"tbl_invLocation\" WHERE \"identifier\" ='".$typeLoc[0]."'";
    if(!($result=pg_query($connection,$sql))){
        $return_arr['error'] = pg_last_error($connection);
        echo json_encode($return_arr);
        return;
    }
    $rowLoc = pg_fetch_row($result);
    $flag = 0;
    foreach ($new_qty_data as $key => $value){
        if($value < 0){
            $return_arr['name'] = "Negative value not accepted";
            $return_arr['error'] = 1;
            echo json_encode($return_arr);
            return;
        }
    }
    foreach ($is_change_new as $key => $value){
        if($value == 1){
            $mainSize = MainSize($data_mainSize,$new_type_data[$key]);
            $opt1 = Opt1Size($data_opt1Size,$new_size_data[$key]);
            $notes = 'auto inventory';
            $query = "";
            $query = "INSERT INTO \"tbl_inventory\" (";
            $query .=" \"styleId\" ";
            $query .=" ,\"styleNumber\" ";
            $query .=" ,\"scaleId\" ";
            //$query .=", \"price\" ";
            $query .=", \"locationId\" ";
            $query .=", \"newQty\" ";
            $query .=", \"quantity\" ";
            if($new_type_key_data[$key])$query .=", \"sizeScaleId\" ";
            $query .=", \"colorId\" ";
            if($new_size_key_data[$key]) $query .=", \"opt1ScaleId\" ";
            //if($k < count($data_opt2Size))$query .=", \"opt2ScaleId\" ";
            $query .=", \"notes\" ";
            $query .=", \"mainSize\" ";
            if($new_size_data[$key])  $query .=", \"rowSize\" ";
            //if($k < count($data_opt2Size))$query .=", \"columnSize\" ";
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
            //$query .=" ,'".."' ";
            $query .=" ,'".$rowLoc[0]."' ";
            $query .=" ,0 ";
            $query .=" ,'".$new_qty_data[$key]."' ";
            if($new_type_key_data[$key]) $query .=", ".$new_type_key_data[$key]." ";
            $query .=", '$colorId' ";
            if($new_size_key_data[$key]) $query .=", ".$new_size_key_data[$key]." ";
            //if($k < count($data_opt2Size))$query .=", '".$data_opt2Size[$k]['opt2SizeId']."' ";
            $query .=" ,'$notes' ";
            $query .=", '".$new_type_data[$key]."' ";
            if($new_size_data[$key]) $query .=", '".$new_size_data[$key]."' ";
            //if($k < count($data_opt2Size))$query .=", '".$data_opt2Size[$k]['opt2Size']."' ";
            $query .=" ,1 ";
            $query .=" ,'".$_SESSION['employeeID']."' ";
            $query .=" ,'".$_SESSION['employeeID']."' ";
            $query .=" ,'".date('U')."' ";
            $query .=" ,'".date('U')."' ";
            $query .=" ) returning \"inventoryId\" ";
            if(!($result=pg_query($connection,$query))){
                $return_arr['error'] = pg_last_error($connection);
                echo json_encode($return_arr);
                return;
            }
            $invId = pg_fetch_row($result);
            pg_free_result($result);

            if($type == 'warehouse' || $type == 'container'){
                $unitNumber = $location.'_'.$box;
            } else{
                $unitNumber = $location.'_'.$slot;
            }

            $query = '';
            $query = "INSERT INTO \"tbl_invStorage\" (";
            $query .= " \"invId\" ";
            $query .= " ,\"styleId\" ";
            $query .= " ,\"colorId\" ";
            $query .= " ,\"locationId\" ";
            if($new_type_key_data[$key])$query .=", \"sizeScaleId\" ";
            if($new_size_key_data[$key]) $query .=", \"opt1ScaleId\" ";
            if ($room != "") $query .= " ,\"room\" ";
            if ($_POST['row'] != "") $query .= " ,\"row\" ";
            if ($rack != "") $query .= " ,\"rack\" ";
            if ($shelf != "") $query .= " ,\"shelf\" ";
            if ($unitNumber != "") $query .= " ,\"unit\" ";
            if ($type != "") $query .= " ,\"type\" ";
            $query .= " ,\"wareHouseQty\" ";
            $query .= " ,\"createdBy\" ";
            $query .= " ,\"updatedBy\" ";
            $query .= " ,\"createdDate\" ";
            $query .= " ,\"updatedDate\" ";
            $query .= ")";
            $query .= " VALUES (";
            $query .= " '" . $invId[0] . "' ";
            $query .= " ,'" . $styleId . "' ";
            $query .= " ,'" . $colorId . "' ";
            $query .= " ,'" . $rowLoc[0] . "' ";
            if($new_type_key_data[$key]) $query .=", ".$new_type_key_data[$key]." ";
            if($new_size_key_data[$key]) $query .=", ".$new_size_key_data[$key]." ";
            if ($room != "") $query .= " ,'" . $room . "' ";
            if ($_POST['row'] != "") $query .= " ,'" . $_POST['row'] . "' ";
            if ($rack != "") $query .= " ,'" . $rack . "' ";
            if ($shelf != "") $query .= " ,'" . $shelf . "' ";
            if ($unitNumber != "") $query .= " ,'" . $unitNumber . "' ";
            if ($type != "") $query .= " ,'" . $type . "' ";
            $query .= " ,".$new_qty_data[$key]." ";
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
            $sql .= " '" . $invId[0] . "' ";
            $sql .= ", '". $_SESSION['employeeID'] ."'";
            $sql .= ", '". date('U') ."'";
            $sql .= ", 'created new box:  ".$unitNumber."'";
            $sql .= ")";
            if(!($audit = pg_query($connection,$sql))){
                $return_arr['error'] = pg_last_error($connection);
            }
        }
    }
    $return_arr['name'] = 'added';
    $return_arr['flag'] = 1;
    echo json_encode($return_arr);
    return;
}
?>