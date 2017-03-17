<?php
require('Application.php');
require('../../jsonwrapper/jsonwrapper.php');
$return_arr = array();
extract($_POST);
//print_r($name." ".$identifier." ".$warehouse." ".$container." ".$conveyor);
$query = '';
$query = "UPDATE \"inventoryLocation\" SET ";
$query .= "\"totalWarehouse\" = '" . $warehouse . "' ";
$query .= ",\"totalContainer\" = '" . $container . "' ";
$query .= ",\"tatalConveyor\" = '" . $conveyor . "' ";
$query .= "  where \"id\"='" . $id . "' ";
if (!($resultProduct = pg_query($connection, $query))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
echo 1;
exit();
?>
