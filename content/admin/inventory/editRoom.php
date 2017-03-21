<?php
require('Application.php');
$sql = "SELECT * FROM \"tbl_invStorage\" WHERE box='".$_REQUEST['boxId']."' and \"styleId\"='".$_REQUEST['styleId']."' ";
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
$query .=" where box='".$_REQUEST['boxId']."' and \"styleId\"='".$_REQUEST['styleId']."' ";
if (!($resultProduct = pg_query($connection, $query))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
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