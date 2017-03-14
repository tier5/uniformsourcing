<?php
require('Application.php');

$query = "INSERT INTO \"tbl_invStorage\" (";
$query .= " \"invId\" ";
$query .= " ,\"styleId\" ";
$query .= " ,\"colorId\" ";
if ($data_inv['sizeScaleId'] != "") $query .= " ,\"sizeScaleId\" ";
if ($data_inv['opt1ScaleId'] != "") $query .= " ,\"opt1ScaleId\" ";
if ($data_inv['opt2ScaleId'] != "") $query .= " ,\"opt2ScaleId\" ";
$query .= " ,\"locationId\" ";
if ($room != "") $query .= " ,\"room\" ";
if ($wareHouseRow != "") $query .= " ,\"row\" ";
if ($rack != "") $query .= " ,\"rack\" ";
if ($shelf != "") $query .= " ,\"shelf\" ";
if ($box != "") $query .= " ,\"box\" ";
$query .= " ,\"wareHouseQty\" ";
$query .= " ,\"createdBy\" ";
$query .= " ,\"updatedBy\" ";
$query .= " ,\"createdDate\" ";
$query .= " ,\"updatedDate\" ";
$query .= ")";
$query .= " VALUES (";
$query .= " '" . $data_inv['inventoryId'] . "' ";
$query .= " ,'" . $data_inv['styleId'] . "' ";
$query .= " ,'" . $data_inv['colorId'] . "' ";
if ($data_inv['sizeScaleId'] != "") $query .= " ,'" . $data_inv['sizeScaleId'] . "' ";
if ($data_inv['opt1ScaleId'] != "") $query .= " ,'" . $data_inv['opt1ScaleId'] . "' ";
if ($data_inv['opt2ScaleId'] != "") $query .= " ,'" . $data_inv['opt2ScaleId'] . "' ";
$query .= " ,'" . $data_inv['locationId'] . "' ";
if ($room != "") $query .= " ,'" . $room . "' ";
if ($wareHouseRow != "") $query .= " ,'" . $wareHouseRow . "' ";
if ($rack != "") $query .= " ,'" . $rack . "' ";
if ($shelf != "") $query .= " ,'" . $shelf . "' ";
if ($box != "") $query .= " ,'" . $box . "' ";
$query .= " ,0 ";
$query .= " ,'" . $_SESSION['employeeID'] . "' ";
$query .= " ,'" . $_SESSION['employeeID'] . "' ";
$query .= " ,'" . date('U') . "' ";
$query .= " ,'" . date('U') . "' ";
$query .= " )";

if (!($resultProduct = pg_query($connection, $query))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
echo 1;
exit;
?>