<?php
require('Application.php');
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
if(in_array($_POST['name'],$locArr)){
    echo 2;
    return;
}
$sql = '';
$sql = "SELECT * FROM \"locationDetails\" WHERE id='".$_POST['id']."'";
if (!($resultProduct = pg_query($connection, $sql))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
$locationDetailsOld = pg_fetch_array($resultProduct);
pg_free_result($resultProduct);
$identifierOld = '';
if($locationDetailsOld != ''){
    $sql = "SELECT identifier FROM \"tbl_invLocation\" WHERE \"locationId\"='".$locationDetailsOld['locationId']."'";
    if (!($resultProduct = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    $locationIdentifierOld = pg_fetch_array($resultProduct);
    pg_free_result($resultProduct);
    $identifierOld = $locationIdentifierOld['identifier'].'_'.$locationDetailsOld[$_POST['type']];
}
if($identifierOld != ''){
    $sql = '';
    $sql = "SELECT * FROM \"tbl_invStorage\" WHERE unit LIKE '".$identifierOld."%'";
    if (!($resultProduct = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($resultProduct)) {
        $data_storage[] = $row;
    }
    $query = '';
    $query = "UPDATE \"locationDetails\" ";
    $query .=" SET \"". $_POST['type'] ."\" ='".$_POST['name']."'";
    $query .=" WHERE \"id\" ='".$_POST['id']."'";
    if (!($resultProduct = pg_query($connection, $query))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    $sql = '';
    $sql = "SELECT * FROM \"locationDetails\" WHERE id='".$_POST['id']."'";
    if (!($resultProduct = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    $locationDetails = pg_fetch_array($resultProduct);
    pg_free_result($resultProduct);
    $identifier = '';
    if($locationDetails != ''){
        $sql = "SELECT identifier FROM \"tbl_invLocation\" WHERE \"locationId\"='".$locationDetails['locationId']."'";
        if (!($resultProduct = pg_query($connection, $sql))) {
            print("Failed invQuery: " . pg_last_error($connection));
            exit;
        }
        $locationIdentifier = pg_fetch_array($resultProduct);
        pg_free_result($resultProduct);
        $identifier = $locationIdentifier['identifier'].'_'.$locationDetails[$_POST['type']];
    }
    foreach ($data_storage as $key => $value){
        $iden = explode('_',$value['unit']);
        $unit = $identifier.'_'.$iden[2];
        $sql = '';
        $sql = "UPDATE \"tbl_invStorage\" SET unit='".$unit."' WHERE \"storageId\"=".$value['storageId'];
        if (!($resultProduct = pg_query($connection, $sql))) {
            print("Failed invQuery: " . pg_last_error($connection));
            exit;
        }
    }
}
echo 1;
return;
?>