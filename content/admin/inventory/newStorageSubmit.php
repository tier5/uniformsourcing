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
$box = $_GET['boxId'];
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
for ($i=0;$i<$totalRow;$i++) {
    if (isset($data_inv1[$i]['inventoryId'])) {
        $sql = 'select "inventoryId", "styleId", "colorId", "sizeScaleId", price, "locationId","opt1ScaleId", "opt2ScaleId", quantity, "mainSize", "rowSize", "newQty" from "tbl_inventory" where "inventoryId"=' . $data_inv1[$i]['inventoryId'] . ' and "isStorage"=0';
        if (!($result = pg_query($connection, $sql))) {
            print("Failed StorageData: " . pg_last_error($connection));
            exit;
        }
        $row = pg_fetch_array($result);
        $data_inv = $row;
        pg_free_result($result);
        if ($data_inv['inventoryId'] != "") {
            $sql = 'select "storageId", "invId", "sizeScaleId", "opt1ScaleId", "opt2ScaleId", "locationId", "conveyorSlotId", "conveyorQty", room, "row", rack, shelf, box, "wareHouseQty", "location", "otherQty" from "tbl_invStorage" where "invId"=' . $data_inv1[$i]['inventoryId'] . ' order by "storageId"';

            if (!($result = pg_query($connection, $sql))) {
                print("Failed Data_invQuery: " . pg_last_error($connection));
                exit;
            }
            while ($row = pg_fetch_array($result)) {
                $data_storage[] = $row;
            }
            pg_free_result($result);
            $totalStorageQty = 0;
            $totalConveyorQty = 0;
            $totalWarehouseQty = 0;
            $totalOtherQty = 0;
            for ($j = 0; $j < count($data_storage); $j++) {
                if ($data_storage[$j]['conveyorQty'] != "" && $data_storage[$j]['conveyorQty'] > 0)
                    $totalConveyorQty += $data_storage[$j]['conveyorQty'];
                if ($data_storage[$j]['wareHouseQty'] != "" && $data_storage[$j]['wareHouseQty'] > 0)
                    $totalWarehouseQty += $data_storage[$j]['wareHouseQty'];
                if ($data_storage[$j]['otherQty'] != "" && $data_storage[$j]['otherQty'] > 0)
                    $totalOtherQty += $data_storage[$j]['otherQty'];
            }
            $totalStorageQty = $totalConveyorQty + $totalWarehouseQty + $totalOtherQty;
            $found = 0;
            $query = "";
            $j = 0;
            if ($box == "") {
                $return_arr['error'] = "Please fill Box number before sumbimiting.";
                echo json_encode($return_arr);
                return;
            }
            if($data_inv['quantity'] != "" && $data_inv['quantity'] > 0)
                $qty = $data_inv['newQty'] - $data_inv['quantity'];
            else
                $qty = $data_inv['newQty'];
            for (;$j<count($data_storage);$j++) {
                if ($data_storage[$j]['wareHouseQty'] == "" || $data_storage[$j]['wareHouseQty'] == 0) {
                    $found = 1;
                    break;
                }
            }
            if ($found) {
                $query = "UPDATE \"tbl_invStorage\" SET ";
                $query .= "\"room\" = '" . $room . "' ";
                $query .= ",\"row\" = '" . $wareHouseRow . "' ";
                $query .= ",\"rack\" = '" . $rack . "' ";
                $query .= ",\"shelf\" = '" . $shelf . "' ";
                $query .= ",\"box\" = '" . $box . "' ";
                $query .= ",\"wareHouseQty\" = '" . $data_inv1[0]['newQty'] . "' ";
                $query .= ",\"updatedBy\" = '" . $_SESSION['employeeID'] . "' ";
                $query .= ",\"updatedDate\" = '" . date('U') . "' ";
                $query .= "  where \"storageId\"='" . $data_storage[$j]['storageId'] . "' ";
                $j++;
            } else {
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
                $query .= " ,'" . $data_inv1[0]['newQty'] . "' ";
                $query .= " ,'" . $_SESSION['employeeID'] . "' ";
                $query .= " ,'" . $_SESSION['employeeID'] . "' ";
                $query .= " ,'" . date('U') . "' ";
                $query .= " ,'" . date('U') . "' ";
                $query .= " )";
            }
            if ($query != "") {
                $return_arr['type'] = "warehouse";
                if ($data_inv['quantity'] != "" && $data_inv['quantity'] > 0)
                    $qty += $data_inv['quantity'];
                if (!($result = pg_query($connection, $query))) {
                    $return_arr['error'] = pg_last_error($connection);
                    echo json_encode($return_arr);
                    return;
                }
                pg_free_result($result);
                $query = "";
            }
            if ($qty > 0) {
                $query = "UPDATE \"tbl_inventory\" SET ";
                $query .= "\"quantity\" = '" . $qty . "' ";
                $query .= ",\"newQty\" = '0' ";
                $query .= ",\"isStorage\" = 1 ";
                $query .= ",\"updatedBy\" = '" . $_SESSION['employeeID'] . "' ";
                $query .= ",\"updatedDate\" = '" . date('U') . "' ";
                $query .= "  where \"inventoryId\"='" . $data_inv['inventoryId'] . "' ";
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
    }
}
echo json_encode($return_arr);
exit;
?>