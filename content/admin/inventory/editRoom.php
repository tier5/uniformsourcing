<?php
require('Application.php');
$sql = "SELECT * FROM \"tbl_invStorage\" WHERE unit='".$_REQUEST['unitId']."' and \"styleId\"='".$_REQUEST['styleId']."' ";
if (!($resultProduct = pg_query($connection, $sql))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
while ($row = pg_fetch_array($resultProduct)) {
    $data_storage = $row;
}

$query = 'UPDATE "tbl_invStorage" SET ';
$query .="room='".$_REQUEST['room']."'";
$query .=",rack='".$_REQUEST['rack']."'";
$query .=",shelf='".$_REQUEST['self']."'";
$query .=",row='".$_REQUEST['row']."' ";
$query .=",\"updatedDate\"='".date('U')."' ";
$query .=" where unit='".$_REQUEST['unitId']."' and \"styleId\"='".$_REQUEST['styleId']."' ";
if (!($resultProduct = pg_query($connection, $query))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}else{
    if($data_storage['room']!=$_REQUEST['room']){
        $storage_array['Room'][0]=$data_storage['room'];
        $storage_array['Room'][1]=$_REQUEST['room'];
    }
    if($data_storage['rack']!=$_REQUEST['rack']){
        $storage_array['Rack'][0]=$data_storage['rack'];
        $storage_array['Rack'][1]=$_REQUEST['rack'];
    }
    if($data_storage['shelf']!=$_REQUEST['self']){
        $storage_array['Self'][0]=$data_storage['shelf'];
        $storage_array['Self'][1]=$_REQUEST['self'];
    }
    if($data_storage['row']!=$_REQUEST['row']){
        $storage_array['Row'][0]=$data_storage['row'];
        $storage_array['Row'][1]=$_REQUEST['row'];
    }
    if(!empty($storage_array)){
                        $json_array['Storage']=$storage_array;
                        $json_array=json_encode($json_array);
                        // print_r($json_array);
                        // print_r($_SESSION['employeeID']);
                        $sql = '';
                        $sql = "INSERT INTO \"tbl_log_updates\" (";
                        $sql .= " \"styleId\", \"createdBy\", \"createdDate\", \"updatedDate\", \"previous\", \"present\" ";
                        $sql .= " ) VALUES (";
                        $sql .= " '" . $_REQUEST['styleId'] . "' ";
                        $sql .= ", '". $_SESSION['employeeID'] ."'";
                        $sql .= ", '". date('U') ."'";
                        $sql .= ", '". date('U') ."'";
                        $sql .= ", '".$json_array."'";
                        $sql .= ", 'Storage'";
                        $sql .= ")";
                        //echo $sql;
                        if(!($audit = pg_query($connection,$sql))){
                        $return_arr['error'] = pg_last_error($connection);
                        }
                    }
      
}
if($data_storage['room'] != $_REQUEST['room'] || $data_storage['rack'] != $_REQUEST['rack'] || $_REQUEST['self'] != $data_storage['shelf'] || $data_storage['row'] != $_REQUEST['row']){
    $sql = '';
    $sql = "INSERT INTO \"audit_logs\" (";
    $sql .= " \"inventory_id\", \"employee_id\", \"updated_time\",";
    $sql .= " \"log\") VALUES (";
    $sql .= " '".$data_storage['invId']."' ";
    $sql .= ", '". $_SESSION['employeeID'] ."'";
    $sql .= ", '". date('U') ."'";
    $sql .= ", 'Edit Box Location room: ".$data_storage['room']." to ".$_REQUEST['room']." rack: ".$data_storage['rack']." to ".$_REQUEST['rack']." shelf: ".$data_storage['shelf']." to ".$_REQUEST['self']." Row: ".$data_storage['row']." to ".$_REQUEST['row']."'";
    $sql .= ")";
    if(!($audit = pg_query($connection,$sql))){
        $return_arr['error'] = pg_last_error($connection);
    }
}
echo 1;
exit;
?>