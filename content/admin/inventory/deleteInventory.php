<?php
require('Application.php');
$query = 'DELETE FROM "tbl_invStorage"';
$query .=" where box='".$_REQUEST['boxId']."'";
if (!($resultProduct = pg_query($connection, $query))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
echo 1;
exit;
?>