<?php
require('Application.php');
extract($_POST);
$sql = '';
$sql = "SELECT * FROM \"locationDetails\" details ".
    " LEFT JOIN \"tbl_invLocation\" location ON location.\"locationId\" = CAST(details.\"locationId\" as INT)".
    " WHERE id='".$location."'";
if (!($resultoldinv = pg_query($connection, $sql))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
$storageData = pg_fetch_array($resultoldinv);
pg_free_result($resultoldinv);
$details = '';
if($storageData['warehouse'] != ''){
    $type = 'warehouse';
} elseif ($storageData['container'] != ''){
    $type = 'container';
} else {
    $type = 'conveyor';
}
if($storageData != ''){
    foreach ($units as $unit){
        $sql = '';
        $sql = 'SELECT * FROM "tbl_invUnit" WHERE id='.$unit;
        if(!($result=pg_query($connection,$sql))){
            echo json_encode([
                'message' => pg_last_error($connection),
                'success' => false,
                'code' => 500
            ]);
            return;
        }
        $box = pg_fetch_array($result);
        pg_free_result($result);

        $sql = '';
        $sql = 'UPDATE "tbl_invUnit" SET ';
        $sql .= " \"storageId\"='".$storageData['id']."'";
        $sql .= " ,type='".$type."'";
        $sql .= " ,\"updatedBy\"='".$_SESSION['employeeID']."'";
        $sql .= " ,\"updatedAt\"='".date('Y-m-d G:i:s')."'";
        $sql .= ' WHERE id='.$unit;
        if(!($result=pg_query($connection,$sql))){
            echo json_encode([
                'message' => pg_last_error($connection),
                'success' => false,
                'code' => 500
            ]);
            return;
        }

        $sql1 = '';
        $sql1 = "INSERT INTO \"audit_logs\" (";
        $sql1 .= " \"inventory_id\", \"employee_id\", \"updated_time\",";
        $sql1 .= " \"log\") VALUES (";
        $sql1 .= " '" . $inventory_id['invId'] . "' ";
        $sql1 .= ", '". $_SESSION['employeeID'] ."'";
        $sql1 .= ", '". date('U') ."'";
        $sql1 .= ", 'Change box Location to: ".$storageData['name']." and ".$storageData[$type]." for box: ".$box['box']."'";
        $sql1 .= ")";
        if(!($audit = pg_query($connection,$sql1))){
            $return_arr['error'] = pg_last_error($connection);
        }
        pg_free_result($audit);
    }
    echo 1;
    return;
} else {
    echo 0;
    return;
}
?>