<?php
include('Application.php');
extract($_POST);
$sql = '';
$sql = 'SELECT * FROM "tbl_invUnit" WHERE id='.$boxId;
if (!($result = pg_query($connection, $sql))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
$unit = pg_fetch_array($result);
pg_free_result($result);
if($unit != ''){
    $sql = '';
    $sql = 'SELECT * FROM "locationDetails" WHERE id='.$storage;
    if (!($result = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    $storage = pg_fetch_array($result);
    pg_free_result($result);
    if($storage != ''){
        $sql = '';
        $sql = 'SELECT * FROM "tbl_invLocation" WHERE "locationId"='.$storage['locationId'];
        if (!($result = pg_query($connection, $sql))) {
            print("Failed invQuery: " . pg_last_error($connection));
            exit;
        }
        $LocationName = pg_fetch_array($result);
        pg_free_result($result);


        $storageId = $storage['id'];
        if($storage['warehouse'] != ''){
            $type = 'warehouse';
        } elseif ($storage['container']){
            $type = 'container';
        } else {
            $type = 'conveyor';
        }

        $sql = '';
        $sql = 'UPDATE "tbl_invUnit" SET ';
        $sql .= " \"storageId\"='".$storageId."'";
        $sql .= " ,type='".$type."'";
        $sql .= " ,\"updatedBy\"='".$_SESSION['employeeID']."'";
        $sql .= " ,\"updatedAt\"='".date('Y-m-d G:i:s')."'";
        $sql .= ' WHERE id='.$unit['id'];
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
            ' "boxId","styleId","createdBy","createdAt",type ) VALUES ('.
            "'".$unit['box']."','".$unit['styleId']."','".$_SESSION['employeeID']."','".date('Y-m-d G:i:s')."','Change Location' ) RETURNING *";
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

        $sql1 = '';
        $sql1 = "INSERT INTO \"audit_logs\" (";
        $sql1 .= " \"inventory_id\", \"employee_id\", \"updated_time\",";
        $sql1 .= " \"log\") VALUES (";
        $sql1 .= " '" . $unit['styleId'] . "' ";
        $sql1 .= ", '". $_SESSION['employeeID'] ."'";
        $sql1 .= ", '". date('U') ."'";
        $sql1 .= ", 'Change box Location to: ".$LocationName['name']." and ".$storageData[$type]." for box: ".$unit['box']."'";
        $sql1 .= ")";
        if(!($audit = pg_query($connection,$sql1))){
            $return_arr['error'] = pg_last_error($connection);
        }
        pg_free_result($audit);
        echo 1;
        return;
    } else {
        echo 0;
        return;
    }
} else {
    echo 0;
    return;
}
?>