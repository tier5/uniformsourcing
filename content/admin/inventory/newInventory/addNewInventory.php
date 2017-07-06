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
    $sql .= " \"storageId\",box ) VALUES ( ";
    $sql .= "'".$styleId."','".$colorId."','".$type."',";
    if($type == 'warehouse'){
        $sql .= " '".$row."','".$rack."','".$shelf."',";
    }
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
        }
    }
    echo json_encode([
        'message' => 'Box Successfully Added',
        'success' => true,
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