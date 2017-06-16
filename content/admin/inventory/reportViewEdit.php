<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<style>
    .container{width:95%;}
     .tool {
         position: relative;
         display: inline-block;
     }
    .tooltext {
        /*visibility: hidden;*/
        display: none;
        width: 120px;
        background-color: black;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px 0;

        /* Position the tooltip */
        position: absolute;
        z-index: 1;
    }
    .tool:hover .tooltext {
        visibility: visible;
    }
</style>
<?php
    require('Application.php');
    require('../../header.php');
    function locationDetails($locId,$connection){
    $sql = 'SELECT * from "tbl_invLocation" WHERE "locationId"='.$locId;
    if (!($result2 = pg_query($connection, $sql))) {
        print("Failed OptionQuery: " . pg_last_error($connection));
        exit;
    }
    $row2 = pg_fetch_array($result2);
    $data_locationFun = $row2;
    $sql = '';
    $sql = "SELECT * from \"locationDetails\" where \"warehouse\" is not null and  \"locationId\"='".$locId."'";
    if (!($result2 = pg_query($connection, $sql))) {
        print("Failed OptionQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row2 = pg_fetch_array($result2)) {
        $data[] = $data_locationFun['identifier'].'_'.$row2['warehouse'];
    }

    $sql = '';
    $sql = "SELECT * from \"locationDetails\" where \"container\" is not null and \"locationId\"='".$locId."'";
    if (!($result2 = pg_query($connection, $sql))) {
        print("Failed OptionQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row2 = pg_fetch_array($result2)) {
        $data[] = $data_locationFun['identifier'].'_'.$row2['container'];
    }
    $sql = '';
    $sql = "SELECT * from \"locationDetails\" where \"conveyor\" is not null and \"locationId\"='".$locId."'";
    if (!($result2 = pg_query($connection, $sql))) {
        print("Failed OptionQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row2 = pg_fetch_array($result2)) {
        $data[] = $data_locationFun['identifier'].'_'.$row2['conveyor'];
    }
    return $data;
}
    function logCheckOStyle($styleId,$connection)
{
    $sql = '';
    $sql = 'select * from "tbl_invScaleSize" where "sizeScaleId" =' . $styleId . ' LIMIT 1';
    if (!($resultoldinv = pg_query($connection, $sql))) {
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit;
    }
    $rowoldinv = pg_fetch_row($resultoldinv);
    $oldinv = $rowoldinv;
    pg_free_result($resultoldinv);
    echo $oldinv['2'];
}
    function logCheckNStyle($styleId,$connection)
{
    $sql = '';
    $sql = 'select * from "tbl_invScaleSize" where "sizeScaleId" =' . $styleId . ' LIMIT 1';
    if (!($resultoldinv = pg_query($connection, $sql))) {
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit;
    }
    $rowoldinv = pg_fetch_row($resultoldinv);
    $oldinv = $rowoldinv;
    pg_free_result($resultoldinv);
    echo $oldinv['3'];
}
    $loc_identity = 0;
    if (isset($_GET["del"]) && $_GET["del"] == "true") {
    $sql = 'select "inventoryId" from "tbl_inventory" as inv where inv."styleId"=' . $_GET['styleId'] . ' and inv."colorId"=' . $_GET['colorId'];
    if (!($result2 = pg_query($connection, $sql))) {
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row2 = pg_fetch_array($result2)) {
        $sql = 'delete  from "tbl_invStorage"  where "invId"=' . $row2["inventoryId"];
        //$sql .=';update "tbl_inventory" set quantity=0 where "inventoryId"='.$row2["inventoryId"];
        $sql .= ';delete from "tbl_inventory" where "inventoryId"=' . $row2["inventoryId"];
        //echo $sql;
        if (!($result = pg_query($connection, $sql))) {
            print("Failed StyleQuery: " . pg_last_error($connection));
            exit;
        }
        pg_free_result($result);
    }
    pg_free_result($result2);
    unset($row2);
    $sp = split("&del", $_SERVER['REQUEST_URI']); ?>
    <script type="text/javascript">location.href = "<?php echo $sp[0];?>"</script>
    <?php
}
    $search = "";
    if (isset($_GET['styleId'])) {

    $styleId = $_GET['styleId'];
    $search = "";
    if (isset($_GET['colorId'])) {
        $clrId = $_GET['colorId'];
        $opt1Id = $_GET['opt1Id'];
        $opt2Id = $_GET['opt2Id'];
    } else {
        $clrId = 0;
        $opt1Id = 0;
        $opt2Id = 0;
    }
    if ($clrId > 0) {
        $search = " and inv.\"colorId\"=$clrId ";
        if ($opt1Id > 0)
            $search .= "and \"opt1ScaleId\"=$opt1Id ";
        if ($opt2Id > 0)
            $search .= "and \"opt2ScaleId\"=$opt2Id ";
    }

    if (isset($_GET['unitId']) && $_GET['unitId'] != '0') {
        $search .= " and st.\"unit\"='" . $_GET['unitId'] . "'";
    }
    if(isset($_GET['location']) && $_GET['location'] != '0'){
        $search .= " and sti.unit LIKE '".$_GET['location']."%'";
    }
}
    $sql = 'select "styleId","sex","garmentId","barcode", "styleNumber", "scaleNameId", price, "locationIds", "clientId" from "tbl_invStyle" where "styleId"=' . $styleId;
    if (!($result = pg_query($connection, $sql))) {
    print("Failed StyleQuery: " . pg_last_error($connection));
    exit;
}
    $row = pg_fetch_array($result);
    $data_style = $row; //--------------------------- data style----------------
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
    $sql = 'select distinct unit from "tbl_invStorage" where "styleId"=' . $_GET['styleId']." and merged = '0'";
    if ($_GET['colorId'] > 0) {
        $sql .= ' and "colorId"=' . $_GET['colorId'];
    } else if (count($data_color) > 0) {
        $sql .= ' and "colorId"=' . $data_color[0]['colorId'];
    }
    if(isset($_GET['location']) && $_GET['location'] != '0'){
        $sql .= " and unit LIKE '".$_GET['location']."%'";
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
    $sql = 'select "inventoryId",quantity,"newQty","isStorage","warehouse_id" from "tbl_inventory" where "styleId"=' . $_GET['styleId'];
    if (!($result = pg_query($connection, $sql))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
    while ($row = pg_fetch_array($result)) {
    $data_inv[] = $row;
}
    for ($i = 0; $i < count($data_inv); $i++) {
    if ($data_inv[$i]['newQty'] > 0) {
        if (($data_inv[$i]['quantity'] != "" && $data_inv[$i]['quantity'] > 0)) {
            $sql = 'update "tbl_inventory" set "isStorage"=1 ,"newQty"=0';
            if (!($result = pg_query($connection, $sql))) {
                print("Failed invUpdateQuery: " . pg_last_error($connection));
                exit;
            }
        } else if (($data_inv[$i]['quantity'] == "" || $data_inv[$i]['quantity'] == 0)) {
            $sql = 'Delete from "tbl_inventory" where "inventoryId"=' . $data_inv[$i]['inventoryId'];
            if (!($result = pg_query($connection, $sql))) {
                print("Failed deleteInvQuery: " . pg_last_error($connection));
                exit;
            }
        }
    }
}
    if (count($data_color) > 0) {
    if ($search != "") {
        $query = 'select inv."inventoryId", inv."sizeScaleId", inv.price, inv."locationId",inv."opt1ScaleId", inv."opt2ScaleId"';
        if (isset($_GET['unitId']) && $_GET['unitId'] != '0') {
            $query .= ',st."wareHouseQty" as st_quantity ';
        }
        if(isset($_GET['location']) && $_GET['location'] != '0'){
            $query .= ',sti."wareHouseQty" as sti_quantity ';
        }
        $query .= ', inv."updatedBy",inv."updatedDate",inv.quantity, inv."newQty" from "tbl_inventory" as inv ';
        if (isset($_GET['unitId']) && $_GET['unitId'] != '0') {
            $query .= ' left join "tbl_invStorage" as st on st."invId"=inv."inventoryId" ';
        }
        if(isset($_GET['location']) && $_GET['location'] != '0'){
            $query .=" left join \"tbl_invStorage\" as sti on sti.\"invId\"=inv.\"inventoryId\"";
        }
        $query .= ' where inv."styleId"=' . $data_style['styleId'] . ' and inv."isActive"=1 '.$search.' order by "inventoryId"';
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
    pg_free_result($result);
    if (count($data_inv) > 0) {
        for ($l = 0; $l < count($data_inv); $l++) {
            if (isset($data_inv[$l]['st_quantity']) && $data_inv[$l]['st_quantity'] != '') {
                $data_inv[$l]['quantity'] = $data_inv[$l]['st_quantity'];
            }
            if (isset($data_inv[$l]['sti_quantity']) && $data_inv[$l]['sti_quantity'] != '') {
                $data_inv[$l]['quantity'] = $data_inv[$l]['sti_quantity'];
            }
        }
    }
    $sql = 'select distinct warehouse , "locationId" from "locationDetails" where warehouse != \'null\'';
    $all_location_inv;
    if (!($result = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($result)) {
        $all_location_inv[] = $row;
    }
    $location_string = " ";
    foreach ($all_location_inv as $key => $value) {
        if ($location_string == " ")
            $location_string .= $value['locationId'];
        else
            $location_string .= ',' . $value['locationId'];
    }
    $sql = 'select name,"locationId" from "tbl_invLocation" where "locationId" in (' . $location_string . ') order by "locationId"';
    $warehouse_info;
    if (!($result = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($result)) {
        $warehouse_info[] = $row;
    }
    $sql = 'select distinct container , "locationId" from "locationDetails" where container != \'null\'';
    $containers;
    if (!($result = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($result)) {
        $containers[] = $row;
    }
    $location_string = " ";
    foreach ($containers as $key => $value) {
        if ($location_string == " ")
            $location_string .= $value['locationId'];
        else
            $location_string .= ',' . $value['locationId'];
    }
    $sql = 'select name,"locationId" from "tbl_invLocation" where "locationId" in (' . $location_string . ') order by "locationId"';
    $containers_location;
    if (!($result = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($result)) {
        $containers_location[] = $row;
    }
    $sql = 'select distinct conveyor , "locationId" from "locationDetails" where conveyor != \'null\'';
    $conveyors;
    if (!($result = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($result)) {
        $conveyors[] = $row;
    }
    $location_string = " ";
    foreach ($conveyors as $key => $value) {
        if ($location_string == " ")
            $location_string .= $value['locationId'];
        else
            $location_string .= ',' . $value['locationId'];
    }
    $sql = 'select name,"locationId" from "tbl_invLocation" where "locationId" in (' . $location_string . ') order by "locationId"';
    $conveyors_location;
    if (!($result = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($result)) {
        $conveyors_location[] = $row;
    }
}
    $query = 'select * from "tbl_invLocation" order by "locationId"';
    if (!($result = pg_query($connection, $query))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
    while ($row = pg_fetch_array($result)) {
    $data_loc[] = $row;
}
    pg_free_result($result);
    $locArr = array();
    if (isset($_GET['unitId']) && $_GET['unitId'] != '0') {
    $sql = 'select "locationId" from "tbl_invStorage" where unit = \'' . $_GET['unitId'] . '\' LIMIT 1';
    if (!($result = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    $row = pg_fetch_array($result);
    $this_location[] = $row;
    pg_free_result($row);
    $locArr[0] = $this_location[0]['locationId'];
} else {
    if ($data_style['locationIds'] != "") {
        $locArr = explode(",", $data_style['locationIds']);
    }
}
    $sql = '';
    $sql = 'SELECT * FROM "tbl_invLocation" WHERE "locationId"=' . $locArr[0];
    if (!($resultClient = pg_query($connection, $sql))) {
    print("Failed StyleQuery: " . pg_last_error($connection));
    exit;
}
    $row = pg_fetch_array($resultClient);
    $data_LocationName = $row;
    $is_slot = false;
    if (!isset($_GET['unitId']) || $_GET['unitId'] != '0') {
    $temp = explode('_', $_GET['unitId']);
    if (isset($temp[1])) {
        $tmp = substr($temp[1], 0, 2);
        if ($tmp == 'CV' || substr($temp[1], 0, 1) == 'C') {
            $is_slot = true;
        }
    }
    $query = 'select * from "tbl_invStorage" WHERE unit=' . "'" . $_GET['unitId'] . "'";
    if (!($resultProduct = pg_query($connection, $query))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($rowProduct = pg_fetch_array($resultProduct)) {
        $data_product[] = $rowProduct;
    }
    pg_free_result($rowProduct);
}
    $sql = 'select distinct "unit" , "wareHouseQty" as "quantity",
           "opt1ScaleId",
           "sizeScaleId" as "mainSizeId",
           "invId","styleId" from "tbl_invStorage" where "styleId"=' . $_GET['styleId'] . ' ORDER BY unit';
    if (!($result = pg_query($connection, $sql))) {
    print("Failed StyleQuery: " . pg_last_error($connection));
    exit;
}
    while ($row = pg_fetch_array($result)) {
    $_store[] = $row; // -------------------------- data_color ---------
}
    $store_new = [];
    foreach ($_store as $key=>$value){
    if($value['quantity'] > 0){
        if(isset($store_new[$value['opt1ScaleId']][$value['mainSizeId']])) {
            $store_new[$value['opt1ScaleId']][$value['mainSizeId']] = $store_new[$value['opt1ScaleId']][$value['mainSizeId']].'<br>'.$value['unit'].' : '.$value['quantity'];
        } else {
            $store_new[$value['opt1ScaleId']][$value['mainSizeId']] = $value['unit'].' : '.$value['quantity'];
        }
    }
}
    $query = '';
    $query = "SELECT * from \"locationDetails\"";
    $query .= "  where \"locationId\"='" . $data_style['locationIds'] . "' ";
    if (!($resultProduct = pg_query($connection, $query))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
    while ($row = pg_fetch_array($resultProduct)) {
    $data_location[] = $row;
}
    pg_free_result($resultProduct);
    if (!isset($_GET['unitId']) || $_GET['unitId'] == '0' /*|| !isset($_GET['location']) || $_GET['location'] == '0'*/) {
    $bckup_data_inv = $data_inv;
    $hash = array();
    for ($i = 0; $i < count($data_inv); $i++) {
        //if(isset($data_inv[$i]['opt1ScaleId']) && isset($data_inv[$i]['sizeScaleId']))
        if (isset($data_inv[$i]['sizeScaleId'])) {
            $y = $data_inv[$i]['opt1ScaleId'] . '_' . $data_inv[$i]['sizeScaleId'];
            if (!isset($hash[$y])) {
                $hash[$y] = (int)$data_inv[$i]['quantity'];
                $data_inv[$i]['locationId'] = 1;
            } else {
                $hash[$y] = $hash[$y] + $data_inv[$i]['quantity'];
                $data_inv[$i]['locationId'] = 1;
            }
        }
    }
    for ($i = 0; $i < count($data_inv); $i++) {
        if (isset($data_inv[$i]['sizeScaleId'])) {

            $x = $data_inv[$i]['opt1ScaleId'] . '_' . $data_inv[$i]['sizeScaleId'];
            $data_inv[$i]['quantity'] = $hash[$x];
        }
    }
    $locArr = array();
    $locArr[0] = 1;
}
    $locHash = array_flip($locArr);
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
        if (isset($locHash[$val['locationId']])
            && isset($opt1SizeIdHash[$val['opt1ScaleId']])
            && isset($data_mainSizeIdHash[$val['sizeScaleId']])
        ) {
            $data_set[$val['sizeScaleId']][$val['opt1ScaleId']] = $val['quantity'];
            $data_invNew[$val['sizeScaleId']][$val['opt1ScaleId']] = $val['inventoryId'];
            $data_set_price[$val['sizeScaleId']] = isset($val['price']) ? $val['price'] : null;
        }else if (isset($locHash[$val['locationId']])
            && !isset($opt1SizeIdHash[$val['opt1ScaleId']])
            && isset($data_mainSizeIdHash[$val['sizeScaleId']])
        ) {
            $data_set[$val['sizeScaleId']][0] = $val['quantity'];
            $data_invNew[$val['sizeScaleId']][0] = $val['inventoryId'];
            $data_set_price[$val['sizeScaleId']] = isset($val['price']) ? $val['price'] : null;
        }
    }
    $sql = '';
    $sql = 'select * from "tbl_garment" where "garmentID"=' . $data_style["garmentId"];
    if (!($result = pg_query($connection, $sql))) {
    print("Failed StyleQuery: " . pg_last_error($connection));
    exit;
}
    $row = pg_fetch_array($result);
    $data_garment = $row;
    $sql = '';
    $sql = 'SELECT * FROM "clientDB" where "ID"=' . $data_style['clientId'];
    if (!($resultClient = pg_query($connection, $sql))) {
    print("Failed StyleQuery: " . pg_last_error($connection));
    exit;
}
    $row = pg_fetch_array($resultClient);
    $data_client = $row;
    $latest = 0;
    if (isset($_GET['unitId']) && $_GET['unitId'] != '0' ) {
    if(count($data_product) > 0){
        $key_product = 0;
        foreach ($data_product as $dk => $dv) {
            if ($dv['updatedDate'] > $latest) {
                $latest = $dv['updatedDate'];
                $key_product = $dk;
            }
        }
        $sql = '';
        $sql = 'SELECT * FROM "employeeDB" where "employeeID"=' . $data_product[$key_product]['updatedBy'];
        if (!($resultClient = pg_query($connection, $sql))) {
            print("Failed StyleQuery: " . pg_last_error($connection));
            exit;
        }
        $row = pg_fetch_array($resultClient);
        $data_employee = $row;
        $sql = '';
        $sql = "SELECT type from \"tbl_invStorage\" WHERE unit='" . $_GET['unitId'] . "'";
        if (!($resultClient = pg_query($connection, $sql))) {
            print("Failed StyleQuery: " . pg_last_error($connection));
            exit;
        }
        $row = pg_fetch_array($resultClient);
        $data_type = $row[0];
    } else {
        $data_employee = '';
        $data_type = 'All';
    }

} else {
    if(count($data_inv_new) > 0){
        $key_inv = 0;
        foreach ($data_inv_new as $ik => $iv) {
            if ($iv['updatedDate'] > $latest) {
                $latest = $iv['updatedDate'];
                $key_inv = $ik;
            }
        }
        $sql = '';
        $sql = 'SELECT * FROM "employeeDB" where "employeeID"=' . $data_inv_new[$key_inv]['updatedBy'];
        if (!($resultClient = pg_query($connection, $sql))) {
            print("Failed StyleQuery: " . pg_last_error($connection));
            exit;
        }
        $row = pg_fetch_array($resultClient);
        $data_employee = $row;
        $data_type = 'All';
    } else {
        $data_employee = '';
        $data_type = 'All';
    }
}
    $sql = '';
    $sql = 'SELECT "locationId" FROM "tbl_invStorage" WHERE "styleId"='.$data_style['styleId'];
    if (!($result = pg_query($connection, $sql))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
    while ($row = pg_fetch_array($result)) {
    $data_locDrop[] = $row;
}
    $userdupe=array();
    foreach ($data_locDrop as $index=>$t) {
    if (isset($userdupe[$t["locationId"]])) {
        unset($data_locDrop[$index]);
        continue;
    }
    $userdupe[$t["locationId"]]=true;
}
    foreach ($data_locDrop as $keyDrop=>$valueDrop){
    $loc_d[] = locationDetails($valueDrop['locationId'],$connection);
}
    $arr_location = call_user_func_array('array_merge', $loc_d);
    $sql = '';
    $sql = 'SELECT * FROM "locationDetails"';
    if (!($result = pg_query($connection, $sql))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
    while ($row = pg_fetch_array($result)) {
    $data_all_loc[] = $row;
}
    $all_loc=array();
    foreach ($data_all_loc as $index=>$allL) {
    if (isset($all_loc[$allL["locationId"]])) {
        unset($data_all_loc[$index]);
        continue;
    }
    $all_loc[$allL["locationId"]]=true;
}
    foreach ($data_all_loc as $keyAll=>$valueAll){
    $loc_all_new[] = locationDetails($valueAll['locationId'],$connection);
}
    $arr_all_location = call_user_func_array('array_merge', $loc_all_new);
    $newAllLocation = [];
    foreach ($arr_all_location as $arrAllLocationKey => $arrAlllocationValue){
        $exp = explode('_',$arrAlllocationValue);
        $sql = '';
        $sql = "SELECT \"locationId\" FROM \"tbl_invLocation\" WHERE identifier='".$exp[0]."'";
        if (!($result = pg_query($connection, $sql))) {
            print("Failed invQuery: " . pg_last_error($connection));
            exit;
        }
        $locId = pg_fetch_row($result);
        $sql = '';
        $sql = "SELECT * FROM \"locationDetails\" WHERE \"locationId\"='".$locId[0]."'";
        $sql .= " and ( warehouse='".$exp[1]."' OR container='".$exp[1]."' OR conveyor ='".$exp[1]."' )";
        if (!($result = pg_query($connection, $sql))) {
            print("Failed invQuery: " . pg_last_error($connection));
            exit;
        }
        $warehouses_all = pg_fetch_row($result);
        if($warehouses_all[4] != ''){
            $newAllLocation[$arrAllLocationKey]['location'] = $arrAlllocationValue;
            $newAllLocation[$arrAllLocationKey]['type'] = 'conveyor';
        } elseif ($warehouses_all[3] != ''){
            $newAllLocation[$arrAllLocationKey]['location'] = $arrAlllocationValue;
            $newAllLocation[$arrAllLocationKey]['type'] = 'container';
        } else {
            $newAllLocation[$arrAllLocationKey]['location'] = $arrAlllocationValue;
            $newAllLocation[$arrAllLocationKey]['type'] = 'warehouse';
        }
    }
    $dataTooltip = [];
    foreach ($data_mainSizeIdHash as $key1 => $val1) {

    if(count($opt1SizeIdHash) > 0){

        foreach ($opt1SizeIdHash as $key2 => $val2) {
            $sql = '';
            $sql = "SELECT * FROM \"tbl_inventory\" as inv ";
            $sql .= " INNER JOIN \"tbl_invStorage\" as str ON str.\"invId\" = inv.\"inventoryId\"  where inv.\"styleId\"=".$data_style['styleId'];
            $sql .= " AND inv.\"sizeScaleId\" = '".$key1."' AND inv.\"opt1ScaleId\" = '".$key2."'";
            if ($_GET['colorId'] > 0) {
                $sql .= ' and inv."colorId"=' . $_GET['colorId'];
            } else if (count($data_color) > 0) {
                $sql .= ' and inv."colorId"=' . $data_color[0]['colorId'];
            }
            if (!($result = pg_query($connection, $sql))) {
                print("Failed invQuery: " . pg_last_error($connection));
                exit;
            }
            $arrData = [];
            while ($row = pg_fetch_array($result)) {
                 $arrData[] = $row;
            }
            $strArr = "";

            if(!is_null($arrData)){
                foreach ($arrData as $ka=>$arrDaraTool){
                    if(($arrDaraTool['unit'] != 0 || $arrDaraTool['unit'] != null) && $arrDaraTool['wareHouseQty'] > 0) {
                        $strArr .= '<a href="http://'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['REQUEST_URI'].'&unitId='.$arrDaraTool['unit'].'">'.$arrDaraTool['unit']." : ".$arrDaraTool['wareHouseQty']."</a></br>";
                    }
                }
            }
            $dataTooltip[$key1][$key2] = $strArr;
        }
    } else {
        $sql = '';
        $sql = "SELECT * FROM \"tbl_inventory\" as inv ";
        $sql .= " INNER JOIN \"tbl_invStorage\" as str ON str.\"invId\" = inv.\"inventoryId\"  where inv.\"styleId\"=".$data_style['styleId'];
        $sql .= " AND inv.\"sizeScaleId\" = '".$key1."'";
        if ($_GET['colorId'] > 0) {
            $sql .= ' and inv."colorId"=' . $_GET['colorId'];
        } else if (count($data_color) > 0) {
            $sql .= ' and inv."colorId"=' . $data_color[0]['colorId'];
        }
        if (!($result = pg_query($connection, $sql))) {
            print("Failed invQuery: " . pg_last_error($connection));
            exit;
        }
        $arrData = '';
        while ($row = pg_fetch_array($result)) {
            $arrData[] = $row;
        }
        $strArr = "";

        if(!is_null($arrData)){
            foreach ($arrData as $ka=>$arrDaraTool){
                if(($arrDaraTool['unit'] != 0 || $arrDaraTool['unit'] != null) && $arrDaraTool['wareHouseQty'] > 0) {
                    $strArr .= '<a href="http://'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['REQUEST_URI'].'&unitId='.$arrDaraTool['unit'].'">'.$arrDaraTool['unit']." : ".$arrDaraTool['wareHouseQty']."</a></br>";
                }
            }
        }
        $dataTooltip[$key1][0] = $strArr;
    }
}
    $sql = 'select * from "tbl_date_interval_setting"';
    if (!($resultProduct = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($resultProduct)) {
        $colorSettings[] = $row;
    }
    pg_free_result($resultProduct);
    if ((isset($_GET['unitId']) && $_GET['unitId'] != '0')) {
        $sqlUnit = " AND warehouse='".$_GET['unitId']."'";
    } else {
        $sqlUnit = '';
    }
    $sql = '';
    $sql = "select * from \"tbl_log_updates\" where \"styleId\" =" . $_GET['styleId'] . " and \"present\" ='inventory' ".$sqlUnit." order by \"updatedDate\" desc LIMIT 1";
    if (!($resultoldinv = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    $rowoldinv = pg_fetch_row($resultoldinv);
    $oldinv = $rowoldinv;
    pg_free_result($resultoldinv);
    if ($oldinv) {
        $empsql = 'select * from "employeeDB" where "employeeID" =' . $oldinv['2'] . ' LIMIT 1';
        if (!($resultemp = pg_query($connection, $empsql))) {
            print("Failed invQuery: " . pg_last_error($connection));
            exit;
        }
        $rowemp = pg_fetch_row($resultemp);
        $oldemp = $rowemp;
    }
    pg_free_result($resultemp);
    if (isset($_GET['unitId']) && $_GET['unitId'] != '0') {

    //$my_data;


    $sql = 'select str."locationId" , inv.location_details_id from "tbl_invStorage" as str  left join "tbl_inventory" as inv on inv."inventoryId" = str."invId" where str.unit = \'' . $_GET['unitId'] . '\' ';


    if (!($result = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($result)) {
        $my_data[] = $row;
    }

    $location_id = '';
    $location_details_id = '';
    foreach ($my_data as $key => $value) {

        $location_id = $value['locationId'];
        if ($value['location_details_id'] != '')
            $location_details_id = $value['location_details_id'];
    }
}
    if (isset($_REQUEST['unitId']) && $_REQUEST['unitId'] != '0') {
        $sql = "select distinct unit from \"tbl_invStorage\" where \"styleId\"=" . $_GET['styleId']." and unit<>'".$_GET['unitId']."' and merged = '0'";
        if ($_GET['colorId'] > 0) {
            $sql .= ' and "colorId"=' . $_GET['colorId'];
        } else if (count($data_color) > 0) {
            $sql .= ' and "colorId"=' . $data_color[0]['colorId'];
        }
        $sql .= ' order by unit asc';
        if (!($result_cnt9 = pg_query($connection, $sql))) {
            print("Failed InvData: " . pg_last_error($connection));
            exit;
        }
        while ($row_cnt9 = pg_fetch_array($result_cnt9)) {
            $mergeBox[] = $row_cnt9;
        }
    }
?>
<script type="text/javascript" src="<?php echo $mydirectory; ?>/js/jquery-ui.min-1.8.2.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory; ?>/js/samplerequest.js"></script>
<script src="<?php echo $mydirectory; ?>/js/modernizr.js"></script>
<script src="<?php echo $mydirectory; ?>/js/tabs.js"></script>
<link href="<?php echo $mydirectory; ?>/css/style.css" rel="stylesheet">
<div class="container">
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <table width="90%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="left"><input type="button" value="Back" onclick="location.href='reports.php'"/></td>
                    <td>&nbsp;</td>
                    <td align="right"><label>
                        <input type="button" name="send-email" id="send-email" value="Send Email"/>
                &nbsp;&nbsp; </label></td>
                </tr>
            </table>
        </div>
    </div>
</div>
<!-- The Modal -->
<div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <span id="close_warehouse_f" class="close">&times;</span>
        <div class="modal-header">
                <h4 class="modal-title" style="text-align: center;">ADD INVENTORY</h4>
        </div>
        <div class="modal-body">
            <div class="inventory-box">
                <div class="container">
                    <br/><br/>
                    <div class="wrapper">
                        <div class="top-table">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width: 30%">Style #:
                                        <strong>
                                            <?php echo $data_style['styleNumber']; ?>
                                        </strong>
                                        <input type="hidden" id="styleNumberAdd" name="styleNumber" value="<?php echo $data_style['styleNumber']; ?>">
                                    </td>
                                    <td style="width: 30%">Garment Type:
                                        <strong><?php echo $data_garment["garmentName"]; ?></strong>
                                    </td>
                                    <td style="width: 35%">Gender :<strong><?php echo(" " . $data_style['sex']); ?></strong></td>
                                    <td>Client: <strong><?php echo $data_client['client'] ?></strong></td>
                                </tr>
                                <tr>
                                    <td style="width: 35%">Color:<strong>
                                            <select name="col_new" id="col_new_add">
                                                <option value="">---Select a Color</option>
                                            <?php
                                            for ($i = 0; $i < count($data_color); $i++) {
                                                echo '<option value="'.$data_color[$i]['colorId'].'">'.$data_color[$i]['name'].'</option>';
                                            }
                                            ?>
                                            </select>
                                        </strong>
                                    </td>
                                    <td style="width: 10%">Location:
                                        <strong>
                                            <select name="add_new_location" id="add_new_location">
                                                <option value="0">All Location</option>
                                                <?php
                                                foreach ($newAllLocation as $arrallKey=>$arrallValue) {
                                                    echo '<option value="' . $arrallValue['location'] . '"';
                                                    if (isset($_REQUEST['location']) && $_REQUEST['location'] == $arrallValue['location']) echo ' selected="selected" ';
                                                    echo '>' . $arrallValue['location'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </strong>
                                    </td>
                                    <td colspan="2" id="box_add" style="display: none;">
                                        Box#:<strong>
                                            <input type="text" name="add_new_box" id="add_new_box"/>
                                        </strong>
                                    </td>
                                    <td colspan="2" id="slot_add" style="display: none;">
                                        Box#:<strong>
                                            <input type="text" name="add_new_slot" id="add_new_slot">
                                        </strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td id="row_add" style="display: none;">
                                        Row:<strong>
                                            <input type="text" name="add_new_row" id="add_new_row">
                                        </strong>
                                    </td>
                                    <td id="rack_add" style="display: none;">
                                        Rack:<strong>
                                            <input type="text" name="add_new_rack" id="add_new_rack">
                                        </strong>
                                    </td>
                                    <td id="shelf_add" style="display: none;">
                                        Shelf:<strong>
                                            <input type="text" name="add_new_shelf" id="add_new_shelf">
                                        </strong>
                                    </td>

                                </tr>
                            </table>
                        </div>
                        <br/><br/>
                        <div class="row">
                                <form id="inventoryFormNewAdd">
                                <div class="col-md-12 right-sidebar">
                                    <div class="inventory-table">
                                        <div class="table-responsive">
                                            <table class="table my-table">
                                                <tr>
                                                    <td>
                                                        <input type="hidden" name="scaleNameId"
                                                               value="<?php echo $data_style['scaleNameId']; ?>"/>
                                                        <input type="hidden" id="styleId1" name="styleId"
                                                               value="<?php echo $styleId; ?>"/>
                                                        <table class="table ">
                                                        <?php
                                                        // echo "<pre>";print_r($data_style);
                                                        $len1 = sizeof($data_mainSizeIdHash);
                                                        $row1= $len1 / sizeof($data_mainSizeIdHash);
                                                        $row1 += $len1 % 4 == 0 ? 0 : 1;
                                                        $row1 = 1;

                                                        $start_head1 = "<div class='row1'>";
                                                        $end1 = "</div>";
                                                        $start_div1 = "<tr><td><div class='title-section'><div class='col-md-12 nopadding'>";
                                                        $end_div1 = "</div></div></td></tr>";
                                                        $data_div1 = $start_div1 . '<p>sizes</p>' . $end_div1;
                                                        /*$data_div .= $start_div . '<p>prices</p>' . $end_div;*/
                                                        $cnt1 = 0;
                                                        if(count($opt1SizeIdHash) > 0){
                                                            foreach ($opt1SizeIdHash as $key1 => $value1) {
                                                                $cnt1++;
                                                                $data_div1 .= $start_div1 . "<p>" . $value1 . "</p>" . $end_div1;
                                                                //echo $data_div."<br>";
                                                            }
                                                        } else {
                                                            $data_div1 .= $start_div1 . "<p>Qty</p>" . $end_div1;
                                                        }

                                                        //var_dump($row);exit();
                                                        while ($row1--) {
                                                            echo $start_head1 . $data_div1 . $end1;
                                                        }
                                                        ?>
                                                        </table>
                                                    </td>
                                                    <td>
                                                        <table>
                                                            <?php
                                                            $mainsize_div1 = '<td><div class="each-section">';
                                                            $mainsize_div_end1 = '</div></td>';
                                                            $data1 = '';
                                                            $element1 = '';
                                                            $count = 0;
                                                            foreach ($data_mainSizeIdHash as $key11 => $val11) {
                                                                $element1 .= '<span>' . $val11 . '</span>';
                                                                if(count($opt1SizeIdHash) > 0){
                                                                    foreach ($opt1SizeIdHash as $key21 => $val21) {
                                                                        $element1 .= '<span><input class="clicked_new" id="input_' . $key11 . '_' . $key21 . '" type="text" value="0" name="new_qty_data[]"></span>';
                                                                        $element1 .= '<input type="hidden" value="' . $val11 . '" name="new_type_data[]">';
                                                                        $element1 .= '<input type="hidden" value="' . $val21 . '" name="new_size_data[]">';
                                                                        if($count == 0){
                                                                            $element1 .= '<input id="h_' . $key11 . '_' . $key21 . '" type="hidden" value="1" name="is_change_new[]">';
                                                                        } else {
                                                                            $element1 .= '<input id="h_' . $key11 . '_' . $key21 . '" type="hidden" value="0" name="is_change_new[]">';
                                                                        }
                                                                        $count++;
                                                                    }
                                                                } else {
                                                                    $element1 .= '<span><input class="clicked_new" id="input_' . $key11 . '_' . 0 . '" type="text" value="0" name="new_qty_data[]"></span>';
                                                                    $element1 .= '<input type="hidden" value="' . $val11 . '" name="new_type_data[]">';
                                                                    $element1 .= '<input type="hidden" value="NULL" name="new_size_data[]">';
                                                                    $element1 .= '<input id="h_' . $key11 . '_' . 0 . '" type="hidden" value="1" name="is_change_new[]">';
                                                                }

                                                                $data1 .= $mainsize_div1 . $element1 . $mainsize_div_end1;
                                                                $element1 = '';
                                                            }
                                                            echo $data1;
                                                            ?>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="row col-md-12 align-right">
                            <div id="message_add"></div>
                        </div>
                        <div class="row col-md-12 align-right">
                            <button class="btn btn-success btn-lg" id="add_inventory_new">Add Inventory</button>
                        </div>
                            </form>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <h2 class="custom-head">Report View/Edit </h2>
            <div align="center" id="message"></div>
            <form id="optForm" method="post">
                <strong>
                    <table class="table1" width="60%" style="margin: 0 auto; text-align: center;">
                <tr>
                    <td>Style: <h4><?php echo $data_style['styleNumber']; ?></h4>
                    </td>
                    <td>
                        Color:&nbsp;<br>
                        <select class="color-option" name="color" id="color">


                            <?php
                            for ($i = 0; $i < count($data_color); $i++) {
                                if ($data_color[$i]['name'] != "") {
                                    if ($data_color[$i]['colorId'] == $clrId) {
                                        $imageName = $data_color[$i]['image'];
                                        echo '<option selected="selected" data-color="' . $data_color[$i]['name'] . '" value="' . $data_color[$i]['colorId'] . '">' . $data_color[$i]['name'] . '</option>';
                                        continue;
                                    }
                                    echo '<option value="' . $data_color[$i]['colorId'] . '">' . $data_color[$i]['name'] . '</option>';
                                }
                            }
                            ?>
                        </select>&nbsp;
                    </td>
                    <td>
                        box #:&nbsp;<br><select name="unit_num" id="unit_num" class="unit_num">
                            <option value="0">---- All box # ----</option>
                            <?php
                            for ($i = 0; $i < count($data_storage); $i++) {
                                if ($data_storage[$i]['unit'] != "")
                                    echo '<option value="' . $data_storage[$i]['unit'] . '"';
                                if (isset($_REQUEST['unitId']) && $_REQUEST['unitId'] == $data_storage[$i]['unit']) echo ' selected="selected" ';
                                echo '>' . $data_storage[$i]['unit'] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <button id="print" type="button"
                                onclick="print_content('<?php echo $_GET['styleId']; ?>'
                                        ,'<?php echo $data_loc[$loc_identity]['name'] ?>'
                                        ,'<?php if (isset($_GET['unitId'])) echo $_GET['unitId'];
                                else echo 'null' ?>')"
                        >Print</button>
                        <span id="hide" style="display: inline-block !important;">
                        <?php if (isset($_SESSION['employeeType']) AND $_SESSION['employeeType'] != 5) { ?>
                            <button type="button" id="addinventory_new">Add Inventory</button>
                        <?php } ?>
                        </span>
                        <?php if (isset($_SESSION['employeeType']) AND $_SESSION['employeeType'] != 5) { ?>
                            <input class="main-inventory" type="button" value="Main Inventory" onclick="main_inv();"/>
                        <?php } ?>
                    </td>

                </tr>
                <tr>
                <tr >
                    <td>
                    </td>
                </tr>
                    <td colspan="2">
                        <?php if ($data_style['barcode'] != "") { ?>
                    Barcode:
                    <h1><img width="100" height="100"
                                 src="../../uploadFiles/inventory/images/<?php echo $data_style['barcode']; ?>">
                        </h1>
                    <?php } ?>
                    </td>
                </tr>
            </table>
                </strong>
            </form>
        </div>
    </div>
</div>
<div style="display:none">
    <?php
    if ($oldinv) {
        ?>
        <fieldset style="margin:10px;">
            <table width="98%" border="0" cellspacing="1" cellpadding="1">
                <tbody>
                <tr>
                    <td width="355" height="25" align="right" valign="top">Date: <br></td>
                    <td width="10">&nbsp;</td>
                    <td align="left" valign="top">
                        <?php echo date("F j, Y, g:i a", $oldinv['3']); ?>
                        <?php
                        $t = time();
                        $datetime1 = date_create(date('Y-m-d', $t));
                        $datetime2 = date_create(date('Y-m-d', $oldinv['3']));
                        $interval = date_diff($datetime1, $datetime2);
                        $days = $interval->format('%a') + 1;
                        $colo = 'black';
                        if (isset($oldinv['3'])) {
                            foreach ($colorSettings as $colorSetting) {
                                if ($colorSetting['interval'] == $days) {
                                    $colo = $colorSetting['color'];
                                }
                            }
                            $updatedby = $oldemp['1'] . " " . $oldemp['2'];
                            echo '<div id="button" style="width:25px; height: 25px; border-radius:100%; background-color:' . $colo . ';"></div>';
                        }
                        ?>
                    </td>
                </tr>
                <tr>

                    <td width="355" height="25" align="right" valign="top">Updated By: <br></td>
                    <td width="10">&nbsp;</td>
                    <td align="left" valign="top">
                        <?php echo $oldemp['1'] . " " . $oldemp['2']; ?>
                    </td>
                </tr>
                <tr>
                    <td width="355" height="25" align="right" valign="top">Data: <br></td>
                    <td width="10">&nbsp;</td>
                    <td align="left" valign="top">
                        <div class="table-responsive">
                            <table>
                                <tr>
                                    <td>Scale1 &nbsp;&nbsp;</td>
                                    <td>Scale2 &nbsp;&nbsp;</td>
                                    <td>Previous &nbsp;&nbsp;</td>
                                    <td>Present &nbsp;&nbsp;</td>
                                    <td>Unit &nbsp;&nbsp;</td>
                                </tr>
                                <?php
                                $data = json_decode($oldinv['5']);
                                foreach ($data as $key => $prevalue) {
                                    //print_r($prevalue);
                                    ?>
                                    <tr>
                                        <td><?php logCheckOStyle($prevalue->sizeScaleId, $connection); ?> &nbsp;&nbsp;</td>
                                        <td><?php
                                            if ($prevalue->opt1ScaleId != '') {
                                                logCheckNStyle($prevalue->opt1ScaleId, $connection);
                                            } else {
                                                echo 'Qty';
                                            }
                                            ?>&nbsp;&nbsp;
                                        </td>
                                        <td><?php echo $prevalue->oldinv; ?>&nbsp;&nbsp;</td>
                                        <td><?php echo $prevalue->wareHouseQty; ?>&nbsp;&nbsp;</td>
                                        <td><?php echo $prevalue->unit; ?>&nbsp;&nbsp;</td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </fieldset>
        <?php
    }
    ?>
</div>
<form id="inventoryForm" style="display: none;">
    <input type="hidden" id="_location_id" name="location_id"
           value="<?php echo (isset($location_id) && $location_id) > 0 ? $location_id : 'null' ?>">

    <input type="hidden" id="_inventory_id" name="inventory_id"
           value="<?php echo (isset($inventory_id) && $inventory_id > 0) ? $inventory_id : 'null' ?>">

    <input type="hidden" id="_location_details_id" name="location_details_id"
           value="<?php echo (isset($location_details_id) && $location_details_id) > 0 ? $location_details_id : 'null' ?>">

    <div id="scrollLinks">
        <fieldset style="margin:10px;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="10"></td>
                    <td width="170" align="left" valign="top" style="padding:5px;">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td><img id="imgView"
                                         src="<?php echo $upload_dir_image . trim($imageName); ?>"
                                         alt="thumbnail" width="150" height="230" border="1"
                                         class="mouseover_left"/></td>
                            </tr>
                            <tr>
                                <td height="100">&nbsp;</td>
                            </tr>


                            <?php if (isset($_SESSION['employeeType']) && $_SESSION['employeeType'] < 4) { ?>

                                <?php
                                if (!isset($_GET['unitId']) || $_GET['unitId'] != '0') {
                                    ?>
                                    <tr>
                                        <td>

                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>


                            <?php } ?>
                        </table>
                    </td>
                    <td width="10"></td>
                    <td width="100">
                        <div id="header" style="float:left; width:100%;">
                            <div id="scrollLinks4">
                                <div id="scrollLinks2">
                                    <div id="scrollLinks">
                                        <div id="scrollLinks3">
                                            <table class="HD001" width="250px" style="float:left;"
                                                   border="0" cellspacing="1" cellpadding="1">
                                                <tr>
                                                    <td class="gridHeaderReportGrids3">&nbsp;</td>
                                                    <td class="gridHeaderReport">sizes</td>
                                                </tr>
                                                <tr>
                                                    <td class="gridHeaderReportGrids3"><a
                                                                class="mouseover_left" href="#"><img
                                                                    src="<?php echo $mydirectory; ?>/images/leftArrw.gif"
                                                                    alt="lft" width="33" height="26"
                                                                    border="0"/></a><a
                                                                class="mouseover_right" href="#"><img
                                                                    src="<?php echo $mydirectory; ?>/images/rightArrw.gif"
                                                                    alt="lft" width="30" height="26"
                                                                    border="0"/></a></td>
                                                    <td class="gridHeaderReport">prices</td>
                                                </tr>
                                                <tr>
                                                    <td class="gridHeaderReportGrids3">&nbsp;</td>
                                                    <td class="gridHeaderReportGrids2">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td class="gridHeaderReportGrids3">&nbsp;</td>
                                                    <td colspan="2"
                                                        class="gridHeaderReportGrids4"><?php echo $data_optionName['opt1Name']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="gridHeaderReportGrids3">&nbsp;</td>
                                                    <td class="gridHeaderReportGrids2"><span
                                                                class="gridHeaderReportGrids3"><a
                                                                    class="mouseover_up" href="#"><img
                                                                        src="<?php echo $mydirectory; ?>/images/upArrw.gif"
                                                                        alt="lft" width="33" height="26"
                                                                        border="0"/></a><a
                                                                    class="mouseover_down" href="#"><img
                                                                        src="<?php echo $mydirectory; ?>/images/dwnArrw.gif"
                                                                        alt="lft" width="33" height="26"
                                                                        border="0"/></a></span></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="TopDiv">
                                <!-- window 1 starts here -->
                                <div id="wn">
                                    <div id="lyr1">
                                        <table style="float:left;"
                                               width="<?php echo $tableWidth . "px"; ?>" border="0"
                                               cellspacing="1" cellpadding="1">
                                            <tr id="mainSizeTop">
                                            </tr>
                                            <tr id="priceTop">
                                            </tr>
                                            <tr id="dummy1">
                                            </tr>
                                            <tr id="dummy2">
                                            </tr>
                                            <tr id="columnSize">
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="wn3">
                            <div id="lyr3">
                                <div id="rowSize" style="float:left; width:250px;">
                                    <table width="250" border="0" cellspacing="1" cellpadding="1">
                                        <?php
                                        if ($locArr[0] > 0 && $locArr[0] != "") {
                                            $loc_i = 0;
                                            for ($i = 0; $i < count($locArr); $i++, $loc_i++) {
                                                for (; $loc_i < count($data_loc); $loc_i++) {
                                                    if ($locArr[$i] == $data_loc[$loc_i]['locationId'])
                                                        break;
                                                }
                                                ?>
                                                <tr>
                                                <td class="gridHeaderReportGrids3"><?php //echo $data_loc[$loc_i]['name'];
                                                    $loc_identity = $loc_i; ?></td>
                                                <?php
                                                if (count($data_opt1Size) > 0) {
                                                    for ($j = 0; $j < count($data_opt1Size); $j++) {
                                                        if ($j != 0) {
                                                            ?>
                                                            <tr>
                                                                <td class="gridHeaderReportGrids3">
                                                                    &nbsp;
                                                                </td>
                                                                <td class="gridHeaderReportalt"><?php echo $data_opt1Size[$j]['opt1Size']; ?></td>
                                                            </tr>

                                                            <?php
                                                        } else {
                                                            ?>
                                                            <td class="gridHeaderReportalt"><?php echo $data_opt1Size[$j]['opt1Size']; ?></td>
                                                            </tr>
                                                            <?php
                                                        }
                                                    }
                                                } else {
                                                    ?>
                                                    <td style="visibility:hidden;"
                                                        class="gridHeaderReportGrids2">&nbsp;
                                                    </td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                <tr>
                                                    <td class="gridHeaderReportGrids3">&nbsp;</td>
                                                    <td class="gridHeaderReportGrids2">&nbsp;</td>
                                                </tr>
                                                <?php
                                            }//LocArr for
                                        }//locArr if
                                        ?>
                                    </table>
                                </div>
                                <?php if (count($data_opt1Size) > 0){ ?>
                                <div id="wn2"
                                     style="position:relative; width:600px; height:<?php echo((count($data_opt1Size) * count($locArr) * 32) + (count($data_loc) * 32)); ?>px;  overflow:hidden; float:left;">
                                    <?php }else{ ?>
                                    <div id="wn2"
                                         style="position:relative; width:600px; height:<?php echo(count($locArr) * 64); ?>px;  overflow:hidden; float:left;">
                                        <?php } ?>
                                        <div id="lyr2">
                                            <div id="values_">

                                                <table id="values"
                                                       width="<?php echo $tableWidth . "px"; ?>"
                                                       border="0" cellspacing="1" cellpadding="1">

                                                    <?php
                                                    // print_r($data_opt1Size); ------------ size1 ------------
                                                    // exit;

                                                    //$data_mainSize,$data_inv,$data_opt1Size[$j]['opt1SizeId'],$locArr[$i],$locIndex,$rowIndex
                                                    // print_r($data_mainSize);
                                                    // exit();

                                                    // print_r($data_inv);
                                                    // exit();
                                                    if ($locArr[0] > 0 && $locArr[0] != "") {
                                                        for ($i = 0; $i < count($locArr); $i++) {
                                                            $rowIndex = 0;
                                                            if (count($data_opt1Size) > 0) {
                                                                for ($j = 0; $j < count($data_opt1Size); $j++) {
                                                                    echo '<tr id="qty_' . $i . '_' . $rowIndex . '"></tr>';
                                                                    $rowIndex++;
                                                                }
                                                            } else {
                                                                echo '<tr id="qty_' . $i . '_' . $rowIndex . '"></tr>';
                                                            }
                                                            echo '<tr id="qtyDummy' . $i . '"></tr>';
                                                        }
                                                    }
                                                    ?>
                                                </table>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <div id="footer" style="width:100%; float:left;">
                                <table class="HD001" style="float:left; width:250px;" border="0"
                                       cellspacing="1" cellpadding="1">
                                    <tr>
                                        <td class="gridHeaderReportGrids3">&nbsp;</td>
                                        <td class="gridHeaderReport">sizes</td>
                                    </tr>
                                </table>
                                <div id="wn4">
                                    <div id="lyr4">
                                        <table class="TopValues"
                                               style="float:left; width:<?php echo $tableWidth . "px"; ?>;"
                                               border="0" cellspacing="1" cellpadding="1">
                                            <tr id="mainSizeBottom">
                                            </tr>
                                            <tr id="adjBottom"></tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                    </td>
                    <td>
                        <input id="update_inventory" width="117" height="98"
                               type="image"
                               src="<?php echo $mydirectory; ?>/images/updtInvbutton.jpg"
                               alt="Submit button"/>
                    </td>
                </tr>
                <tr>
                    <td id="hdnVar">
                        <input type="hidden" name="scaleNameId"
                               value="<?php echo $data_style['scaleNameId']; ?>"/>
                        <input type="hidden" id="styleId" name="styleId"
                               value="<?php echo $styleId; ?>"/>
                        <input type="hidden" id="colorId" name="colorId" value="<?php echo $clrId; ?>"/>
                        <input type="hidden" id="locCount" name="locCount" value="0"/>
                        <input type="hidden" id="rowCount" name="rowCount" value="0"/>
                        <input type="hidden" id="mainCount" name="mainCount" value="0"/>
                    </td>
                </tr>
            </table>
            <br/>
        </fieldset>
    </div>
</form>
<div class="inventory-box">
    <div class="container">
        <br/><br/>
        <div class="wrapper">
            <div class="top-table">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="width:87%">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width: 30%">Style #: <strong><?php echo $data_style['styleNumber']; ?></strong></td>
                                    <td style="width: 35%">
                                        Employee:<strong><?php echo $data_employee['firstname'] . ' ' . $data_employee['lastname']; ?></strong>
                                    </td>
                                    <td style="width: 35%">Date Entered:
                                        <strong><?php echo ($latest != '0')?date("F j, Y, g:i a", $latest):''; ?></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">Garment Type:
                                        <strong><?php echo $data_garment["garmentName"]; ?></strong></td>
                                    <td style="width: 35%">Color:<strong>
                                            <?php
                                            for ($i = 0; $i < count($data_color); $i++) {
                                                if ($data_color[$i]['name'] != "") {
                                                    if ($data_color[$i]['colorId'] == $clrId) {
                                                        echo $data_color[$i]['name'];
                                                        break;
                                                    }
                                                } else {
                                                    echo 'Unknown';
                                                }
                                            }
                                            ?></strong>
                                    </td>
                                    <td style="width: 35%">Gender :<strong><?php echo(" " . $data_style['sex']); ?></strong></td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">Client: <strong><?php echo $data_client['client'] ?></strong></td>
                                    <td style="width: 30%">Location:
                                        <strong>
                                            <?php
                                            if (isset($_REQUEST['unitId']) && $_REQUEST['unitId'] != '0')
                                                echo $data_loc[$loc_identity]['name'];
                                            else echo "All Location";
                                            ?>
                                        </strong>
                                    </td>
                                    <td>
                                        <?php
                                            if (isset($_REQUEST['unitId']) && $_REQUEST['unitId'] != '0') {
                                                if(count($mergeBox) > 0){
                                                    ?>
                                                    <button class="btn btn-warning" type="button" id="mergeBoxButton"
                                                            onclick="mergrButton()" style="color: #6f4215"> Merge
                                                    </button>
                                                    <span id="margeBoxId" style="display: none; border: 1px solid red;">
                                                    <label>Select A Box to Merge</label>
                                                    <select name="mergebox" id="mergeBox" class="select-style">
                                                        <option value="">Select a Box</option>
                                                        <?php
                                                        for ($i = 0; $i < count($mergeBox); $i++) {
                                                            echo '<option value="' . $mergeBox[$i]['unit'] . '">' . $mergeBox[$i]['unit'] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </span>
                                                    <?php
                                                }
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 10%">
                                        Box#:<strong> <?php if (isset($_REQUEST['unitId']) && $_REQUEST['unitId'] != '0') echo $_REQUEST['unitId']; else echo "All Box" ?></strong>
                                    </td>
                                    <td>Box#:
                                        <strong> <select name="unit_num" class="slot_num">
                                                <option value="0">---- All box # ----</option>
                                                <?php
                                                for ($i = 0; $i < count($data_storage); $i++) {
                                                    if ($data_storage[$i]['unit'] != "")
                                                        echo '<option value="' . $data_storage[$i]['unit'] . '"';
                                                    if (isset($_REQUEST['unitId']) && $_REQUEST['unitId'] == $data_storage[$i]['unit']) echo ' selected="selected" ';
                                                    echo '>' . $data_storage[$i]['unit'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </strong>
                                    </td>
                                    <td>
                                        <?php
                                        if (isset($_REQUEST['unitId']) && $_REQUEST['unitId'] != '0') {
                                            echo '<button class="btn btn-danger" type="button" onclick="Delete()" style="color: #cd0a0a"> Delete </button>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                $unitPost = $_GET['unitId'];
                                $explode = explode('_',$unitPost);
                                $sql = '';
                                $sql = "SELECT \"locationId\" FROM \"tbl_invLocation\" WHERE identifier='".$explode[0]."'";
                                if (!($result = pg_query($connection, $sql))) {
                                    print("Failed invQuery: " . pg_last_error($connection));
                                    exit;
                                }
                                $locIdbody = pg_fetch_row($result);
                                $sql = '';
                                $sql = "SELECT * FROM \"locationDetails\" WHERE \"locationId\"='".$locIdbody[0]."'";
                                $sql .= " and ( warehouse='".$explode[1]."' OR container='".$explode[1]."' OR conveyor ='".$explode[1]."' )";
                                if (!($result = pg_query($connection, $sql))) {
                                    print("Failed invQuery: " . pg_last_error($connection));
                                    exit;
                                }
                                $warehouses_all_body = pg_fetch_row($result);
                                if($warehouses_all_body[3] == '' && $warehouses_all_body[4] == ''){
                                    ?>
                                    <tr>
                                        <td>Row:
                                            <strong>
                                                <?php
                                                if (isset($_REQUEST['unitId']) && $_REQUEST['unitId'] != '0') {
                                                    echo '<input type="text" id="up_row" value="' . $data_product[0]['row'] . '">';
                                                } else {
                                                    echo 'All';
                                                }
                                                ?>
                                            </strong>
                                        </td>
                                        <td>Rack:
                                            <strong>
                                                <?php
                                                if (isset($_REQUEST['unitId']) && $_REQUEST['unitId'] != '0') {
                                                    echo '<input type="text" id="up_rack"  value="' . $data_product[0]['rack'] . '">';
                                                } else {
                                                    echo 'All';
                                                }
                                                ?>
                                            </strong>
                                        </td>
                                        <td>Shelf:
                                            <strong>
                                                <?php
                                                if (isset($_REQUEST['unitId']) && $_REQUEST['unitId'] != '0') {
                                                    echo '<input type="text" id="up_shelf"  value="' . $data_product[0]['shelf'] . '">';
                                                } else {
                                                    echo 'All';
                                                }
                                                ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <?php
                                            if (isset($_REQUEST['unitId']) && $_REQUEST['unitId'] != '0') {
                                                echo '<button class="btn btn btn-success" onclick="UpdateNew()">Update</button>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tr>
                                <tr>
                                    <td id="hdnVar">
                                        <input type="hidden" name="scaleNameId"
                                               value="<?php echo $data_style['scaleNameId']; ?>"/>
                                        <input type="hidden" id="styleId" name="styleId"
                                               value="<?php echo $styleId; ?>"/>
                                        <input type="hidden" id="colorId" name="colorId" value="<?php echo $clrId; ?>"/>
                                        <input type="hidden" id="locCount" name="locCount" value="0"/>
                                        <input type="hidden" id="rowCount" name="rowCount" value="0"/>
                                        <input type="hidden" id="mainCount" name="mainCount" value="0"/>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 13%;">
                            <table width="100%" cellpadding="0" cellspacing="0" >
                                <tr>
                                    <td>
                                        <span class="p-pic">
                                            <img id="imgView" src="<?php echo $upload_dir_image . trim($imageName); ?>" alt="thumbnail"
                                                    width="150" height="230" border="1" class="mouseover_left"/>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
            </div>
            <br/><br/>
            <div class="row">
                <form id="inventoryFormNew">
                    <div class="col-md-12 right-sidebar">
                        <div class="inventory-table">
                            <div class="table-responsive">
                                <table class="table my-table">
                                    <tr>
                                        <td>
                                            <input type="hidden" name="scaleNameId"
                                                   value="<?php echo $data_style['scaleNameId']; ?>"/>
                                            <input type="hidden" id="styleId" name="styleId"
                                                   value="<?php echo $styleId; ?>"/>
                                            <input type="hidden" id="colorId" name="colorId" value="<?php echo $clrId; ?>"/>
                                            <table class="table ">
                                            <?php
                                             //echo "<pre>";print_r($data_set);die();
                                            $len = sizeof($data_mainSizeIdHash);
                                            $row = $len / sizeof($data_mainSizeIdHash);
                                            $row += $len % 4 == 0 ? 0 : 1;
                                            $row = 1;

                                            $start_head = "<div class='row1'>";
                                            $end = "</div>";
                                            $start_div = "<tr><td><div class='title-section'><div class='col-md-12 nopadding'>";
                                            $end_div = "</div></div></td></tr>";
                                            $data_div = $start_div . '<p>sizes</p>' . $end_div;
                                            /*$data_div .= $start_div . '<p>prices</p>' . $end_div;*/
                                            $cnt = 0;
                                            if(count($opt1SizeIdHash) > 0){
                                                foreach ($opt1SizeIdHash as $key => $value) {
                                                    $cnt++;
                                                    $data_div .= $start_div . "<p>" . $value . "</p>" . $end_div;
                                                    //echo $data_div."<br>";
                                                }
                                            } else {
                                                $data_div .= $start_div . "<p>Qty</p>" . $end_div;
                                            }



                                            //var_dump($row);exit();
                                            while ($row--) {
                                                echo $start_head . $data_div . $end;
                                            }
                                            ?>

                                            </table>
                                        </td>
                                        <td>
                                            <table>
                                            <?php
                                            $mainsize_div = '<td><div class="each-section">';
                                            $mainsize_div_end = '</div></td>';
                                            $data = '';
                                            $element = '';
                                            foreach ($data_mainSizeIdHash as $key1 => $val1) {
                                                $element .= '<span>' . $val1 . '</span>'; //for sizes
                                                /*                                                $price = isset($data_style['price'])
                                                                                                    ? $data_style['price']
                                                                                                    : ' ';
                                                                                                $element .= '<span><input type="text" value="' . $price . '" readonly></span>'; //for prices*/
                                                if(count($opt1SizeIdHash) > 0){
                                                    foreach ($opt1SizeIdHash as $key2 => $val2) {
                                                        if (isset($data_set[$key1][$key2])) {
                                                            if (!(isset($_GET['unitId'])) || $_GET['unitId'] == '0') {
                                                                if ($data_set[$key1][$key2] > 0) {
                                                                    $element .= '<span class="tool">';
                                                                } else {
                                                                    $element .= '<span>';
                                                                }
                                                            } else {
                                                                $element .= '<span>';
                                                            }
                                                            $element .= '<input class="clicked"  id="input_' . $key1 . '_' . $key2 . '" type="text" value="' . $data_set[$key1][$key2] . '" name="new_qty_data[]"></span>';
                                                            $element .= '<p class="tooltext">'.$dataTooltip[$key1][$key2].'</p>';
                                                            $element .= '<input type="hidden" value="' . $val1 . '" name="new_type_data[]">';
                                                            $element .= '<input type="hidden" value="' . $val2 . '" name="new_size_data[]">';
                                                            $element .= '<input type="hidden" id="_' . $key1 . '_' . $key2 . '" value="0" name="is_change[]">';
                                                            $element .= '<input type="hidden" id="data_inv_new" name="data_inv_new" value="'.$data_invNew[$key1][$key2].'">';
                                                        } else {
                                                            $element .= '<span><input class="clicked"  id="input_' . $key1 . '_' . $key2 . '" type="text" value="0" name="new_qty_data[]"></span>';
                                                            $element .= '<p class="tooltext">'.$dataTooltip[$key1][$key2].'</p>';
                                                            $element .= '<input type="hidden" value="' . $val1 . '" name="new_type_data[]">';
                                                            $element .= '<input type="hidden" value="' . $val2 . '" name="new_size_data[]">';
                                                            $element .= '<input id="_' . $key1 . '_' . $key2 . '" type="hidden" value="0" name="is_change[]">';
                                                            $element .= '<input type="hidden" id="data_inv_new" name="data_inv_new" value="0">';
                                                        }
                                                    }
                                                } else {
                                                    if(isset($data_set[$key1][0])){
                                                        if (!(isset($_GET['unitId'])) || $_GET['unitId'] == '0') {
                                                            if ($data_set[$key1][0] > 0) {
                                                                $element .= '<span class="tool">';
                                                            } else {
                                                                $element .= '<span>';
                                                            }
                                                        } else {
                                                            $element .= '<span>';
                                                        }
                                                        $element .= '<input class="clicked" id="input_' . $key1 . '_' . 0 . '" type="text" value="' . $data_set[$key1][0] . '" name="new_qty_data[]"></span>';
                                                        $element .= '<p class="tooltext">'.$dataTooltip[$key1][0].'</p>';
                                                        $element .= '<input type="hidden" value="' . $val1 . '" name="new_type_data[]">';
                                                        $element .= '<input type="hidden" value="NULL" name="new_size_data[]">';
                                                        $element .= '<input type="hidden" id="_' . $key1 . '_' . 0 . '" value="0" name="is_change[]">';
                                                        $element .= '<input type="hidden" id="data_inv_new" name="data_inv_new[]" value="'.$data_invNew[$key1][0].'">';
                                                    } else {
                                                        $element .= '<span><input class="clicked" id="input_' . $key1 . '_' . 0 . '" type="text" value="0" name="new_qty_data[]"></span>';
                                                        $element .= '<p class="tooltext">'.$dataTooltip[$key1][0].'</p>';
                                                        $element .= '<input type="hidden" value="' . $val1 . '" name="new_type_data[]">';
                                                        $element .= '<input type="hidden" value="NULL" name="new_size_data[]">';
                                                        $element .= '<input type="hidden" id="_' . $key1 . '_' . 0 . '" value="0" name="is_change[]">';
                                                        $element .= '<input type="hidden" id="data_inv_new" name="data_inv_new[]" value="'.$data_invNew[$key1][0].'">';
                                                    }
                                                }
                                                $data .= $mainsize_div . $element . $mainsize_div_end;
                                                $element = '';
                                            }
                                            echo $data;
                                            ?>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row col-md-12 align-right">
                        <?php if (isset($_SESSION['employeeType']) AND $_SESSION['employeeType'] != 5) { ?>
                            <input id="update_inventory_new" width="117" height="98"
                                   type="image"
                                   src="<?php echo $mydirectory; ?>/images/updtInvbutton.jpg"
                                   alt="Submit button"/>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="dialog-form" title="Submit By Email">
    <p class="validateTips">All form fields are required.</p>
    <form action='reportMail.php?styleId=<?php echo $styleId; ?>' id="frmmailsendform" method="POST">
        <fieldset>
            <label for="email">Email</label>
            <input type="text" name="email" id="email" value="" class="text ui-widget-content ui-corner-all"/>
            <label for="subject">Subject:</label>
            <input type="text" name="subject" id="subject" class="text ui-widget-content ui-corner-all"/>
        </fieldset>
        <input type="hidden" name="colorId" id="colorid_mail" value="<?php echo $_GET['colorId']; ?>"/>
        <input type="hidden" name="unitId" id="unitId_mail" value="<?php echo $_GET['unitId']; ?>"/>
        <input type="hidden" name="styleId" id="styleId_mail" value="<?php echo $_GET['styleId']; ?>"/>
    </form>
</div>
<script type="text/javascript">
        var opt1Array_str = null;
        var obj_arr = new Array();
        function storeopt1Array(str) {
            opt1Array_str = str;
            var obj = opt1Array_str.split(",").map(String);
            for (var key in obj) {
                var temp = obj[key].split("=").map(String);
                obj_arr.push(temp[1].slice(1, -1));
            }
        }
</script>
<script>
        $('#add_new_location').change(function(){
            var arr = <?php echo json_encode($newAllLocation); ?>;
            var select = $('#add_new_location').val();
            var type = '';
            for(var i=0;i<arr.length;i++){
                if(arr[i]['location'] == select){
                    type =arr[i]['type'];
                    break;
                }
            }
            if(type == 'warehouse'){
                $('#box_add').show();
                $('#row_add').show();
                $('#rack_add').show();
                $('#shelf_add').show();
                $('#slot_add').hide();
            } else if(type == 'container'){
                $('#row_add').hide();
                $('#rack_add').hide();
                $('#shelf_add').hide();
                $('#box_add').show();
                $('#slot_add').hide();
            } else if(type == 'conveyor'){
                $('#slot_add').show();
                $('#box_add').hide();
                $('#row_add').hide();
                $('#rack_add').hide();
                $('#shelf_add').hide();
            } else {
                $('#slot_add').hide();
                $('#box_add').hide();
                $('#row_add').hide();
                $('#rack_add').hide();
                $('#shelf_add').hide();
            }
        });
        $('#add_inventory_new').click(function () {
            var arr = <?php echo json_encode($newAllLocation); ?>;
            var location = $('#add_new_location').val();
            if(location == '0' || location == "undefined" || location == null){
                $('#message_add').html('<h4 style="color: red">Please Select a Location</h4>');
                $('#message_add').show();
                return false;
            }
            var style = $('#styleNumberAdd').val();
            if(style == undefined || style == null){
                $('#message_add').html('<h4 style="color: red">Style not available</h4>');
                $('#message_add').show();
                return false;
            }
            var color = $('#col_new_add').val();
            if(color == undefined || color == null || color == "" || color == '0'){
                $('#message_add').html('<h4 style="color: red">Please Select a Color</h4>');
                $('#message_add').show();
                return false;
            }
            var type = '';
            for(var i=0;i<arr.length;i++){
                if(arr[i]['location'] == location){
                    type =arr[i]['type'];
                    break;
                }
            }
            var box = '';
            var row = '';
            var rack = '';
            var shelf = '';
            var slot = '';
            var unitId = '';
            if(type == 'warehouse'){
                box = $('#add_new_box').val();
                if(box == '' || box == null){
                    $('#message_add').html('<h4 style="color: red;">Please Enter a box number</h4>')
                    $('#message_add').show();
                    return false;
                }
                unitId = location+'_'+box;
                row = $('#add_new_row').val();
                rack = $('#add_new_rack').val();
                shelf = $('#add_new_shelf').val();
            } else if(type == 'container'){
                box = $('#add_new_box').val();
                if(box == '' || box == null){
                    $('#message_add').html('<h4 style="color: red;">Please Enter a box number</h4>')
                    $('#message_add').show();
                    return false;
                }
                unitId = location+'_'+box;
            } else if(type == 'conveyor'){
                slot = $('#add_new_slot').val();
                if(slot == '' || slot == null){
                    $('#message_add').html('<h4 style="color: red;">Please Enter a Slot number</h4>');
                    $('#message_add').show();
                    return false;
                }
                unitId = location+'_'+slot;
            } else {
                $('#message_add').html('<h4 style="color: red;">Box Type undefined</h4>');
                $('#message_add').show();
                return false;
            }
            var datastring = $('#inventoryFormNewAdd').serialize();
            datastring += '&location='+location;
            datastring += '&type='+type;
            datastring += '&colorId='+color;
            datastring += '&box='+box+'&slot='+slot+'&row='+row+'&rack='+rack+'&shelf='+shelf;
            $.ajax({
                url: "inventoryNew.php",
                type: "POST",
                data: datastring,
                success: function (data) {
                    var json = JSON.parse(data);
                    if(json.error == '1'){
                        if(json.conflict == '1'){
                            var arr = json.name;
                            var html = '<h4 style="color: #0c00d2">';
                            jQuery.each(arr,function (i,item) {
                                if(item.location != null && item.type != null){
                                    html += 'Box already present in '+item.location+' , '+item.type+'<br/>';
                                } else if(item.location != null && item.type == null){
                                    html += 'Box already present in '+item.location +'<br/>';
                                } else {
                                    html += 'Box already present in another location<br/>';
                                }
                            });
                            html += '</h4>';
                            $('#message_add').html(html);
                            $('#message_add').show();
                        } else {
                            $('#message_add').html('<h4 style="color: red;">'+json.name+'</h4>');
                            $('#message_add').show();
                        }
                    } else {
                        if(json.flag == '1'){
                            window.location.replace("reportViewEdit.php?styleId=" + document.getElementById('styleId').value + "&colorId=" + color + "&unitId=" + unitId);
                            $("#message").html("<div class='successMessage'><strong>Inventory Added. Thank you.</strong></div>");
                        } else {
                            $('#message_add').html('<h4 style="color: red;">Server error please try again</h4>');
                            $('#message_add').show();
                        }
                    }
                }
            });
            return false;
        });
        $(document).ready(function () {
            $('#warehouse_link').hide();
        });
        $('#container_link').click(function (e) {
            $('#container_link').hide();
            $('#conveyor_link').show();
            $('#warehouse_link').show();
        });
        $('#conveyor_link').click(function (e) {
            $('#container_link').show();
            $('#conveyor_link').hide();
            $('#warehouse_link').show();
        });
        $('#warehouse_link').click(function (e) {
            $('#container_link').show();
            $('#conveyor_link').show();
            $('#warehouse_link').hide();
        });
        $('#warehouse_dropdown').select(function (e) {
            // alert(this.value);
        });
        $('#conveyor_loc_dropdown').change(function (e) {
            id = this.value;
            if (id != 0) {
                $.ajax({
                    url: 'conveyor_dropdown.php',
                    type: "post",
                    dataType: "json",
                    data: {
                        id: id
                    },
                    success: function (data) {
                        $('#conveyor_td').show();
                        $('#conveyor_dropdown').empty();
                        $('#conveyor_dropdown').append($('<option>',
                            {
                                value: 0,
                                data: "",
                                text: '--All conveyor--'
                            }));
                        $.each(data, function (i) {
                            $('#conveyor_dropdown').append($('<option>',
                                {
                                    value: data[i].id,
                                    text: data[i].conveyor
                                }));
                        });
                    }
                });
            }
            else {
                $('#conveyor_dropdown').empty();
            }

        });
        $('#container_loc_dropdown').change(function (e) {
            id = this.value;
            if (id != 0) {
                $.ajax({
                    url: 'container_dropdown.php',
                    type: "post",
                    dataType: "json",
                    data: {
                        id: id
                    },
                    success: function (data) {
                        $('#container_td').show();
                        $('#container_dropdown').empty();
                        $('#container_dropdown').append($('<option>',
                            {
                                value: 0,
                                text: '--All container--'
                            }));
                        $.each(data, function (i) {
                            $('#container_dropdown').append($('<option>',
                                {
                                    value: data[i].id,
                                    text: data[i].container
                                }));
                        });


                    }
                });
            }
            else {
                $('#container_dropdown').empty();
            }
        });
        $('.location_dropdown').change(function (e) {
            id = this.value;
            if (id != 0) {
                $.ajax({
                    url: 'warehouse_dropdown.php',
                    type: "post",
                    dataType: "json",
                    data: {
                        id: id
                    },
                    success: function (data) {
                        length = Object.keys(data).length;
                        //console.log(length);
                        $('#warehouse_td').show();
                        $('#warehouse_dropdown').empty();
                        $('#warehouse_dropdown').append($('<option>',
                            {
                                value: 0,
                                text: '--All warehouse--'
                            }));

                        $.each(data, function (i) {
                            $('#warehouse_dropdown').append($('<option>',
                                {
                                    value: data[i].id,
                                    text: data[i].warehouse
                                }));
                        });
                    }
                });
            }
            else {
                $('#warehouse_dropdown').empty();
            }
        });
        $('#new_conveyor_form').submit(function (e) {
            e.preventDefault();
            var styleId = document.getElementById('styleId').value;
            var colorId = document.getElementById('colorId').value;
            var slot = $('input[name="cv_slot"]').val();
            var locationId = $('#conveyor_loc_dropdown').val();
            var conveyorId = $('#conveyor_dropdown').val();
            if (slot == '' || conveyorId == '0' || conveyorId == null || locationId == '0' || locationId == null) {
                console.log(slot, conveyorId, locationId);
            }
            else {
                $.ajax({
                    url: 'add_unit_to_conveyor.php',
                    type: "post",
                    dataType: "json",
                    data: {
                        styleId: styleId,
                        colorId: colorId,
                        slot: slot,
                        locationId: locationId,
                        conveyorId: conveyorId,
                        type: 'slot'
                    },
                    success: function (data) {
                        if (data == 'slot not available') {
                            $('#conv_err_msg').empty();
                            $('#conv_err_msg').text(' -- slot not available');
                            $('#conv_err_msg').show();
                            $('#conv_err_msg').delay(3000).fadeOut();
                        }
                        else {
                            $('#close_warehouse_f').trigger("click");
                            window.location.replace("reportViewEdit.php?styleId=" + styleId + "&colorId=" + colorId + "&unitId=" + data);
                            $("#message").html("<div class='successMessage'><strong>Inventory Added. Thank you.</strong></div>");
                        }
                    }
                });
            }
        });
        $('#new_container_form').submit(function (e) {
            e.preventDefault();
            var styleId = document.getElementById('styleId').value;
            var colorId = document.getElementById('colorId').value;
            var unit = $('input[name="co_unit"]').val();
            var locationId = $('#container_loc_dropdown').val();
            var containerId = $('#container_dropdown').val();
            if (unit == '' || containerId == '0' || containerId == null || locationId == '0' || locationId == null) {
                console.log(unit, containerId, locationId);
            }
            else {
                //alert (locationId+' '+styleId);
                $.ajax({
                    url: 'add_unit_to_container.php',
                    type: "post",
                    dataType: "json",
                    data: {
                        styleId: styleId,
                        colorId: colorId,
                        unit: unit,
                        locationId: locationId,
                        containerId: containerId,
                        type: 'unit_c'
                    },
                    success: function (data) {
                        if (data == 'box not available') {
                            //alert(data);
                            $('#cont_err_msg').empty();
                            $('#cont_err_msg').text(' -- box not available');
                            $('#cont_err_msg').show();
                            $('#cont_err_msg').delay(3000).fadeOut();
                            //$('#cont_err_msg').empty();
                        }
                        else {
                            $('#close_warehouse_f').trigger("click");

                            window.location.replace("reportViewEdit.php?styleId=" + styleId + "&colorId=" + colorId + "&unitId=" + data);
                            $("#message").html("<div class='successMessage'><strong>Inventory Added. Thank you.</strong></div>");

                            //alert('success');
                        }
                    }
                });
            }
        });
        $('#warehouse_new_form').submit(function (e) {
            e.preventDefault();
            var styleId = document.getElementById('styleId').value;
            var colorId = document.getElementById('colorId').value;
            var location = $('#location_dropdown').val();
            var warehouse = $('#warehouse_dropdown').val();
            var rack = $('input[name="rack"]').val();
            var row = $('input[name="row"]').val();
            var shelf = $('input[name="shelf"]').val();
            var unit = $('input[name="unit"]').val();
            if (colorId == '' || styleId == '' || unit == '' || shelf == '' || row == '' || rack == '' || warehouse == '' || location == null || location == '0') {
                $('#close_warehouse_f').trigger("click");
                $("#message").html("<div class='errorMessage'><strong>All fields are mandatory. Please fill all fields and try later.</strong></div>").delay(3000).fadeOut();
            }
            else {
                $.ajax({
                    url: 'addNewInventory.php',
                    type: "post",

                    data: {
                        location: location,
                        warehouse: warehouse,
                        rack: rack,
                        row: row,
                        shelf: shelf,
                        unit: unit,
                        colorId: colorId,
                        styleId: styleId,
                        type: 'type_w'
                    },
                    success: function (data) {
                        if (data != null) {
                            if (data == "box not available") {
                                $('#warehouse_err_msg').empty();
                                $('#warehouse_err_msg').text(' -- unit not available');
                                $('#warehouse_err_msg').show();
                                $('#warehouse_err_msg').delay(3000).fadeOut();
                            }
                            else {
                                $('#close_warehouse_f').trigger("click");
                                window.location.replace("reportViewEdit.php?styleId=" + styleId + "&colorId=" + colorId + "&unitId=" + data);
                                $("#message").html("<div class='successMessage'><strong>Inventory Added. Thank you.</strong></div>");
                            }
                        }
                        else {
                            $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
                        }
                    }
                });
            }
        });
        function clear_form_data() {
            //empty warehouse form
            $('#warehouse_form_row').val('');
            $('#warehouse_form_rack').val('');
            $('#warehouse_form_shelf').val('');
            $('#warehouse_form_unit').val('');
            $('#warehouse_dropdown').empty();
            $('#warehouse_td').hide();
            // $('#warehouse_dropdown').val('0');
            // $('#warehouse_dropdown').text('--All warehouse--');
            $('#warehouse_err_msg').text('');
            $('#warehouse_err_msg').hide();
            document.getElementById("location_dropdown").options[0].selected = true;
            //empty container form
            $('#co_unit').val('');
            $('#container_dropdown').empty();
            $('#container_td').hide();
            $('#cont_err_msg').empty();
            $('#cont_err_msg').hide();
            document.getElementById("container_loc_dropdown").options[0].selected = true;
            //empty conveyor form
            $('#cv_slot').val('');
            $('#conveyor_dropdown').empty();
            $('#conveyor_td').hide();
            $('#conv_err_msg').empty();
            $('#conv_err_msg').hide();
            document.getElementById("conveyor_loc_dropdown").options[0].selected = true;

        };
        // Get the modal
        var modal = document.getElementById('myModal');
        // Get the button that opens the modal
        var btn = document.getElementById("addinventory_new");
        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];
        // When the user clicks the button, open the modal
        btn.onclick = function () {
            modal.style.display = "block";
        };
        // When the user clicks on <span> (x), close the modal
        span.onclick = function () {
            modal.style.display = "none";
            clear_form_data();
        };
        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
<script>
        $('#newInventory').click(function (e) {
            $('#inventory_form').show();
            $('#warehouse_form').show();
        });
        function UpdateNew() {
            var room = $('#up_room').val();
            var rack = $('#up_rack').val();
            if (rack == '') {
                alert("Please Provide a rack");
                return false;
            }
            var row = $('#up_row').val();
            if (row == '') {
                alert("Please Provide a row");
                return false;
            }
            var self = $('#up_shelf').val();
            if (self == '') {
                alert("Please Provide a self");
                return false;
            }
            //alert(row);
            $.ajax({
                url: 'editRoom.php',
                type: "post",
                data: {
                    room: room,
                    rack: rack,
                    row: row,
                    self: self,
                    unitId: "<?php echo $_REQUEST['unitId'];?>",
                    styleId: document.getElementById('styleId').value
                },
                success: function (response) {
                    if (response == 1) {
                        alert("Updated");
                        location.reload();
                    } else {
                        alert("Not Updated! Please Try Again After Some Time");
                    }
                }
            });
            // $(location).attr('href', "updateInventory.php?styleId=" + document.getElementById('styleId').value + "&colorId=" + document.getElementById('colorId').value + "<?php if (isset($_REQUEST['unitId']) && $_REQUEST['unitId'] != '') echo '&unitId=' . $_REQUEST['unitId'];?>");
        };
        function Update() {
            room = $('#updateroom').val();
            // if(room == '') {
            //     alert("Please Provide a Room");
            //     return false;
            // }
            var rack = $('#updaterack').val();
            if (rack == '') {
                alert("Please Provide a rack");
                return false;
            }
            var row = $('#updaterow').val();
            if (row == '') {
                alert("Please Provide a row");
                return false;
            }
            var self = $('#updateshelf').val();
            if (self == '') {
                alert("Please Provide a self");
                return false;
            }
            //alert(row);
            $.ajax({
                url: 'editRoom.php',
                type: "post",
                data: {
                    room: room,
                    rack: rack,
                    row: row,
                    self: self,
                    unitId: "<?php echo $_REQUEST['unitId'];?>",
                    styleId: document.getElementById('styleId').value
                },
                success: function (response) {
                    if (response == 1) {
                        alert("Updated");
                        location.reload();
                    } else {
                        alert("Not Updated! Please Try Again After Some Time");
                    }
                }
            });
            // $(location).attr('href', "updateInventory.php?styleId=" + document.getElementById('styleId').value + "&colorId=" + document.getElementById('colorId').value + "<?php if (isset($_REQUEST['unitId']) && $_REQUEST['unitId'] != '') echo '&unitId=' . $_REQUEST['unitId'];?>");
        };
        function Delete() {
            if (confirm("Are you Sure you want to delete this unit") == true) {
                $.ajax({
                    url: "deleteInventory.php",
                    type: "post",
                    data: {
                        styleId: document.getElementById('styleId').value,
                        colorId: document.getElementById('colorId').value,
                        unitId: "<?php echo $_REQUEST['unitId'];?>"
                    },
                    success: function (response) {
                        if (response == 1) {
                            alert("unit Deleted SuccessFully");
                            window.location.replace("reportViewEdit.php?styleId=" + document.getElementById('styleId').value + "&colorId=" + document.getElementById('colorId').value);
                        } else if(response == 2) {
                            alert("unit Not Deleted Please Empty the unit first");
                        } else {
                            alert('Internal server error please try again after some time');
                        }
                    }
                });
            } else {
                console.log('cancel');
            }
        };
        function mergrButton(){
            $('#mergeBoxButton').hide();
            $('#margeBoxId').show();
        };
</script>
<script type="text/javascript">
        var unique_id = 0;
        function print_content(stylId, loc, unitId) {
            if (stylId == 'null' || unitId == 'null') {
                alert('error');
            } else {
                var clrId = $('#color option[selected="selected"]').val();
                window.location.replace("print.php" + '?styleId=' + stylId + '&colorId=' + clrId + '&unit=' + unitId + '&location=' + loc );
            }
        };
        function AddRow(type, cellId, value) {
            switch (type) {
                case 'main': {
                    var trTop = document.getElementById('mainSizeTop');
                    var trBottom = document.getElementById('mainSizeBottom');
                    var tr2Bottom = document.getElementById('adjBottom');
                    var cell = trTop.insertCell(cellId);
                    cell.className = 'gridHeaderReport';
                    cell.innerHTML = value;
                    cell = trBottom.insertCell(cellId);
                    cell.className = 'gridHeaderReport';
                    cell.innerHTML = value;
                    cell = tr2Bottom.insertCell(cellId);
                    cell.className = 'txBxWhite';
                    var txtunit = document.createElement("input");
                    txtunit.type = "text";
                    txtunit.className = "txBxWhite";
                    txtunit.value = "";
                    cell.appendChild(txtunit);
                    break;
                }
                case 'price': {
                    var trPrice = document.getElementById('priceTop');
                    var cell = trPrice.insertCell(cellId);
                    cell.className = 'gridHeaderReportGrids2';
                    var txtunit = document.createElement("input");
                    txtunit.type = "text";
                    txtunit.className = "txBxWhite";
                    txtunit.name = 'price[]';
                    txtunit.value = value;
                    cell.appendChild(txtunit);
                    break;
                }
                case 'column': {
                    var trc = document.getElementById('columnSize');
                    var cell = trc.insertCell(cellId);
                    cell.className = 'gridHeaderReportBlue';
                    cell.innerHTML = value;
                    break;
                }
                case 'dummy': {
                    var trd = document.getElementById('dummy1');
                    var cell = trd.insertCell(cellId);
                    cell.className = 'gridHeaderReportGrids2';
                    cell.innerHTML = value;
                    trd = document.getElementById('dummy2');
                    cell = trd.insertCell(cellId);
                    cell.className = 'gridHeaderReportGrids2';
                    cell.innerHTML = value;
                    break;
                }
            }
        };
        function AddQty(trId, type, cellId, i, j, data, locIndex, rowIndex, qty, invIdValue) {
                //alert(qty);
            // console.log(i,j,qty);
            //alert(data);
            //alert(invIdValue);
            switch (type) {
                case 'qty': {
                    var abc = "Abc:hfdfh \nBcd:jhyf";
                    var tr = document.getElementById(trId);
                    var cell = tr.insertCell(cellId);
                    var txtunit = document.createElement("input");
                    cell.className = 'gridHeaderReportGrids allvaluesingrid';
                    txtunit.type = "text";
                    txtunit.name = "qty[" + locIndex + "][" + rowIndex + "][]";
                    txtunit.className = "txBxGrey eachcell";
                    txtunit.id = 'unique_' + unique_id++;

                    if (data != -1) {
                        data = data.replace(/::/g, '\n');
                        txtunit.title = data;
                    }

                    txtunit.value = qty;
                    cell.appendChild(txtunit);

                    txtunit = document.createElement("input");
                    txtunit.type = "hidden";
                    txtunit.name = "invId[" + locIndex + "][" + rowIndex + "][]";
                    txtunit.value = invIdValue;
                    cell.appendChild(txtunit);
                    /*if(invIdValue > 0 && qty > 0)
                     {
                     a = document.createElement("a");
                     a.setAttribute("href", "#");
                     img = document.createElement("img");
                     img.width="15px";
                     img. height="14px";
                     img.className = "imgRght";*/
                    //img.src="<?php// echo $mydirectory;?>/images/Btn_edit.gif";
                    /*img.setAttribute("onclick",'QtyDblClick('+invIdValue+');');
                     a.appendChild(img);
                     cell.appendChild(a);
                     }*/
                    break;
                }
                case 'qtyDummy': {
                    var trd = document.getElementById('qtyDummy' + locIndex);
                    if (trd != null) {
                        //alert(trd+' '+locIndex);
                        var cell = trd.insertCell(cellId);
                        cell.className = 'gridHeaderReportGrids2';
                        cell.innerHTML = "&nbsp;";
                    }


                    break;
                }
            }
        }
        function StoreInitialValues(locIndex, rowIndex, txtValue, newQty) {
            var td = document.getElementById('hdnVar');
            var element1 = document.createElement("input");
            element1.type = "hidden";
            element1.name = "hdnqty[" + locIndex + "][" + rowIndex + "][]";
            element1.value = txtValue;
            var element2 = document.createElement("input");
            element2.type = "hidden";
            element2.name = "hdnNewQty[" + locIndex + "][" + rowIndex + "][]";
            element2.value = newQty;
            td.appendChild(element1);
            td.appendChild(element2);

        }
        function QtyDblClick(inventoryId) {

            $(location).attr('href', "storage.php?styleId=" + document.getElementById('styleId').value + "&colorId=" + document.getElementById('colorId').value + "&invId=" + inventoryId + "<?php if (isset($_REQUEST['unitId']) && $_REQUEST['unitId'] != '') echo '&unitId=' . $_REQUEST['unitId'];?>");
        }
</script>
<script type="text/javascript">
        $(document).ready(function () {
            <?php if(!isset($_REQUEST['unitId']) || $_REQUEST['unitId'] == 0): ?>
            $('.tool').bind({
                mouseenter: function(){
                    $(this).next('.tooltext').show();
                },
                mouseleave : function(){
                    $('.tooltext').hide();

                }
            });
            $('.tooltext').bind({
                mouseenter: function(){
                    $(this).show();
                },
                mouseleave : function(){
                    $('.tooltext').hide();
                }
            });
            <?php endif; ?>
            if ($('#unit_num').val() == 0 || $('#unit_num').val() == 'undefined') {
                $('#update_inventory').hide();
                $('#update_inventory_new').hide();
                $('#print').hide();
            }
            if ($('#unit_num').val() == 0 || $('#unit_num').val() == 'undefined') {
                $('#view_details').hide();
                $("#hide").css("display", "inline-block");
            } else {
                $("#hide").css("display", "none");
                $('#addinventory_new').hide();
            }
            <?php
            if ($data_style['scaleNameId'] != "") {
                $sizeIndex = 0;
                $columnSize = 0;


                for ($i = 0; $i < count($data_mainSize); $i++) {
                    $invPrice = 0;
                    $found = 0;

                    echo 'AddRow("main",' . $sizeIndex . ',"' . $data_mainSize[$i]['scaleSize'] . '");';
                    for ($j = 0; $j < count($data_inv); $j++) {
                        if ($data_inv[$j]['sizeScaleId'] == $data_mainSize[$i]['mainSizeId']) {
                            if ($data_inv[$j]['price'] != "" || $data_inv[$j]['price'] > 0) {
                                $invPrice = 1;
                                echo 'AddRow("price",' . $sizeIndex . ',"' . $data_inv[$j]['price'] . '");';
                            }
                            break;
                        }
                    }
                    if (!$invPrice) {
                        echo 'AddRow("price",' . $sizeIndex . ',"' . $data_style['price'] . '");';
                    }
                    if ($i < count($data_opt2Size)) {
                        echo 'AddRow("column",' . $columnSize++ . ',"' . $data_opt2Size[$i]['opt2Size'] . '");';
                    }
                    echo 'AddRow("dummy",' . $sizeIndex . ',"&nbsp;");';
                    $sizeIndex++;
                }
                if ($sizeIndex)
                    echo "document.getElementById('mainCount').value = $sizeIndex;";
            }

            $locIndex = 0;
            $rowIndex = 0;
            $mainIndex = 0;
            if ($locArr[0] > 0 && $locArr[0] != "") {
                // var_dump(count($locArr));
                // exit();
                for ($i = 0; $i < count($locArr); $i++, $locIndex++) {
                    $rowIndex = 0;
                    //echo ">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>".implode(';', $data_inv[$i])." >>>>>>>>>>>>>>   ";
                    if (count($data_opt1Size) > 0) {
                        for ($j = 0; $j < count($data_opt1Size); $j++) {
                            InsertQty($data_mainSize, $data_inv, $data_opt1Size[$j]['opt1SizeId'], $locArr[$i], $locIndex, $rowIndex, $_store);
                            $rowIndex++;
                        }
                    } else {
                        InsertQty($data_mainSize, $data_inv, 0, $locArr[$i], $locIndex, $rowIndex, $_store);
                        $rowIndex++;
                    }
                    echo 'AddQty("dummy","qtyDummy",' . $mainIndex . ',0,0,0,' . $locIndex . ',' . $rowIndex . ',0,0);';

                    //trId,type,cellId,i,j,data,locIndex,rowIndex,qty,invIdValue
                }
            }
            if ($locIndex)
                echo "document.getElementById('locCount').value = $locIndex;";
            if ($rowIndex)
                echo "document.getElementById('rowCount').value = $rowIndex;";



            function InsertQty($data_mainsize, $data_inv, $rowSizeId, $locId, $locIndex, $rowIndex, $store)
            {
                $mainIndex = 0;
                for ($i = 0; $i < count($data_mainsize); $i++) {
                    $invFound = 0;
                    for ($j = 0; $j < count($data_inv); $j++) {
                        if ($rowSizeId > 0) {

                            if (($data_inv[$j]['sizeScaleId'] == $data_mainsize[$i]['mainSizeId']) &&
                                ($locId == $data_inv[$j]['locationId']) &&
                                ($rowSizeId == $data_inv[$j]['opt1ScaleId'])
                            ) {

                                $invFound = 1;

                                if ($data_inv[$j]['inventoryId'] != "") {
                                    if ($data_inv[$j]['quantity'] != "") {
                                        echo "StoreInitialValues($locIndex,$rowIndex,'" . $data_inv[$j]['quantity'] . "','" . $data_inv[$j]['newQty'] . "');";
                                        // echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'","qty",'.$mainIndex.','.$locIndex.','.$rowIndex.',"'.$data_inv[$j]['quantity'].'",'.$data_inv[$j]['inventoryId'].');';

                                        $data = "";
                                        if (!isset($_GET['unitId']) || $_GET['unitId'] == '0') {
                                            for ($ii = 0; $ii < count($store); $ii++) {

                                                //echo($_store['mainSizeId'].",".);

                                                if ($store[$ii]['mainSizeId'] == $data_mainsize[$i]['mainSizeId'] &&
                                                    $store[$ii]['opt1ScaleId'] == $data_inv[$j]['opt1ScaleId']
                                                ) {
                                                    $data .= 'unit=' . $store[$ii]['unit'] . " : " . $store[$ii]['quantity'] . "::";
                                                }
                                            }
                                        } else {
                                            $data = "-1";
                                        }
                                        // echo "***************************************************";
                                        // var_dump($data);
                                        // //var_dump($data_inv[$j]['quantity']);
                                        // echo "***************************************************";
                                        // exit();
                                        if ($data == '')
                                            $data = "-1";

                                        echo 'AddQty("qty_' . $locIndex . '_' . $rowIndex . '",
                                                    "qty",
                                                    ' . $mainIndex . ',
                                                    ' . $i . ',
                                                    ' . $j . ',
                                                    "' . $data . '",
                                                    ' . $locIndex . ',
                                                    ' . $rowIndex . ',
                                                    "' . $data_inv[$j]['quantity'] . '",
                                                    ' . $data_inv[$j]['inventoryId'] . ');';

                                        //echo "*****************************************************";


                                    } else {
                                        echo "StoreInitialValues($locIndex,$rowIndex,0,'" . $data_inv[$j]['newQty'] . "');";
                                        // echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'","qty",'.$mainIndex.','.$locIndex.','.$rowIndex.',0,'.$data_inv[$j]['inventoryId'].');';
                                        $data = "-1";
                                        echo 'AddQty("qty_' . $locIndex . '_' . $rowIndex . '",
                                                "qty",
                                                ' . $mainIndex . ',
                                                ' . $i . ',
                                                ' . $j . ',
                                                "' . $data . '",
                                                ' . $locIndex . ',
                                                ' . $rowIndex . ',
                                                0,
                                                ' . $data_inv[$j]['inventoryId'] . ');';
                                    }
                                } else {
                                    echo "StoreInitialValues($locIndex,$rowIndex,0,0);";
                                    // echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'","qty",'.$mainIndex.','.$locIndex.','.$rowIndex.',0,0);';


                                    $data = "-1";
                                    echo 'AddQty("qty_' . $locIndex . '_' . $rowIndex . '",
                                            "qty",
                                            ' . $mainIndex . ',
                                            ' . $i . ',
                                            ' . $j . ',
                                            "' . $data . '",
                                            ' . $locIndex . ',
                                            ' . $rowIndex . ',
                                            0,
                                            0);';
                                }
                                break;
                            }

                        } else {
                            if ($data_inv[$j]['sizeScaleId'] == $data_mainsize[$i]['mainSizeId'] && ($locId == $data_inv[$j]['locationId']) && ("" == $data_inv[$j]['opt1ScaleId'])) {
                                $invFound = 1;
                                if ($data_inv[$j]['inventoryId'] != "") {
                                    if ($data_inv[$j]['quantity'] != "") {
                                        echo "StoreInitialValues($locIndex,$rowIndex,'" . $data_inv[$j]['quantity'] . "','" . $data_inv[$j]['newQty'] . "');";
                                        //echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'","qty",'.$mainIndex.','.$locIndex.','.$rowIndex.',"'.$data_inv[$j]['quantity'].'",'.$data_inv[$j]['inventoryId'].');';


                                        $data = "-1";
                                        echo 'AddQty("qty_' . $locIndex . '_' . $rowIndex . '",
                                            "qty",
                                            ' . $mainIndex . ',
                                            ' . $i . ',
                                            ' . $j . ',
                                            "' . $data . '",
                                            ' . $locIndex . ',
                                            ' . $rowIndex . ',
                                            "' . $data_inv[$j]['quantity'] . '",
                                            ' . $data_inv[$j]['inventoryId'] . ');';


                                    } else {
                                        echo "StoreInitialValues($locIndex,$rowIndex,0,'" . $data_inv[$j]['newQty'] . "');";
                                        //echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'","qty",'.$mainIndex.','.$locIndex.','.$rowIndex.',0,'.$data_inv[$j]['inventoryId'].');';

                                        $data = "-1";
                                        echo 'AddQty("qty_' . $locIndex . '_' . $rowIndex . '",
                                            "qty",
                                            ' . $mainIndex . ',
                                            ' . $i . ',
                                            ' . $j . ',
                                            "' . $data . '",
                                            ' . $locIndex . ',
                                            ' . $rowIndex . ',
                                            0,
                                            ' . $data_inv[$j]['inventoryId'] . ');';
                                    }

                                } else {
                                    echo "StoreInitialValues($locIndex,$rowIndex,0,0);";
                                    //echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'","qty",'.$mainIndex.','.$locIndex.','.$rowIndex.',0,0);';

                                    $data = "-1";
                                    echo 'AddQty("qty_' . $locIndex . '_' . $rowIndex . '",
                                            "qty",
                                            ' . $mainIndex . ',
                                            ' . $i . ',
                                            ' . $j . ',
                                            "' . $data . '",
                                            ' . $locIndex . ',
                                            ' . $rowIndex . ',
                                            0,
                                            0);';


                                }
                                break;
                            }
                        }
                    }
                    if (!$invFound) {
                        echo "StoreInitialValues($locIndex,$rowIndex,0,0);";
                        // echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'","qty",'.$mainIndex.','.$locIndex.','.$rowIndex.',0,0);';

                        $data = "-1";
                        echo 'AddQty("qty_' . $locIndex . '_' . $rowIndex . '",
                                "qty",
                                ' . $mainIndex . ',
                                ' . $i . ',
                                ' . $j . ',
                                "' . $data . '",
                                ' . $locIndex . ',
                                ' . $rowIndex . ',
                                0,
                                0);';


                    }


                    //var_dump($xx);

                    $mainIndex++;
                }
            }
            ?>
            $("#color").change(function () {

                $("#colorid_mail").val($("#color").val());
                PostRequest();

            });
            $("#unit_num").change(function ()   {
                $("#unitId_mail").val($("#unit_num").val());
                PostRequest();
            });
            $(".slot_num").change(function () {
                $("#unit_num").val($(this).val());
                PostRequest();
            });
            $("#sConveyor").change(function () {
                PostRequest();
            });
            $("#main_location").change(function () {
                PostRequest();
            });
            $('#mergeBox').change(function (){
                var styleId = document.getElementById('styleId').value;
                var colorId = document.getElementById('colorId').value;
                var unit = "<?php echo $_REQUEST['unitId'];?>";
                var newUnit = $('#mergeBox').val();
                if(newUnit != ''){
                    if (confirm("Are you Sure you want to Merge \n\n\t"+unit+"\n\t\t to \n\t"+newUnit) == true) {
                        $.ajax({
                            url: "mergeInventory.php",
                            type: "post",
                            data: {
                                styleId: styleId,
                                colorId: colorId,
                                unit: unit,
                                newUnit: newUnit
                            },
                            success: function (response) {
                                if (response == 1) {
                                    alert("unit Merged SuccessFully");
                                    window.location.replace("reportViewEdit.php?styleId=" + document.getElementById('styleId').value);
                                } else {
                                    alert('Internal server error please try again after some time \n'+response);
                                }
                            }
                        });
                    } else {
                        console.log('cancel');
                    }
                }
            });
            function PostRequest() {
                //alert('PostRequest');
                var stylId = document.getElementById('styleId').value;
                var clrId = 0;
                if ($("#color").val() != undefined) {
                    clrId = $("#color").val();
                }

                var unitId = 0;
                if ($("#unit_num").val() != undefined) {
                    unitId = $("#unit_num").val();
                }
                var conveyor = 0;
                if ($("#sConveyor").val() != undefined) {
                    conveyor = $("#sConveyor").val();
                }
                var locationMain = 0;
                if($('#main_location').val() != undefined){
                    locationMain = $('#main_location').val();
                }
                var dataString = 'styleId=' + stylId + '&colorId=' + clrId + '&unitId=' + unitId + '&conveyor=' + conveyor + '&location=' + locationMain;
                //alert(dataString);
                $.ajax
                ({
                    type: "POST",
                    url: "reportOptSubmit.php",
                    data: dataString,
                    dataType: "json",
                    success: function (data) {
                        //console.log(data);
                        if (data != null) {
                            if (data.styleId != null) {
                                $(location).attr('href', 'reportViewEdit.php?' + dataString);
                            }
                            else {
                                $("#message").html("<div class='errorMessage'><strong>No Inventory found with sample color selected!</strong></div>");
                            }
                        }
                    }
                });
            }
        });
    </script>
<script type="text/javascript">
        function init_dw_Scroll() {
            var wndo = new dw_scrollObj('wn', 'lyr1');
            wndo.setUpScrollControls('scrollLinks');
            var wndo = new dw_scrollObj('wn2', 'lyr2');
            wndo.setUpScrollControls('scrollLinks2');
            var wndo1 = new dw_scrollObj('wn3', 'lyr3');
            wndo1.setUpScrollControls('scrollLinks3');
            var wndo1 = new dw_scrollObj('wn4', 'lyr4');
            wndo1.setUpScrollControls('scrollLinks4');
        };
        // if code supported, link in the style sheet and call the init function onload
        if (dw_scrollObj.isSupported()) {
            //dw_Util.writeStyleSheet('css/scroll.css');
            dw_Event.add(window, 'load', init_dw_Scroll);
        }
</script>
<script type="text/javascript">
        $(function () {
            $("#inventoryFormNew").submit(function (e) {
                var location_id = $('#_location_id').val();
                var location_details_id = $('#_location_details_id').val();
                var unitId = 0;
                if ($("#unit_num").val() != undefined) {
                    unitId = $("#unit_num").val();
                }
                var row = "<?php echo $data_product[0]['row']; ?>";
                var room = "<?php echo $data_product[0]['room']; ?>";
                var shelf = "<?php echo $data_product[0]['shelf']; ?>";
                var rack = "<?php echo $data_product[0]['rack']; ?>";
                dataString = $("#inventoryFormNew").serialize();
                dataString += "&type=e";
                dataString += "&unitId=" + unitId;
                dataString += "&location_id=" + location_id;
                dataString += "&location_details_id=" + location_details_id;
                flag = 0;
                for (i = 0; ; i++) {
                    var data = $('#unique_' + i).val();
                    if (typeof(data) != "undefined" && data !== null) {
                        if (data < 0) {
                            flag = 1;
                            break;
                        }
                    }
                    else {
                        break;
                    }
                }
                if (unitId == '') {
                    alert('unitId is null! not submiting the form');
                }
                else if (flag == 1) {
                    alert('Negative value not accepted!');
                }
                else {
                    $("#message").html("<div class='errorMessage'><strong>Processing, Please wait...!</strong></div>");
                    $.ajax({
                        type: "POST",
                        url: "invReportSubmit.php",
                        data: dataString,
                        dataType: "json",
                        success: function (data) {
                            if (data != null) {
                                if (data[0].name || data[0].error) {
                                    $("#message").html("<div class='errorMessage'><strong>Sorry, " + data[0].name + data[0].error + "</strong></div>");
                                    if (data[0].flag) {
                                        $.ajax({
                                            url: "newStorageSubmit.php?type=a&styleId=" + document.getElementById('styleId').value + "&colorId=" + document.getElementById('colorId').value + "&unitId=" + unitId + "&row=" + row + "&rack=" + rack + "&room=" + room + "&shelf=" + shelf,
                                            type: "GET",
                                            success: function (data) {
                                                //return false;
                                                if (data != null) {
                                                    if (data.name || data.error) {
                                                        $("#message").html("<div class='errorMessage'><strong>" + data.name + data.error + "</strong></div>");
                                                    }
                                                    else {
                                                        location.reload(true);
                                                        $("#message").html("<div class='successMessage'><strong>Storage Updated. Thank you.</strong></div>");
                                                    }
                                                }
                                                else {
                                                    $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
                                                }
                                            }
                                        });
                                    }
                                } else {
                                    if (data[0].flag) {
                                        $("#message").html("<div class='successMessage'><strong>Inventory Quantity Updated. Thank you.</strong></div>");
                                        $.ajax({
                                            url: "newStorageSubmit.php?type=a&styleId=" + document.getElementById('styleId').value + "&colorId=" + document.getElementById('colorId').value + "&unitId=" + unitId + "&row=" + row + "&rack=" + rack + "&room=" + room + "&shelf=" + shelf,
                                            type: "GET",
                                            success: function (data) {
                                                //return false;
                                                if (data != null) {
                                                    if (data.name || data.error) {
                                                        $("#message").html("<div class='errorMessage'><strong>" + data.name + data.error + "</strong></div>");
                                                    }
                                                    else {
                                                        location.reload(true);
                                                        $("#message").html("<div class='successMessage'><strong>Storage Updated. Thank you.</strong></div>");
                                                    }
                                                }
                                                else {
                                                    $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
                                                }
                                            }
                                        });
                                        //$(location).attr('href',"newStorageSubmit.php?type=a&styleId="+document.getElementById('styleId').value+"&colorId="+document.getElementById('colorId').value+"&unitId="+unitId+"&row="+row+"&rack="+rack+"&room="+room+"&shelf="+shelf);
                                    } else {
                                        $("#message").html("<div class='successMessage'><strong> All Inventories are up to date...</strong></div>");
                                    }
                                }
                            } else {
                                    $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
                            }
                        }
                    });
                }
                return false;
            });
        });
        $('.clicked').keyup(function () {
            var id = this.id;
            var change = id.slice(5);
            $('#' + change).val(1);
        });
        $('.clicked_new').keyup(function () {
            var id = this.id;
            var change = id.slice(5);
            $('#h' + change).val(1);
        });
        $('#box_add').keyup(function () {
            $('#message_add').hide();
        });
        function delAllQnts() {
            var url = location.href;
            if (!url.contains("colorId"))
                url += "&colorId=" + $("#color").val();
            if (!url.contains("del"))
                url += "&del=true";
            location.href = url;
        }
        function main_inv() {
            location.href = './reportViewEdit.php?styleId=' + $('#styleId_mail').val() + '&colorId=' + $('#colorid_mail').val();
        }
        function addInventory() {
            $('#inventoryDetails').show();
            $('#hide').hide();
        }
        function cancelInventory() {
            $('#hide').show();
            $('#inventoryDetails').hide();
        }
        function addNewInventory() {
            var unitId = $("#newunit").val();
            if (unitId == '') {
                alert("Please Provide a unit name");
                return false;
            }
            var room = $('#newRoom').val();
            if (room == '') {
                alert("Please Provide a room");
                return false;
            }
            var rack = $('#newRack').val();
            if (rack == '') {
                alert("Please provide a Rack");
                return false;
            }
            var row = $('#newRow').val();
            if (row == '') {
                alert("Please provide a Row");
                return false;
            }
            var shelf = $("#newShelf").val();
            if (shelf == '') {
                alert("Please provide a Shelf");
                return false;
            }
            var styleId = document.getElementById('styleId').value;
            var colorId = document.getElementById('colorId').value;
            $.ajax({
                url: "addNewInventory.php",
                type: "POST",
                data: {
                    unit: unitId,
                    room: room,
                    row: row,
                    shelf: shelf,
                    rack: rack,
                    styleId: styleId,
                    colorId: colorId
                },
                success: function (data) {
                    if (data == 1) {
                        $(location).attr('href', "reportViewEdit.php?styleId=" + styleId + "&colorId=" + colorId + "&unitId=" + unitId);
                        $("#message").html("<div class='successMessage'><strong>Inventory Added. Thank you.</strong></div>");
                    } else {
                        $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
                    }
                }
            })

        }
</script>
<?php require('../../trailer.php'); ?>
