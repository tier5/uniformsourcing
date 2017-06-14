<?php
    require('Application.php');
    require('../../jsonwrapper/jsonwrapper.php');
    $return_arr = array();
    extract($_POST);
    $sql="SELECT * from \"tbl_invLocation\" WHERE \"locationId\"='".$id."'";
    if (!($resultProduct = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($resultProduct)) {
        $location = $row;
    }
    $sql = '';
    $sql = 'SELECT * FROM "locationDetails"';
    if (!($resultLocation = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($resultLocation)) {
        $data_location[] = $row;
    }
    pg_free_result($resultLocation);
    $locArr = [];
    foreach ($data_location as $value){
        if($value['warehouse'] != null){
            $locArr[] = $value['warehouse'];
        }
        if($value['container'] != null){
            $locArr[] = $value['container'];
        }
        if($value['conveyor'] != null){
            $locArr[] = $value['conveyor'];
        }
    }
    if(in_array($name,$locArr)){
        echo 2;
        return;
    }
    /*$query = '';
    $query = "SELECT conveyor from \"locationDetails\"";
    $query .= "  where \"locationId\"='" . $id . "' ";
    if (!($resultProduct = pg_query($connection, $query))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($resultProduct)) {
        $conveyor[] = $row;
    }
    if (count($conveyor) > 0) {
        for ($i=0;$i<count($conveyor);$i++) {
            if($conveyor[$i]['conveyor'] != null){
                $last_result = $conveyor[$i]['conveyor'];
            }
        }
    } else {
        $last_result = 0;
    }
    if($last_result == '0'){
        $current = 1;
    } else {
        $current = substr($last_result,2)+1;
    }
    pg_free_result($resultProduct);*/
    $query = '';
    $query = "INSERT INTO \"locationDetails\" (";
    $query .=" \"conveyor\" ";
    $query .=" ,\"locationId\" ";
    $query .=" ) VALUES ( ";
    $query .="'".$name."'";
    $query .=", '".$id."'";
    $query .=")";
    if (!($resultProduct = pg_query($connection, $query))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    $conveyor = '';
    $conveyor = "INSERT INTO \"tbl_conveyor\" (";
    $conveyor .= " \"locationId\", \"quantity\",\"name\") VALUES (";
    $conveyor .= " '".$id."','0','".$name."')";
    if (!($resultProduct = pg_query($connection, $conveyor))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    $sql = '';
    $sql = "INSERT INTO \"audit_logs\" (";
    $sql .= " \"inventory_id\", \"employee_id\", \"updated_time\",";
    $sql .= " \"log\") VALUES (";
    $sql .= " 'null' ";
    $sql .= ", '". $_SESSION['employeeID'] ."'";
    $sql .= ", '". date('U') ."'";
    $sql .= ", 'Add Conveyor ".$name." at Location ".$location['identifier']."'";
    $sql .= ")";
    if(!($audit = pg_query($connection,$sql))){
        $return_arr['error'] = pg_last_error($connection);
    }
    echo 1;
    exit();
?>
