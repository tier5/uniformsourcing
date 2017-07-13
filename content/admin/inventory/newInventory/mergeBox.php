<?php
require('Application.php');
$returnArray['name'] = '';
extract($_POST);
$sql = '';
$sql = 'SELECT * FROM "tbl_invUnit" WHERE id='.$currentBox;
if(!($result=pg_query($connection,$sql))){
    echo json_encode([
        'message' => pg_last_error($connection),
        'success' => false,
        'code' => 500
    ]);
    return;
}
$current = pg_fetch_array($result);
pg_free_result($result);
$sql = '';
$sql = 'SELECT * FROM "tbl_invUnit" WHERE id='.$targetBox;
if(!($result=pg_query($connection,$sql))){
    echo json_encode([
        'message' => pg_last_error($connection),
        'success' => false,
        'code' => 500
    ]);
    return;
}
$target = pg_fetch_array($result);
pg_free_result($result);
if($target != '' && $current != ''){
    $sql = '';
    $sql = 'SELECT * FROM "tbl_invQuantity" WHERE "boxId"='.$current['id'];
    if(!($result=pg_query($connection,$sql))){
        echo json_encode([
            'message' => pg_last_error($connection),
            'success' => false,
            'code' => 500
        ]);
        return;
    }
    while($row = pg_fetch_array($result)){
        $currentQuantity[] = $row;
    }
    pg_free_result($result);
    foreach ($currentQuantity as $value){
        $sql = '';
        $sql = 'SELECT * FROM "tbl_invQuantity" WHERE "boxId"='.$target['id'].'and "mainSizeId"='.$value['mainSizeId'].' and "optSizeId"='.$value['optSizeId'];
        if(!($result=pg_query($connection,$sql))){
            echo json_encode([
                'message' => pg_last_error($connection),
                'success' => false,
                'code' => 500
            ]);
            return;
        }
        $targetQuantity = pg_fetch_array($result);
        pg_free_result($result);
        if($targetQuantity != ''){
            $quantity = $targetQuantity['qty'] + $value['qty'];
            $sql = '';
            $sql = 'UPDATE "tbl_invQuantity" SET ';
            $sql .= ' qty='.$quantity;
            $sql .= ' WHERE id='.$targetQuantity['id'];
        } else {
            $sql = '';
            $sql = 'INSERT INTO "tbl_invQuantity" ( ';
            $sql .= '"boxId","mainSizeId","optSizeId","qty" ) VALUES (';
            $sql .= " '".$target['id']."','".$value['mainSizeId']."','".$value['optSizeId']."','".$value['qty']."' ) ";
        }
        if(!($result=pg_query($connection,$sql))){
            echo json_encode([
                'message' => pg_last_error($connection),
                'success' => false,
                'code' => 500
            ]);
            return;
        }
        pg_free_result($result);
        $sql = '';
        $sql = 'UPDATE "tbl_invQuantity" SET ';
        $sql .= ' qty=0';
        $sql .= ' WHERE id='.$value['id'];
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
    $sql = '';
    $sql = 'UPDATE "tbl_invUnit" SET merged=1 WHERE id='.$current['id'];
    if(!($result=pg_query($connection,$sql))){
        echo json_encode([
            'message' => pg_last_error($connection),
            'success' => false,
            'code' => 500
        ]);
        return;
    }
    pg_free_result($result);
    $sql = '';
    $sql = 'SELECT * FROM "tbl_invUnit" WHERE id='.$target['id'];
    if(!($result=pg_query($connection,$sql))){
        echo json_encode([
            'message' => pg_last_error($connection),
            'success' => false,
            'code' => 500
        ]);
        return;
    }
    $data = pg_fetch_array($result);
    pg_free_result($result);


    $sql = '';
    $sql = 'INSERT INTO "tbl_invUpdateLog" ('.
        ' "boxId","styleId","createdBy","createdAt",type ) VALUES ('.
        "'".$current['box']."','".$current['styleId']."','".$_SESSION['employeeID']."','".date('Y-m-d G:i:s')."','Merge Box with ".$target['box']."' ) RETURNING *";
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


    $sql = '';
    $sql = "INSERT INTO \"audit_logs\" (";
    $sql .= " \"inventory_id\", \"employee_id\", \"updated_time\",";
    $sql .= " \"log\") VALUES (";
    $sql .= " '" . $current['styleId'] . "' ";
    $sql .= ", '". $_SESSION['employeeID'] ."'";
    $sql .= ", '". date('U') ."'";
    $sql .= ", 'Merge box:  ".$current['box']." to ".$target['box']." '";
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
    echo json_encode([
        'message' => 'Box Merged Successfully',
        'success' => true,
        'info' => $data,
        'code' => 200
    ]);
    return;
} else {
    echo json_encode([
        'message' => 'Box Not Found',
        'success' => false,
        'code' => 400
    ]);
    return;
}
?>