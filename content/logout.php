<?php
require('Application.php');
$sql = '';
$sql = "INSERT INTO \"audit_logs\" (";
$sql .= " \"inventory_id\", \"employee_id\", \"updated_time\",";
$sql .= " \"log\") VALUES (";
$sql .= " 'null' ";
$sql .= ", '". $_SESSION['employeeID'] ."'";
$sql .= ", '". date('U') ."'";
$sql .= ", 'Logout'";
$sql .= ")";
if(!($audit = pg_query($connection,$sql))){
    $return_arr['error'] = pg_last_error($connection);
}
session_destroy();
unset($_SESSION);
header("location: ../login.php");
exit;
?>
