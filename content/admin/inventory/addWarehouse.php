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
//print_r($name." ".$identifier." ".$warehouse." ".$container." ".$conveyor);
$query = '';
$query = "SELECT warehouse from \"locationDetails\"";
$query .= "  where \"locationId\"='" . $id . "' ";
if (!($resultProduct = pg_query($connection, $query))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
while ($row = pg_fetch_array($resultProduct)) {
    $data_warehouse[] = $row;
}
if (count($data_warehouse) > 0) {
    for ($i=0;$i<count($data_warehouse);$i++) {
        if($data_warehouse[$i]['warehouse'] != null){
            $last_result = $data_warehouse[$i]['warehouse'];
        }
    }
} else {
    $last_result = 0;
}
if($last_result == '0'){
    $current = 1;
} else {
    $current = substr($last_result,1)+1;
}
pg_free_result($resultProduct);
$query = '';
$query = "INSERT INTO \"locationDetails\" (";
$query .=" \"warehouse\" ";
$query .=" ,\"locationId\" ";
$query .=" ) VALUES ( ";
$query .="'W".$current."'";
$query .=", '".$id."'";
$query .=")";
if (!($resultProduct = pg_query($connection, $query))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
pg_free_result($resultProduct);
// $warehouse = '';
// $warehouse = "INSERT INTO \"warehouse\" (";
// $warehouse .= " \"locationId\", \"warehouse_name\") VALUES (";
// $warehouse .= " '".$id."','W".$current."')";
// //print_r($warehouse);die();
// if (!($resultProduct = pg_query($connection, $warehouse))) {
//     print("Failed invQuery: " . pg_last_error($connection));
//     exit;
// }
// pg_free_result($resultProduct);
$sql = '';
$sql = "INSERT INTO \"audit_logs\" (";
$sql .= " \"inventory_id\", \"employee_id\", \"updated_time\",";
$sql .= " \"log\") VALUES (";
$sql .= " 'null' ";
$sql .= ", '". $_SESSION['employeeID'] ."'";
$sql .= ", '". date('U') ."'";
$sql .= ", 'Add Warehouse W".$current." at Location ".$location['identifier']."'";
$sql .= ")";
if(!($audit = pg_query($connection,$sql))){
    $return_arr['error'] = pg_last_error($connection);
}
pg_free_result($audit);
echo 1;
exit();
?>
