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