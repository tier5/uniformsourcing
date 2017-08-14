<?php
require('Application.php');
$returnArray['name'] = '';
extract($_POST);
$sql = 'SELECT * FROM "tbl_invColor" WHERE "colorId"='.$colorId.' AND "styleId"='.$styleId;
if(!($result=pg_query($connection,$sql))){
    echo json_encode([
        'message' => pg_last_error($connection),
        'success' => false,
        'code' => 500
    ]);
    return;
}
$color = pg_fetch_array($result);
pg_free_result($result);
$sql = '';
$sql = 'SELECT count(*) FROM "tbl_invUnit" WHERE "colorId"='.$colorId.' and "styleId"='.$styleId;
if(!($result=pg_query($connection,$sql))){
    echo json_encode([
        'message' => pg_last_error($connection),
        'success' => false,
        'code' => 500
    ]);
    return;
}
$count = pg_fetch_array($result);
pg_free_result($result);
if($count['count'] > 0 ){
    echo json_encode([
        'message' => 'Please Delete all the box for this color before delete this color',
        'success' => false,
        'code' => 400
    ]);
    return;
} else {
    $sql = '';
    $sql = 'DELETE FROM "tbl_invColor" WHERE "colorId"='.$colorId.' and "styleId"='.$styleId;
    if(!($result=pg_query($connection,$sql))){
        echo json_encode([
            'message' => pg_last_error($connection),
            'success' => false,
            'code' => 500
        ]);
        return;
    }

    $sql = '';
    $sql = 'INSERT INTO "tbl_invUpdateLog" ('.
        '"styleId","createdBy","createdAt",type ) VALUES ('.
        "'".$styleId."','".$_SESSION['employeeID']."','".date('Y-m-d G:i:s')."','Delete Color' ) RETURNING *";
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
    $sql .= " '" . $styleId . "' ";
    $sql .= ", '". $_SESSION['employeeID'] ."'";
    $sql .= ", '". date('U') ."'";
    $sql .= ", 'Delete Color:  ".$colorId."'";
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
        'message' => 'Color Deleted Successfully',
        'success' => true,
        'code' => 202
    ]);
    return;
}
?>