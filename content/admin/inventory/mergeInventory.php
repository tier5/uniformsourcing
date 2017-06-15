<?php
require('Application.php');
if($_POST['styleId'] != '' && $_POST['unit'] != '' && $_POST['newUnit'] != ''){
    extract($_POST);
    if (isset($styleId)) {

        $styleId = $styleId;
        $search = "";
        if (isset($colorId)) {
            $clrId = $colorId;
        } else {
            $clrId = 0;
        }
        if ($clrId > 0) {
            $search = " and inv.\"colorId\"=$clrId ";
        }
        $searchUnit = "";
        if (isset($unit) && $unit != '0') {
            $searchUnit .= " and st.\"unit\"='" . $unit . "'";
        }
        $searchUnitNew = "";
        if (isset($newUnit) && $newUnit != '0') {
            $searchUnitNew .= " and st.\"unit\"='" . $newUnit . "'";
        }
    }
    $sql = 'select "styleId","sex","garmentId","barcode", "styleNumber", "scaleNameId", price, "locationIds", "clientId" from "tbl_invStyle" where "styleId"=' . $styleId;
    if (!($result = pg_query($connection, $sql))) {
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit;
    }
    $row = pg_fetch_array($result);
    $data_style = $row;
    pg_free_result($result);
    $query2 = 'Select * from "tbl_invColor" where "styleId"=' . $data_style['styleId'];
    if (!($result2 = pg_query($connection, $query2))) {
        print("Failed OptionQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row2 = pg_fetch_array($result2)) {
        $data_color[] = $row2;
    }
    pg_free_result($result2);
    if ($data_style['scaleNameId'] != "") {
        $query2 = 'Select * from "tbl_invScaleName" where "scaleId"=' . $data_style['scaleNameId'];
        if (!($result = pg_query($connection, $query2))) {
            print("Failed OptionQuery: " . pg_last_error($connection));
            exit;
        }
        $row = pg_fetch_array($result);
        $data_optionName = $row;
        pg_free_result($result);
        $query2 = 'Select "sizeScaleId" as "mainSizeId", "scaleSize" from "tbl_invScaleSize" where "scaleId"=' . $data_style['scaleNameId'] . ' and "scaleSize" IS NOT NULL  and "scaleSize" <>\'\'  order by "mainOrder","sizeScaleId"';
        if (!($result2 = pg_query($connection, $query2))) {
            print("Failed OptionQuery: " . pg_last_error($connection));
            exit;
        }
        while ($row2 = pg_fetch_array($result2)) {
            $data_mainSize[] = $row2;
        }
        pg_free_result($result2);
        $query2 = 'Select "sizeScaleId" as "opt1SizeId", "opt1Size" from "tbl_invScaleSize" where "scaleId"=' . $data_style['scaleNameId'] . ' and "opt1Size" IS NOT NULL  and "opt1Size" <>\'\' order by "opt1Order","sizeScaleId"';
        if (!($result2 = pg_query($connection, $query2))) {
            print("Failed OptionQuery: " . pg_last_error($connection));
            exit;
        }
        while ($row2 = pg_fetch_array($result2)) {
            $data_opt1Size[] = $row2;
        }
        pg_free_result($result2);
        $query2 = 'Select "sizeScaleId" as "opt2SizeId", "opt2Size" from "tbl_invScaleSize" where "scaleId"=' . $data_style['scaleNameId'] . ' and "opt2Size" IS NOT NULL and "opt2Size" <>\'\' order by "opt2Order","sizeScaleId"';
        if (!($result2 = pg_query($connection, $query2))) {
            print("Failed OptionQuery: " . pg_last_error($connection));
            exit;
        }
        while ($row2 = pg_fetch_array($result2)) {
            $data_opt2Size[] = $row2;
        }
        pg_free_result($result2);
        $sql = 'select distinct unit from "tbl_invStorage" where "styleId"=' . $styleId;
        if ($colorId > 0) {
            $sql .= ' and "colorId"=' . $colorId;
        } else if (count($data_color) > 0) {
            $sql .= ' and "colorId"=' . $data_color[0]['colorId'];
        }
        $sql .= ' order by unit asc';
        if (!($result_cnt9 = pg_query($connection, $sql))) {
            print("Failed InvData: " . pg_last_error($connection));
            exit;
        }
        while ($row_cnt9 = pg_fetch_array($result_cnt9)) {
            $data_storage[] = $row_cnt9;
        }
        pg_free_result($result_cnt9);
    }
    $totalScale = count($data_mainSize);
    $tableWidth = 0;
    $tableWidth = $totalScale * 100;
    $sql = 'select "inventoryId",quantity,"newQty","isStorage","warehouse_id" from "tbl_inventory" where "styleId"=' . $styleId;
    if (!($result = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($result)) {
        $data_inv[] = $row;
    }
    if (count($data_color) > 0) {
        if ($search != "") {
            $query = 'select inv."inventoryId", inv."sizeScaleId", inv.price, inv."locationId",inv."opt1ScaleId", inv."opt2ScaleId"';
            if (isset($unit) && $unit != '0') {
                $query .= ',st."wareHouseQty" as st_quantity, st."storageId" ';
            }
            $query .= ', inv."updatedBy",inv."updatedDate",inv.quantity, inv."newQty" from "tbl_inventory" as inv ';
            if (isset($unit) && $unit != '0') {
                $query .= ' left join "tbl_invStorage" as st on st."invId"=inv."inventoryId" ';
            }
            $query .= ' where inv."styleId"=' . $data_style['styleId'] . ' and inv."isActive"=1 ' . $search .' '. $searchUnit .' order by "inventoryId"';
        } else {
            $clrId = $data_color[0]['colorId'];
            $query = 'select "updatedBy","updatedDate","inventoryId", "sizeScaleId", price, "locationId","opt1ScaleId", "opt2ScaleId", quantity, "newQty" from "tbl_inventory" where "styleId"=' . $data_style['styleId'] . ' and "colorId"=' . $data_color[0]['colorId'] . '  and "isActive"=1 order by "inventoryId"';
        }
        if (!($result = pg_query($connection, $query))) {
            print("Failed invQuery: " . pg_last_error($connection));
            exit;
        }
        while ($row = pg_fetch_array($result)) {
            $data_inv[] = $row;
            $data_inv_new[] = $row;
        }
    }
    pg_free_result($result);
    if (count($data_inv) > 0) {
        for ($l = 0; $l < count($data_inv); $l++) {
            if (isset($data_inv[$l]['st_quantity']) && $data_inv[$l]['st_quantity'] != '') {
                $data_inv[$l]['quantity'] = $data_inv[$l]['st_quantity'];
            }
        }
    }
    if (count($data_color) > 0) {
        if ($search != "") {
            $query = '';
            $query = 'select inv."inventoryId", inv."sizeScaleId", inv.price, inv."locationId",inv."opt1ScaleId", inv."opt2ScaleId"';
            if (isset($newUnit) && $newUnit != '0') {
                $query .= ',st."wareHouseQty" as st_quantity, st."storageId" ';
            }
            $query .= ', inv."updatedBy",inv."updatedDate",inv.quantity, inv."newQty" from "tbl_inventory" as inv ';
            if (isset($newUnit) && $newUnit != '0') {
                $query .= ' left join "tbl_invStorage" as st on st."invId"=inv."inventoryId" ';
            }
            $query .= ' where inv."styleId"=' . $data_style['styleId'] . ' and inv."isActive"=1 ' . $search .' '. $searchUnitNew . ' order by "inventoryId"';
        } else {
            $clrId = $data_color[0]['colorId'];
            $query = 'select "updatedBy","updatedDate","inventoryId", "sizeScaleId", price, "locationId","opt1ScaleId", "opt2ScaleId", quantity, "newQty" from "tbl_inventory" where "styleId"=' . $data_style['styleId'] . ' and "colorId"=' . $data_color[0]['colorId'] . '  and "isActive"=1 order by "inventoryId"';
        }
        if (!($result = pg_query($connection, $query))) {
            print("Failed invQuery: " . pg_last_error($connection));
            exit;
        }
        while ($row = pg_fetch_array($result)) {
            $data_inv_to[] = $row;
        }
    }
    pg_free_result($result);
    if (count($data_inv_to) > 0) {
        for ($l = 0; $l < count($data_inv_to); $l++) {
            if (isset($data_inv_to[$l]['st_quantity']) && $data_inv_to[$l]['st_quantity'] != '') {
                $data_inv_to[$l]['quantity'] = $data_inv_to[$l]['st_quantity'];
            }
        }
    }
    $opt1SizeIdHash = array();
    foreach ($data_opt1Size as $key => $val) {
        if (isset($val['opt1SizeId'])) {
            $opt1SizeIdHash[(int)$val['opt1SizeId']] = $val['opt1Size'];
        }
    }
    $data_mainSizeIdHash = array();
    foreach ($data_mainSize as $key => $val){
        if (isset($val['mainSizeId'])){
            $data_mainSizeIdHash[(int)$val['mainSizeId']] = $val['scaleSize'];
        }
    }
    $data_set = array();
    $data_set_price = array();
    $d_i = 0;
    $d_j = 0;
    foreach ($data_inv as $key => $val) {
        if ( isset($opt1SizeIdHash[$val['opt1ScaleId']])
            && isset($data_mainSizeIdHash[$val['sizeScaleId']])
        ) {
            $data_set[$val['sizeScaleId']][$val['opt1ScaleId']] = $val['quantity'];
            $data_storageId[$val['sizeScaleId']][$val['opt1ScaleId']] = $val['storageId'];
            $data_invNew[$val['sizeScaleId']][$val['opt1ScaleId']] = $val['inventoryId'];
            $data_set_price[$val['sizeScaleId']] = isset($val['price']) ? $val['price'] : null;
        }else if ( !isset($opt1SizeIdHash[$val['opt1ScaleId']])
            && isset($data_mainSizeIdHash[$val['sizeScaleId']])
        ) {
            $data_set[$val['sizeScaleId']][0] = $val['quantity'];
            $data_storageId[$val['sizeScaleId']][0] = $val['storageId'];
            $data_invNew[$val['sizeScaleId']][0] = $val['inventoryId'];
            $data_set_price[$val['sizeScaleId']] = isset($val['price']) ? $val['price'] : null;
        }
    }
    foreach ($data_inv_to as $key => $val) {
        if ( isset($opt1SizeIdHash[$val['opt1ScaleId']])
            && isset($data_mainSizeIdHash[$val['sizeScaleId']])
        ) {
            $data_set_to[$val['sizeScaleId']][$val['opt1ScaleId']] = $val['quantity'];
            $data_storageId_to[$val['sizeScaleId']][$val['opt1ScaleId']] = $val['storageId'];
            $data_invNew_to[$val['sizeScaleId']][$val['opt1ScaleId']] = $val['inventoryId'];
        }else if ( !isset($opt1SizeIdHash[$val['opt1ScaleId']])
            && isset($data_mainSizeIdHash[$val['sizeScaleId']])
        ) {
            $data_set_to[$val['sizeScaleId']][0] = $val['quantity'];
            $data_storageId_to[$val['sizeScaleId']][0] = $val['storageId'];
            $data_invNew_to[$val['sizeScaleId']][0] = $val['inventoryId'];
        }
    }
    $sql1='SELECT "locationId",row,rack,shelf FROM "tbl_invStorage" where unit='."'".$newUnit."'";
    if (!($result = pg_query($connection, $sql1))) {
        print("Failed Data_invQuery: " . pg_last_error($connection));
        exit;
    }
    $row = pg_fetch_array($result);
    $locId = $row;
    $row1 = '';
    $rack = '';
    $shelf = '';
    if($locId != ''){
        $locationId = $locId['locationId'];
        $row1 = $locId['row'];
        $rack = $locId['rack'];
        $shelf = $locId['shelf'];
    }
    foreach ($data_mainSizeIdHash as $key1 => $val1) {
        if(count($opt1SizeIdHash) > 0){
            foreach ($opt1SizeIdHash as $key2 => $val2) {
                if(isset($data_set[$key1][$key2]) && $data_set[$key1][$key2] > 0){
                    if(isset($data_storageId_to[$key1][$key2])){
                        $addition = $data_set[$key1][$key2]+$data_set_to[$key1][$key2];
                        $query = '';
                        $query = "UPDATE \"tbl_invStorage\" SET ";
                        $query .= " \"wareHouseQty\" = '" . $addition . "' ";
                        $query .=",\"oldinv\" ='".$data_set_to[$key1][$key2]."'";
                        $query .= ",\"updatedBy\" = '" . $_SESSION['employeeID'] . "' ";
                        $query .= ",\"updatedDate\" = '" . date('U') . "' ";
                        $query .= "  where \"storageId\"='" . $data_storageId_to[$key1][$key2] . "' ";
                        if (!($result = pg_query($connection, $query))) {
                            $return_arr['error'] = pg_last_error($connection);
                            echo json_encode($return_arr);
                            return;
                        }
                        pg_free_result($result);
                    } else {
                        $query = '';
                        $query = "INSERT INTO \"tbl_invStorage\" (";
                        $query .= " \"invId\" ";
                        $query .= " ,\"styleId\" ";
                        $query .= " ,\"colorId\" ";
                        $query .= " ,\"sizeScaleId\" ";
                        $query .= " ,\"opt1ScaleId\" ";
                        $query .= " ,\"locationId\" ";
                        if ($row1!= "") $query .= " ,\"row\" ";
                        if ($rack != "") $query .= " ,\"rack\" ";
                        if ($shelf != "") $query .= " ,\"shelf\" ";
                        if ($newUnit != "") $query .= " ,\"unit\" ";
                        $query .= " ,\"wareHouseQty\" ";
                        $query .= " ,\"createdBy\" ";
                        $query .= " ,\"updatedBy\" ";
                        $query .= " ,\"createdDate\" ";
                        $query .= " ,\"updatedDate\" ";
                        $query .= " ,\"oldinv\" ";
                        $query .= ")";
                        $query .= " VALUES (";
                        $query .= " '" . $data_invNew[$key1][$key2] . "' ";
                        $query .= " ,'" . $styleId . "' ";
                        $query .= " ,'" . $colorId . "' ";
                            $query .= " ,'" . $key1 . "' ";
                        $query .= " ,'" . $key2 . "' ";
                        $query .= " ,'" . $locationId . "' ";
                        if ($row1 != "") $query .= " ,'" .$row1. "' ";
                        if ($rack != "") $query .= " ,'" . $rack . "' ";
                        if ($shelf != "") $query .= " ,'" . $shelf . "' ";
                        if ($newUnit != "") $query .= " ,'" . $newUnit . "' ";
                        $query .= " ,'" . $data_set[$key1][$key2] . "' ";
                        $query .= " ,'" . $_SESSION['employeeID'] . "' ";
                        $query .= " ,'" . $_SESSION['employeeID'] . "' ";
                        $query .= " ,'" . date('U') . "' ";
                        $query .= " ,'" . date('U') . "' ";
                        $query .= " ,'0' ";
                        $query .= " )";
                        if (!($result = pg_query($connection, $query))) {
                            $return_arr['error'] = pg_last_error($connection);
                            echo json_encode($return_arr);
                            return;
                        }
                        pg_free_result($result);
                    }
                }
                if(isset($data_storageId[$key1][$key2])){
                    $sql = '';
                    $sql = "UPDATE \"tbl_invStorage\" SET ";
                    $sql .= " \"wareHouseQty\" = '0' ";
                    $sql .=",\"oldinv\" ='".$data_set[$key1][$key2]."'";
                    $sql .=",\"merged\"='1'";
                    $sql .= ",\"updatedBy\" = '" . $_SESSION['employeeID'] . "' ";
                    $sql .= ",\"updatedDate\" = '" . date('U') . "' ";
                    $sql .= "  where \"storageId\"='" . $data_storageId[$key1][$key2] . "' ";
                    if (!($result = pg_query($connection, $sql))) {
                        $return_arr['error'] = pg_last_error($connection);
                        echo json_encode($return_arr);
                        return;
                    }
                    pg_free_result($result);
                }
            }
        } else {
            if(isset($data_set[$key1][0]) && $data_set[$key1][0] > 0){
                if(isset($data_storageId_to[$key1][0])){
                    $addition = $data_set[$key1][0]+$data_set_to[$key1][0];
                    $query = '';
                    $query = "UPDATE \"tbl_invStorage\" SET ";
                    $query .= " \"wareHouseQty\" = '" . $addition . "' ";
                    $query .=",\"oldinv\" ='".$data_set_to[$key1][0]."'";
                    $query .= ",\"updatedBy\" = '" . $_SESSION['employeeID'] . "' ";
                    $query .= ",\"updatedDate\" = '" . date('U') . "' ";
                    $query .= "  where \"storageId\"='" . $data_storageId_to[$key1][0] . "' ";
                    if (!($result = pg_query($connection, $query))) {
                        $return_arr['error'] = pg_last_error($connection);
                        echo json_encode($return_arr);
                        return;
                    }
                    pg_free_result($result);
                } else {
                    $query = '';
                    $query = "INSERT INTO \"tbl_invStorage\" (";
                    $query .= " \"invId\" ";
                    $query .= " ,\"styleId\" ";
                    $query .= " ,\"colorId\" ";
                    $query .= " ,\"sizeScaleId\" ";
                    $query .= " ,\"opt1ScaleId\" ";
                    $query .= " ,\"locationId\" ";
                    if ($row1!= "") $query .= " ,\"row\" ";
                    if ($rack != "") $query .= " ,\"rack\" ";
                    if ($shelf != "") $query .= " ,\"shelf\" ";
                    if ($newUnit != "") $query .= " ,\"unit\" ";
                    $query .= " ,\"wareHouseQty\" ";
                    $query .= " ,\"createdBy\" ";
                    $query .= " ,\"updatedBy\" ";
                    $query .= " ,\"createdDate\" ";
                    $query .= " ,\"updatedDate\" ";
                    $query .= " ,\"oldinv\" ";
                    $query .= ")";
                    $query .= " VALUES (";
                    $query .= " '" . $data_invNew[$key1][0] . "' ";
                    $query .= " ,'" . $styleId . "' ";
                    $query .= " ,'" . $colorId . "' ";
                    $query .= " ,'" . $key1 . "' ";
                    $query .= " ,'" . $key2 . "' ";
                    $query .= " ,'" . $locId . "' ";
                    if ($row1 != "") $query .= " ,'" .$row1. "' ";
                    if ($rack != "") $query .= " ,'" . $rack . "' ";
                    if ($shelf != "") $query .= " ,'" . $shelf . "' ";
                    if ($newUnit != "") $query .= " ,'" . $newUnit . "' ";
                    $query .= " ,'" . $data_set[$key1][0] . "' ";
                    $query .= " ,'" . $_SESSION['employeeID'] . "' ";
                    $query .= " ,'" . $_SESSION['employeeID'] . "' ";
                    $query .= " ,'" . date('U') . "' ";
                    $query .= " ,'" . date('U') . "' ";
                    $query .= " ,'0' ";
                    $query .= " )";
                    if (!($result = pg_query($connection, $query))) {
                        $return_arr['error'] = pg_last_error($connection);
                        echo json_encode($return_arr);
                        return;
                    }
                    pg_free_result($result);
                }
            }
            if(isset($data_storageId[$key1][0])) {
                $sql = '';
                $sql = "UPDATE \"tbl_invStorage\" SET ";
                $sql .= " \"wareHouseQty\" = '0' ";
                $sql .=",\"oldinv\" ='".$data_set[$key1][0]."'";
                $sql .=",\"merged\"='1'";
                $sql .= ",\"updatedBy\" = '" . $_SESSION['employeeID'] . "' ";
                $sql .= ",\"updatedDate\" = '" . date('U') . "' ";
                $sql .= "  where \"storageId\"='" . $data_storageId[$key1][0] . "' ";
                if (!($result = pg_query($connection, $sql))) {
                    $return_arr['error'] = pg_last_error($connection);
                    echo json_encode($return_arr);
                    return;
                }
                pg_free_result($result);
            }
        }
    }
    echo 1;
    return;
} else {
    echo 'Please Enter all Details';
    return;
}
?>