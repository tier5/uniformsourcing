<?php
require('Application.php');
require('../../jsonwrapper/jsonwrapper.php');
$return_arr = array();
extract($_POST);
//print_r($name." ".$identifier." ".$warehouse." ".$container." ".$conveyor);
$query = '';
$query = "INSERT INTO \"inventoryLocation\" (";
$query .=" \"location\" ";
$query .=" ,\"locIdn\" ";
$query .=" ,\"totalWarehouse\" ";
$query .=" ,\"totalContainer\" ";
$query .=" ,\"tatalConveyor\" ";
$query .=" ) VALUES ( ";
$query .="'".$name."'";
$query .=", '".$identifier."'";
$query .=", '".$warehouse."'";
$query .=", '".$container."'";
$query .=", '".$conveyor."'";
$query .=")";
if (!($resultProduct = pg_query($connection, $query))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
echo 1;
exit();
?>
