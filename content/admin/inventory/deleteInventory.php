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
$query = 'DELETE FROM "tbl_invStorage"';
$query .=" where box='".$_REQUEST['boxId']."' and \"styleId\"='".$_REQUEST['styleId']."' and \"wareHouseQty\"='0' RETURNING *";
if (!($resultProduct = pg_query($connection, $query))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
$row = pg_fetch_array($resultProduct);
$data_inv = $row;
pg_free_result($result);
if($data_inv) {
    $sql = '';
    $sql = "INSERT INTO \"audit_logs\" (";
    $sql .= " \"inventory_id\", \"employee_id\", \"updated_time\",";
    $sql .= " \"log\") VALUES (";
    $sql .= " '".$data_storage['invId']."' ";
    $sql .= ", '". $_SESSION['employeeID'] ."'";
    $sql .= ", '". date('U') ."'";
    $sql .= ", 'Delete Box: ".$_REQUEST['boxId']."'";
    $sql .= ")";
    if(!($audit = pg_query($connection,$sql))){
        $return_arr['error'] = pg_last_error($connection);
    }
    echo 1;
} else {
    echo 'No Result Found';
}

exit;
?>