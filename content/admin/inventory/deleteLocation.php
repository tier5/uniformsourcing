<?php
require('Application.php');
$sql = '';
$sql = "SELECT * FROM \"tbl_invStorage\" WHERE \"locationId\"='".$_POST['locationId']."'";
if (!($resultProduct = pg_query($connection, $sql))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
while ($row = pg_fetch_array($resultProduct)) {
    $data_location[] = $row;
}
$total = 0;
for ($i=0;$i<count($data_location);$i++){
    $total = $total+$data_location[$i]['wareHouseQty'];
}
echo $total;
if($total == 0){
    $query = "DELETE FROM \"tbl_invStorage\" WHERE \"locationId\"='".$_POST['locationId']."'";
    if (!($result = pg_query($connection, $query))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    $query1="DELETE From \"locationDetails\" WHERE \"locationId\"='".$_POST['locationId']."'";
    if (!($result1 = pg_query($connection, $query1))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    $query2="DELETE From \"tbl_invLocation\" WHERE \"locationId\"='".$_POST['locationId']."'";
    if (!($result1 = pg_query($connection, $query2))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    echo 1;
    exit;
} else {
    echo 2;
    exit;
}
?>