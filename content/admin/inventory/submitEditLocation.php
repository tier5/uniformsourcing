<?php
require('Application.php');
require('../../jsonwrapper/jsonwrapper.php');
$return_arr = array();
extract($_POST);
$sql = '';
$sql = 'SELECT * FROM "tbl_invLocation"';
if (!($resultProduct = pg_query($connection, $sql))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
while ($row = pg_fetch_array($resultProduct)){
    $data_location[] = $row;
}
pg_free_result($resultProduct);
$locArr = [];
foreach ($data_location as $value){
    if($value['identifier'] != null){
        $locArr[] = $value['identifier'];
    }
}
if(in_array($identifier,$locArr)){
    echo 2;
    return;
}
$sql = '';
$sql = "SELECT identifier FROM \"tbl_invLocation\" WHERE \"locationId\"='".$id."'";
if (!($resultProduct = pg_query($connection, $sql))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
$iden = pg_fetch_row($resultProduct);
pg_free_result($resultProduct);
$sql = '';
$sql = "SELECT * FROM \"tbl_invStorage\" WHERE unit LIKE '".$iden[0]."_%'";
if (!($resultProduct = pg_query($connection, $sql))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
while ($row = pg_fetch_array($resultProduct)){
    $data_storage[] = $row;
}
pg_free_result($resultProduct);
$query = '';
$query = "UPDATE \"tbl_invLocation\" SET ";
$query .= "\"identifier\" = '" . $identifier . "' ";
$query .= "  where \"locationId\"='" . $id . "' ";
if (!($resultProduct = pg_query($connection, $query))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
pg_free_result($resultProduct);
foreach ($data_storage as $key=>$value){
    $unit = explode('_',$value['unit']);
    $newUnit = $identifier.'_'.$unit[1].'_'.$unit[2];
    $sql = '';
    $sql = "UPDATE \"tbl_invStorage\" SET unit='".$newUnit."' WHERE \"storageId\"=".$value['storageId'];
    if (!($resultProduct = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
}
echo 1;
exit();
?>
