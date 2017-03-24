<?php
require('Application.php');
require('../../jsonwrapper/jsonwrapper.php');
if ($debug == "on") {
    require('../../header.php');
    foreach ($_POST as $key => $value) {
        if ($key != "submit") {
            echo "$key = $value<br/>";
        }
    }
}
$return_arr = array();
$rowCount = 0;
$mainCount = 0;
$invId = 0;
$styleId = $_GET['styleId'];
$colorId = $_GET['colorId'];
$row = $_GET['row'];
$rack = $_GET['rack'];
$shelf = $_GET['shelf'];
$unit = $_GET['unitId'];
$room = $_GET['room'];
$sql = 'select "inventoryId","styleNumber",col."name","sizeScaleId", price, "locationId","opt1ScaleId", "opt2ScaleId", quantity, "newQty", "mainSize", "rowSize", "columnSize" from "tbl_inventory" as inv inner join "tbl_invColor" as col on col."colorId"=inv."colorId" where inv."styleId"=' . $styleId . ' and inv."colorId"=' . $colorId . ' and "isStorage"=0 order by "inventoryId"';

if (!($result = pg_query($connection, $sql))) {
    print("Failed StorageData: " . pg_last_error($connection));
    exit;
}
while ($row = pg_fetch_array($result)) {
    $data_inv1[] = $row;
}
pg_free_result($result);
$totalRow = count($data_inv1);
//echo '<pre>';print_r($totalRow);die();
$wareHouseRow = $row;
$return_arr['name'] = "";
$return_arr['error'] = "";
$return_arr['type'] = "";
$sql1='SELECT "locationId" FROM "tbl_invStorage" where unit='."'".$unit."' and \"sizeScaleId\" IS NULL";
if (!($result = pg_query($connection, $sql1))) {
    print("Failed Data_invQuery: " . pg_last_error($connection));
    exit;
}
$row = pg_fetch_array($result);
$locId = $row;
if($locId == null){
    $locId = $data_inv1[0]['locationId'];
} else {
    $locId = $locId['locationId'];
}
for ($i=0;$i<$totalRow;$i++) {
    if (isset($data_inv1[$i]['inventoryId'])) {
        $sql = 'select "storageId", "invId", "sizeScaleId", "opt1ScaleId", "opt2ScaleId", "locationId", "conveyorSlotId", "conveyorQty", room, "row", rack, shelf, unit, "wareHouseQty", "location", "otherQty" from "tbl_invStorage" where "invId"=' . $data_inv1[$i]['inventoryId'] . ' and "unit"='." '".$unit."'";
        if (!($result = pg_query($connection, $sql))) {
            print("Failed Data_invQuery: " . pg_last_error($connection));
            exit;
        }
        $row = pg_fetch_array($result);
        $data_storage = $row;
        if($data_inv1[$i]['quantity'] != "" && $data_inv1[$i]['quantity'] > 0)
            $qty = $data_inv1[$i]['newQty'] - $data_inv1[$i]['quantity'];
        else
            $qty = $data_inv1[$i]['newQty'];
        if($data_storage != null) {
            if($data_storage['wareHouseQty'] > $data_inv1[$i]['newQty']) {
                $newQty = $data_inv1[$i]['quantity'] + ($data_inv1[$i]['newQty']-$data_storage['wareHouseQty']);
            } elseif ($data_storage['wareHouseQty'] < $data_inv1[$i]['newQty']) {
                $newQty = $data_inv1[$i]['quantity'] + ($data_inv1[$i]['newQty']-$data_storage['wareHouseQty']);
            } else {
                $newQty = $data_inv1[$i]['quantity'];
            }
        } else {
            $newQty = $data_inv1[$i]['quantity']+$data_inv1[$i]['newQty'];
        }
        if($data_storage) {
            $query = "UPDATE \"tbl_invStorage\" SET ";
            $query .= " \"wareHouseQty\" = '" . $data_inv1[$i]['newQty'] . "' ";
            $query .= ",\"updatedBy\" = '" . $_SESSION['employeeID'] . "' ";
            $query .= ",\"updatedDate\" = '" . date('U') . "' ";
            $query .= "  where \"storageId\"='" . $data_storage['storageId'] . "' ";

            //Log Tracking
            $sql = '';
            $sql = "INSERT INTO \"audit_logs\" (";
            $sql .= " \"inventory_id\", \"employee_id\", \"updated_time\",";
            $sql .= " \"log\") VALUES (";
            $sql .= "'".$data_inv1[$i]['inventoryId']."'";
            $sql .= ", '". $_SESSION['employeeID'] ."'";
            $sql .= ", '". date('U') ."'";
            $sql .= ", 'Edit Inventory Quantity form ".$data_storage['wareHouseQty']." to ". $data_inv1[$i]['newQty'] ." '";
            $sql .= ")";
        } else {
            $query = "INSERT INTO \"tbl_invStorage\" (";
            $query .= " \"invId\" ";
            $query .= " ,\"styleId\" ";
            $query .= " ,\"colorId\" ";
            if ($data_inv1[$i]['sizeScaleId'] != "") $query .= " ,\"sizeScaleId\" ";
            if ($data_inv1[$i]['opt1ScaleId'] != "") $query .= " ,\"opt1ScaleId\" ";
            if ($data_inv1[$i]['opt2ScaleId'] != "") $query .= " ,\"opt2ScaleId\" ";
            $query .= " ,\"locationId\" ";
            if ($room != "") $query .= " ,\"room\" ";
            if ($wareHouseRow != "") $query .= " ,\"row\" ";
            if ($rack != "") $query .= " ,\"rack\" ";
            if ($shelf != "") $query .= " ,\"shelf\" ";
            if ($unit != "") $query .= " ,\"unit\" ";
            $query .= " ,\"wareHouseQty\" ";
            $query .= " ,\"createdBy\" ";
            $query .= " ,\"updatedBy\" ";
            $query .= " ,\"createdDate\" ";
            $query .= " ,\"updatedDate\" ";
            $query .= ")";
            $query .= " VALUES (";
            $query .= " '" . $data_inv1[$i]['inventoryId'] . "' ";
            $query .= " ,'" . $styleId . "' ";
            $query .= " ,'" . $colorId . "' ";
            if ($data_inv1[$i]['sizeScaleId'] != "") $query .= " ,'" . $data_inv1[$i]['sizeScaleId'] . "' ";
            if ($data_inv1[$i]['opt1ScaleId'] != "") $query .= " ,'" . $data_inv1[$i]['opt1ScaleId'] . "' ";
            if ($data_inv1[$i]['opt2ScaleId'] != "") $query .= " ,'" . $data_inv1[$i]['opt2ScaleId'] . "' ";
            $query .= " ,'" . $locId . "' ";
            if ($room != "") $query .= " ,'" . $room . "' ";
            if ($wareHouseRow != "") $query .= " ,'" . $wareHouseRow . "' ";
            if ($rack != "") $query .= " ,'" . $rack . "' ";
            if ($shelf != "") $query .= " ,'" . $shelf . "' ";
            if ($unit != "") $query .= " ,'" . $unit . "' ";
            $query .= " ,'" . $data_inv1[$i]['newQty'] . "' ";
            $query .= " ,'" . $_SESSION['employeeID'] . "' ";
            $query .= " ,'" . $_SESSION['employeeID'] . "' ";
            $query .= " ,'" . date('U') . "' ";
            $query .= " ,'" . date('U') . "' ";
            $query .= " )";

            //Log Tracking
            $sql = '';
            $sql = "INSERT INTO \"audit_logs\" (";
            $sql .= " \"inventory_id\", \"employee_id\", \"updated_time\",";
            $sql .= " \"log\") VALUES (";
            $sql .= "'".$data_inv1[$i]['inventoryId']."'";
            $sql .= ", '". $_SESSION['employeeID'] ."'";
            $sql .= ", '". date('U') ."'";
            $sql .= ", 'Edit Inventory Quantity form 0 to ". $data_inv1[$i]['newQty'] ." '";
            $sql .= ")";
        }
        if ($query != "") {
            $return_arr['type'] = "warehouse";
            if ($data_inv1[$i]['quantity'] != "" && $data_inv1[$i]['quantity'] > 0)
                $qty += $data_inv1[$i]['quantity'];
            if (!($result = pg_query($connection, $query))) {
                $return_arr['error'] = pg_last_error($connection);
                echo json_encode($return_arr);
                return;
            }
            pg_free_result($result);
            $query = "";
        }
        if($sql != '') {
            if(!($audit = pg_query($connection,$sql))){
                $return_arr['error'] = pg_last_error($connection);
                echo json_encode($return_arr);
                return;
            }
            pg_free_result($result);
        }
            $query = '';
            $query = "UPDATE \"tbl_inventory\" SET ";
            $query .= "\"quantity\" = '" . $newQty . "' ";
            $query .= ",\"newQty\" = '0' ";
            $query .= ",\"isStorage\" = 1 ";
            $query .= ",\"updatedBy\" = '" . $_SESSION['employeeID'] . "' ";
            $query .= ",\"updatedDate\" = '" . date('U') . "' ";
            $query .= "  where \"inventoryId\"='" . $data_inv1[$i]['inventoryId'] . "' ";
            if (!($result = pg_query($connection, $query))) {
                $return_arr['error'] = pg_last_error($connection);
                echo json_encode($return_arr);
                return;
            }
            pg_free_result($result);
            $query = "";
    } else {
        $return_arr['name'] = "Storage information is already updated...";
    }
}
echo json_encode($return_arr);
exit;
?>