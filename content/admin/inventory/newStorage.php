<?php
require('Application.php');
require('../../jsonwrapper/jsonwrapper.php');
$rowCount = 0;
$mainCount = 0;
$invId = 0;
$styleId = $_GET['styleId'];
$colorId = $_GET['colorId'];
$row = $_GET['row'];
$rack = $_GET['rack'];
$shelf = $_GET['shelf'];
$box = $_GET['boxId'];
$room = $_GET['room'];
$sql = 'select "inventoryId","styleNumber",col."name","sizeScaleId", price, "locationId","opt1ScaleId", "opt2ScaleId", quantity, "newQty", "mainSize", "rowSize", "columnSize" from "tbl_inventory" as inv inner join "tbl_invColor" as col on col."colorId"=inv."colorId" where inv."styleId"=' . $styleId . ' and inv."colorId"=' . $colorId . ' and "isStorage"=0 order by "inventoryId"';

if (!($result = pg_query($connection, $sql))) {
    print("Failed StorageData: " . pg_last_error($connection));
    exit;
}
while ($row = pg_fetch_array($result)) {
    $data_inv[] = $row;
}
print_r($data_inv[0]);die();
pg_free_result($result);
$sql = 'select "storageId", "invId", "sizeScaleId", "opt1ScaleId", "opt2ScaleId", "locationId", "conveyorSlotId", "conveyorQty", room, "row", rack, shelf, box, "wareHouseQty", "location", "otherQty" from "tbl_invStorage" where "styleId"=' . $styleId . ' and "colorId"=' . $colorId . ' order by "storageId"';

if (!($result = pg_query($connection, $sql))) {
    print("Failed Data_invQuery: " . pg_last_error($connection));
    exit;
}
while ($row = pg_fetch_array($result)) {
    $data_storage[] = $row;
}
pg_free_result($result);
$query = 'select * from "tbl_invLocation" order by "locationId"';
if (!($result = pg_query($connection, $query))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
while ($row = pg_fetch_array($result)) {
    $data_loc[] = $row;
}
pg_free_result($result);