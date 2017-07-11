<?php
require('Application.php');
$returnArray['name'] = '';
extract($_POST);
$sql = '';
$sql = 'SELECT * FROM "tbl_invUnit" WHERE id='.$boxId;
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
    if($unit['type'] == 'warehouse'){
        $sql = '';
        $sql = 'UPDATE "tbl_invUnit" SET ';
        $sql .= " row='".$row."'";
        $sql .= " ,rack='".$rack."'";
        $sql .= " ,shelf='".$shelf."'";
        $sql .= " ,\"updatedBy\"='".$_SESSION['employeeID']."'";
        $sql .= " ,\"updatedAt\"='".date('Y-m-d G:i:s')."'";
        $sql .= ' WHERE id='.$boxId;
        if(!($result=pg_query($connection,$sql))){
            echo json_encode([
                'message' => pg_last_error($connection),
                'success' => false,
                'code' => 500
            ]);
            return;
        }
        $sql = '';
        $sql = "INSERT INTO \"audit_logs\" (";
        $sql .= " \"inventory_id\", \"employee_id\", \"updated_time\",";
        $sql .= " \"log\") VALUES (";
        $sql .= " '" . $unit['styleId'] . "' ";
        $sql .= ", '". $_SESSION['employeeID'] ."'";
        $sql .= ", '". date('U') ."'";
        $sql .= ", 'Update box:  ".$unit['box']." for Row:".$unit['row']." to ".$row.", Rack:".$unit['rack']." to ".$rack." and Shelf:".$unit['shelf']." to ".$shelf."'";
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
            'message' => 'Unit Updated Successfully',
            'success' => true,
            'code' => 200
        ]);
        return;
    } else {
        echo json_encode([
            'message' => 'Unit is not a warehouse',
            'success' => false,
            'code' => 400
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