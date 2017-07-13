<?php
require('Application.php');
$returnArray['name'] = '';
extract($_POST);
$sql = '';
$sql = 'SELECT * FROM "tbl_invUnit" WHERE id='.$boxId.' and "colorId"='.$colorId.' and "styleId"='.$styleId;
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
if($unit != ''){
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
    $count = 0;
    foreach ($is_change as $key=>$change){
        if($change == 1){
            if($count == 0){
                $sql = '';
                $sql = 'INSERT INTO "tbl_invUpdateLog" ('.
                    ' "boxId","styleId","createdBy","createdAt",type ) VALUES ('.
                    "'".$unit['box']."','".$styleId."','".$_SESSION['employeeID']."','".date('Y-m-d G:i:s')."','Update Box' ) RETURNING *";
                if(!($result=pg_query($connection,$sql))){
                    echo json_encode([
                        'message' => pg_last_error($connection),
                        'success' => false,
                        'code' => 500
                    ]);
                    return;
                }
                $log = pg_fetch_array($result);
                pg_free_result($result);
            }
            $mainSize = $mainSizeId[$key];
            $optSize = $optSizeId[$key];
            $sql = '';
            $sql = 'SELECT * FROM "tbl_invQuantity" WHERE "boxId"='.$unit['id'].' and "mainSizeId"='.$mainSize.' and "optSizeId"='.$optSize;
            if(!($result=pg_query($connection,$sql))){
                echo json_encode([
                    'message' => pg_last_error($connection),
                    'success' => false,
                    'code' => 500
                ]);
                return;
            }

            $mainSizeName = getSizeName($mainSize,"mainSize",$connection);
            $optSizeName = (getSizeName($optSize,"opt1size",$connection) == NULL)?"qty":getSizeName($optSize,"opt1size",$connection);
            $quantity = pg_fetch_array($result);
            if($quantity != null){
                if($qty[$key] != $quantity['qty']){
                    $sql = '';
                    $sql = 'UPDATE "tbl_invQuantity" SET ';
                    $sql .= ' qty='.$qty[$key];
                    $sql .= ' WHERE id='.$quantity['id'];
                    $count++;

                    $sql2 = '';
                    $sql2 = 'INSERT INTO "tbl_invUpdateLogQuantity" ('.
                        '"mainSize","optSize","logId","oldValue","newValue","log" ) VALUES ( '.
                        "'".$mainSize."','".$optSize."','".$log['id']."','".$quantity['qty']."','".$qty[$key]."','Update Box' )";
                    if(!($audit = pg_query($connection,$sql2))){
                        echo json_encode([
                            'message' => pg_last_error($connection),
                            'success' => false,
                            'code' => 500
                        ]);
                        return;
                    }
                    pg_free_result($audit);


                    $sql1 = '';
                    $sql1 = "INSERT INTO \"audit_logs\" (";
                    $sql1 .= " \"inventory_id\", \"employee_id\", \"updated_time\",";
                    $sql1 .= " \"log\") VALUES (";
                    $sql1 .= " '" . $styleId . "' ";
                    $sql1 .= ", '". $_SESSION['employeeID'] ."'";
                    $sql1 .= ", '". date('U') ."'";
                    $sql1 .= ", 'Update Quantity From ".$quantity['qty']." To ".$qty[$key]." for Scale ".$mainSizeName." - ".$optSizeName." in box: ".$unit['box']." '";
                    $sql1 .= ")";
                    if(!($audit = pg_query($connection,$sql1))){
                        echo json_encode([
                            'message' => pg_last_error($connection),
                            'success' => false,
                            'code' => 500
                        ]);
                        return;
                    }
                    pg_free_result($audit);
                }
            } else {
                $sql = '';
                $sql = 'INSERT INTO "tbl_invQuantity" ( ';
                $sql .= '"boxId","mainSizeId","optSizeId","qty" ) VALUES (';
                $sql .= " '".$unit['id']."','".$mainSize."','".$optSize."','".$qty[$key]."' ) ";
                $count++;

                $sql2 = '';
                $sql2 = 'INSERT INTO "tbl_invUpdateLogQuantity" ('.
                    '"mainSize","optSize","logId","oldValue","newValue","log" ) VALUES ( '.
                    "'".$mainSize."','".$optSize."','".$log['id']."','0','".$qty[$key]."','Update Box' )";
                if(!($audit = pg_query($connection,$sql2))){
                    echo json_encode([
                        'message' => pg_last_error($connection),
                        'success' => false,
                        'code' => 500
                    ]);
                    return;
                }
                pg_free_result($audit);

                $sql1 = '';
                $sql1 = "INSERT INTO \"audit_logs\" (";
                $sql1 .= " \"inventory_id\", \"employee_id\", \"updated_time\",";
                $sql1 .= " \"log\") VALUES (";
                $sql1 .= " '" . $styleId . "' ";
                $sql1 .= ", '". $_SESSION['employeeID'] ."'";
                $sql1 .= ", '". date('U') ."'";
                $sql1 .= ", 'Update Quantity From 0 To ".$qty[$key]." for Scale ".$mainSizeName." - ".$optSizeName." in box: ".$unit['box']." '";
                $sql1 .= ")";
                if(!($audit = pg_query($connection,$sql1))){
                    echo json_encode([
                        'message' => pg_last_error($connection),
                        'success' => false,
                        'code' => 500
                    ]);
                    return;
                }
                pg_free_result($audit);
            }
            if(!($result=pg_query($connection,$sql))){
                echo json_encode([
                    'message' => pg_last_error($connection),
                    'success' => false,
                    'code' => 500
                ]);
                return;
            }
        }
    }
    if($count > 0 ){
        echo json_encode([
            'message' => 'Box Updated Successfully',
            'success' => true,
            'code' => 200
        ]);
        return;
    } else {
        echo json_encode([
            'message' => 'Box Already Updated',
            'success' => true,
            'code' => 202
        ]);
        return;
    }
} else {
    echo json_encode([
        'message' => 'Box Not Found',
        'success' => false,
        'code' => 500
    ]);
    return;
}
?>