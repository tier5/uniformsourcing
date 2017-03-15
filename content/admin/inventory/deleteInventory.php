<?php
require('Application.php');
$query = 'DELETE FROM "tbl_invStorage"';
$query .=" where box='".$_REQUEST['boxId']."' and \"wareHouseQty\"='0' RETURNING *";
if (!($resultProduct = pg_query($connection, $query))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
$row = pg_fetch_array($resultProduct);
$data_inv = $row;
pg_free_result($result);
if($data_inv) {
    echo 1;
} else {
    echo 'No Result Found';
}

exit;
?>