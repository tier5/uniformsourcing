<?php
    require('Application.php');
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
    if($identifier != ''){
        $sum = 0;
        $sql = '';
        $sql = "SELECT * FROM \"tbl_invStorage\" WHERE unit LIKE '".$identifier."%'";
        if (!($resultProduct = pg_query($connection, $sql))) {
            print("Failed invQuery: " . pg_last_error($connection));
            exit;
        }
        while ($row = pg_fetch_array($resultProduct)) {
            $data_inventory[] = $row;
        }
        pg_free_result($resultProduct);
        if(count($data_inventory) > 0){
            foreach ($data_inventory as $inv){
                $sum = $sum+ $inv['wareHouseQty'];
            }
        }
    }
    if($sum == 0){
        $query = '';
        $query = "DELETE FROM \"tbl_invStorage\" WHERE \"unit\"='".$identifier."'";
        if (!($result = pg_query($connection, $query))) {
            print("Failed invQuery: " . pg_last_error($connection));
            exit;
        }
        $query = '';
        $query="DELETE From \"locationDetails\" WHERE \"id\"='".$_POST['id']."'";
        if (!($result1 = pg_query($connection, $query))) {
            print("Failed invQuery: " . pg_last_error($connection));
            exit;
        }
        echo 1;
        return;
    } else {
        echo 2;
        return;
    }
    echo 3;
    return;
?>