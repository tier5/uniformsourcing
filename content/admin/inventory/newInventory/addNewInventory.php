<?php
require('Application.php');
$returnArray['name'] = '';
extract($_POST);
//Check Box is available or not
$sql = "";
$sql = "SELECT * FROM \"tbl_invUnit\" WHERE box='".$box."'";
if(!($result=pg_query($connection,$sql))){
    echo json_encode([
        'message' => pg_last_error($connection),
        'success' => false,
        'code' => 500
    ]);
    return;
}
$oldUnit= pg_fetch_array($result);
pg_free_result($result);
if(!$oldUnit){
    //Insert into unit table
    $sql = "";
    $sql = "INSERT INTO \"tbl_invUnit\" ( ".
    $sql .=" \"styleId\",\"colorId\",type ,";
    if($type == 'warehouse'){
        $sql .= " row, rack, shelf, ";
    }
    $sql .= " \"createdAt\",\"createdBy\",\"updatedAt\",\"updatedBy\",";
    $sql .= " \"storageId\",box ) VALUES ( ";
    $sql .= "'".$styleId."','".$colorId."','".$type."',";
    if($type == 'warehouse'){
        $sql .= " '".$row."','".$rack."','".$shelf."',";
    }
    $sql .= "'".date('Y-m-d G:i:s')."','".$_SESSION['employeeID']."','".date('Y-m-d G:i:s')."','".$_SESSION['employeeID']."', ";
    $sql .= "'".$locationId."','".$box."'";
    $sql .= ") RETURNING *";
    if(!($result=pg_query($connection,$sql))){
        echo json_encode([
            'message' => pg_last_error($connection),
            'success' => false,
            'code' => 500
        ]);
        return;
    }
    $unit = pg_fetch_array($result);
    pg_free_result($result);

    $sql = '';
    $sql = "INSERT INTO \"audit_logs\" (";
    $sql .= " \"inventory_id\", \"employee_id\", \"updated_time\",";
    $sql .= " \"log\") VALUES (";
    $sql .= " '" . $styleId . "' ";
    $sql .= ", '". $_SESSION['employeeID'] ."'";
    $sql .= ", '". date('U') ."'";
    $sql .= ", 'created new box:  ".$box."'";
    $sql .= ")";
    if(!($audit = pg_query($connection,$sql))){
        echo json_encode([
            'message' => pg_last_error($connection),
            'success' => false,
            'code' => 500
        ]);
        return;
    }
    pg_free_result($audit);

    foreach ($qty as $q){
        if($q < 0){
            echo json_encode([
                'message' => 'Negative values not allowed',
                'success' => false,
                'code' => 400
            ]);
            return;
        }
    }
    foreach ($is_change_new as $key => $change){
        if($change == '1'){
            $sql = "";
            $sql = "INSERT INTO \"tbl_invQuantity\" ( ".
            $sql .= " \"boxId\",\"mainSizeId\",\"optSizeId\",\"qty\" ) VALUES (";
            $sql .= "'".$unit['id']."','".$mainSizeId[$key]."','".$optSizeId[$key]."','".$qty[$key]."' )";
            if(!($result=pg_query($connection,$sql))){
                echo json_encode([
                    'message' => pg_last_error($connection),
                    'success' => false,
                    'code' => 500
                ]);
                return;
            }
            pg_free_result($result);
            $mainSize = getSizeName($mainSizeId[$key],"mainSize",$connection);
            $optSize = (getSizeName($optSizeId[$key],"opt1size",$connection) == NULL)?"qty":getSizeName($optSizeId[$key],"opt1size",$connection);
            $sql = '';
            $sql = "INSERT INTO \"audit_logs\" (";
            $sql .= " \"inventory_id\", \"employee_id\", \"updated_time\",";
            $sql .= " \"log\") VALUES (";
            $sql .= " '" . $styleId . "' ";
            $sql .= ", '". $_SESSION['employeeID'] ."'";
            $sql .= ", '". date('U') ."'";
            $sql .= ", 'Added ".$qty[$key]." for Scale ".$mainSize." - ".$optSize." in box: ".$box." '";
            $sql .= ")";
            if(!($audit = pg_query($connection,$sql))){
                echo json_encode([
                    'message' => pg_last_error($connection),
                    'success' => false,
                    'code' => 500
                ]);
                return;
            }
            pg_free_result($audit);
        }
    }
    echo json_encode([
        'message' => 'Box Successfully Added',
        'success' => true,
        'data' => $unit,
        'code' => 200
    ]);
    return;
} else {
    $sql = "";
    $sql = "SELECT details.*,location.* FROM \"locationDetails\" details".
        " INNER JOIN \"tbl_invLocation\" location on location.\"locationId\"= CAST(details.\"locationId\" as bigint) ".
        " WHERE details.id=".$locationId;
    if(!($result=pg_query($connection,$sql))){
        echo json_encode([
            'message' => pg_last_error($connection),
            'success' => false,
            'code' => 500
        ]);
        return;
    }
    $location= pg_fetch_array($result);
    pg_free_result($result);
    echo json_encode([
        'message' => 'box is already taken !! location : '.$location['name'],
        'success' => false,
        'code' => 400
    ]);
    return;
}
?>