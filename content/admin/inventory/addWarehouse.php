<?php
require('Application.php');
require('../../jsonwrapper/jsonwrapper.php');
$return_arr = array();
extract($_POST);
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
    $current = substr($last_result,9)+1;
}
pg_free_result($resultProduct);
$query = '';
$query = "INSERT INTO \"locationDetails\" (";
$query .=" \"warehouse\" ";
$query .=" ,\"locationId\" ";
$query .=" ) VALUES ( ";
$query .="'warehouse".$current."'";
$query .=", '".$id."'";
$query .=")";
if (!($resultProduct = pg_query($connection, $query))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
echo 1;
exit();
?>
