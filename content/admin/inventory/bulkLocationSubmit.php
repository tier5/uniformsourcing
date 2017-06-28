<?php
require('Application.php');
extract($_POST);
$sql = '';
$sql = "SELECT * FROM \"locationDetails\" WHERE id='".$location."'";
if (!($resultoldinv = pg_query($connection, $sql))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
$storageData = pg_fetch_array($resultoldinv);
pg_free_result($resultoldinv);

if($storageData != ''){
    $sql = '';
    $sql = "SELECT * FROM \"tbl_invLocation\" WHERE \"locationId\"='".$storageData['locationId']."'";
    if (!($resultoldinv = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    $locationData = pg_fetch_array($resultoldinv);
    pg_free_result($resultoldinv);
    if($locationData != ''){
        if($storageData['warehouse'] != ''){
            $locationIdetifier = $locationData['identifier'].'_'.$storageData['warehouse'];
        } elseif ($storageData['container'] != ''){
            $locationIdetifier = $locationData['identifier'].'_'.$storageData['container'];
        } elseif ($storageData['conveyor'] != '') {
            $locationIdetifier = $locationData['identifier'].'_'.$storageData['conveyor'];
        } else {
            echo 0;
            return;
        }
        foreach ($units as $unit){
            $box = explode('_',$unit);
            if(count($box) == 3){
                $finalLocationIdentifier = $locationIdetifier.'_'.$box[2];
            } else {
                $finalLocationIdentifier = $locationIdetifier.'_'.$box[0];
            }
            $sql = '';
            $sql = "UPDATE \"tbl_invStorage\" SET ";
            $sql .= " unit='".$finalLocationIdentifier."'";
            $sql .= " , \"locationId\"='".$storageData['locationId']."'";
            $sql .= " , box='".$finalLocationIdentifier."'";
            $sql .= " WHERE unit='".$unit."' and \"styleId\"='".$styleId."'";
            if (!($resultoldinv = pg_query($connection, $sql))) {
                print("Failed invQuery: " . pg_last_error($connection));
                exit;
            }
            pg_free_result($resultoldinv);
        }
        echo 1;
        return;
    } else {
        echo 0;
        return;
    }
} else {
    echo 0;
    return;
}

?>