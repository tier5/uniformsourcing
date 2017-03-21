<?php
require('Application.php');
require('../../jsonwrapper/jsonwrapper.php');
$return_arr = array();
extract($_POST);
//print_r($name." ".$identifier." ".$warehouse." ".$container." ".$conveyor);
$query = '';
$query = "INSERT INTO \"tbl_invLocation\" (";
$query .=" \"name\" ";
$query .=" ,\"identifier\" ";
$query .=" ) VALUES ( ";
$query .="'".$name."'";
$query .=", '".$identifier."'";
$query .=")";
if (!($resultProduct = pg_query($connection, $query))) {
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
$sql .= ", 'Add Location ".$name."  as  ".$identifier."'";
$sql .= ")";
if(!($audit = pg_query($connection,$sql))){
    $return_arr['error'] = pg_last_error($connection);
}
echo 1;
exit();
?>
