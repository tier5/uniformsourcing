<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<?php
    require('Application.php');
    require('../../header.php');
    //$sql='select st."styleId" st."styleNumber",st."scaleNameId",st."scaleNameId" from "tbl_invStyle" st left join tbl_inventory inv on st."styleId"=inv."styleId" where st."styleId"='.$_GET['StyleId'];
    
    
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
    
    
    }
    
    $sql = 'select "styleId","sex","garmentId","barcode", "styleNumber", "scaleNameId", price, "locationIds" from "tbl_invStyle" where "styleId"=' . $styleId;
    if (!($result = pg_query($connection, $sql))) {
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit;
    }
    $row = pg_fetch_array($result);
    $data_style = $row; //--------------------------- data style----------------
    
    //echo "<pre>";print_r($data_style['sex']);
    
    pg_free_result($result);
    $query2 = 'Select * from "tbl_invColor" where "styleId"=' . $data_style['styleId'];
    if (!($result2 = pg_query($connection, $query2))) {
        print("Failed OptionQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row2 = pg_fetch_array($result2)) {
        $data_color[] = $row2; // -------------------------- data_color ---------
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
        }//---------------------------data_mainSize-----------------------------
        pg_free_result($result2);
        $query2 = 'Select "sizeScaleId" as "opt1SizeId", "opt1Size" from "tbl_invScaleSize" where "scaleId"=' . $data_style['scaleNameId'] . ' and "opt1Size" IS NOT NULL  and "opt1Size" <>\'\' order by "opt1Order","sizeScaleId"';
        if (!($result2 = pg_query($connection, $query2))) {
            print("Failed OptionQuery: " . pg_last_error($connection));
            exit;
        }
        while ($row2 = pg_fetch_array($result2)) {
            $data_opt1Size[] = $row2;
        }//------------------------data_opt1Size----------------------------
    
    
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
    
    
    
        
        $sql = 'select distinct unit from "tbl_invStorage" where "styleId"=' . $_GET['styleId'];
    
        //$sql = 'select distinct unit from "tbl_invStorage" where "styleId"=' . $_GET['styleId'];
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
            $data_storage[] = $row_cnt9;
        }
        pg_free_result($result_cnt9);
    
        // echo "<pre>";print_r($data_storage);
        // exit();
    
    }
    
    $totalScale = count($data_mainSize);
    $tableWidth = 0;
    
    
    
    $tableWidth = $totalScale * 100;
    
    
    //query changed -- added 'where "styleId"='.$_GET['styleId']';
    
    $sql = 'select "inventoryId",quantity,"newQty","isStorage","warehouse_id" from "tbl_inventory" where "styleId"='.$_GET['styleId'];
    
    if (!($result = pg_query($connection, $sql))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($result)) {
        $data_inv[] = $row;
    }
    
    
    
    
    
    for ($i = 0; $i < count($data_inv); $i++) {
        if ($data_inv[$i]['newQty'] > 0) 
        {
            if (($data_inv[$i]['quantity'] != "" && $data_inv[$i]['quantity'] > 0)) 
            {
                $sql = 'update "tbl_inventory" set "isStorage"=1 ,"newQty"=0';
                if (!($result = pg_query($connection, $sql))) {
                    print("Failed invUpdateQuery: " . pg_last_error($connection));
                    exit;
                }
            } else if (($data_inv[$i]['quantity'] == "" || $data_inv[$i]['quantity'] == 0)) 
            {
                $sql = 'Delete from "tbl_inventory" where "inventoryId"=' . $data_inv[$i]['inventoryId'];
                if (!($result = pg_query($connection, $sql))) {
                    print("Failed deleteInvQuery: " . pg_last_error($connection));
                    exit;
                }
            }
        }
    }
    
    
    
    if (count($data_color) > 0) {
        if ($search != "") 
        {
            $query = 'select inv."inventoryId", inv."sizeScaleId", inv.price, inv."locationId",inv."opt1ScaleId", inv."opt2ScaleId"';
            if (isset($_GET['unitId']) && $_GET['unitId'] != '0') {
                $query .= ',st."wareHouseQty" as st_quantity ';
            }
    
            $query .= ',inv.quantity, inv."newQty" from "tbl_inventory" as inv ';
            if (isset($_GET['unitId']) && $_GET['unitId'] != '0') {
                $query .= ' left join "tbl_invStorage" as st on st."invId"=inv."inventoryId" ';
            }
            $query .= ' where inv."styleId"=' . $data_style['styleId'] . ' and inv."isActive"=1' . $search . ' order by "inventoryId"';
        } 
        else {
            $clrId = $data_color[0]['colorId'];
            $query = 'select "inventoryId", "sizeScaleId", price, "locationId","opt1ScaleId", "opt2ScaleId", quantity, "newQty" from "tbl_inventory" where "styleId"=' . $data_style['styleId'] . ' and "colorId"=' . $data_color[0]['colorId'] . '  and "isActive"=1 order by "inventoryId"';
        }
        // echo $query;
        // exit();
        if (!($result = pg_query($connection, $query))) {
            print("Failed invQuery: " . pg_last_error($connection));
            exit;
        }
        while ($row = pg_fetch_array($result)) {
            $data_inv[] = $row;
        }
       
    
    
        pg_free_result($result);
        if (count($data_inv) > 0) 
        {
            for ($l = 0; $l < count($data_inv); $l++) 
            {
                if (isset($data_inv[$l]['st_quantity']) && $data_inv[$l]['st_quantity'] != '')
                {
                    $data_inv[$l]['quantity'] = $data_inv[$l]['st_quantity'];
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
        // echo "<pre>"; print_r($all_location_inv);
        // exit();
    
        $location_string = " ";
        foreach ($all_location_inv as $key => $value) {
            if($location_string == " ") 
                $location_string .= $value['locationId'];
            else
                $location_string .= ','.$value['locationId'];
        }
        // echo "<pre>"; print_r($location_string);
        // exit();
        $sql = 'select name,"locationId" from "tbl_invLocation" where "locationId" in ('.$location_string.') order by "locationId"';
    
        $warehouse_info;
        if (!($result = pg_query($connection, $sql))) {
            print("Failed invQuery: " . pg_last_error($connection));
            exit;
        }
        while ($row = pg_fetch_array($result)) {
            $warehouse_info[] = $row;
        }
        // echo "<pre>"; print_r($warehouse_info);
        // exit();
    
    
    
    
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
            if($location_string == " ") 
                $location_string .= $value['locationId'];
            else
                $location_string .= ','.$value['locationId'];
        }
        $sql = 'select name,"locationId" from "tbl_invLocation" where "locationId" in ('.$location_string.') order by "locationId"';
    
        $containers_location;
        if (!($result = pg_query($connection, $sql))) {
            print("Failed invQuery: " . pg_last_error($connection));
            exit;
        }
        while ($row = pg_fetch_array($result)) {
            $containers_location[] = $row;
        }
        // echo "<pre>"; print_r( $containers_location);
        // exit();
    
    
    
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
            if($location_string == " ") 
                $location_string .= $value['locationId'];
            else
                $location_string .= ','.$value['locationId'];
        }
        $sql = 'select name,"locationId" from "tbl_invLocation" where "locationId" in ('.$location_string.') order by "locationId"';
    
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
    // if ($data_style['locationIds'] != "") {
    //     $locArr = explode(",", $data_style['locationIds']);
    // }
    
    
    if (isset($_GET['unitId']) && $_GET['unitId'] != '0')
        {
    
           $sql = 'select "locationId" from "tbl_invStorage" where unit = \''.$_GET['unitId'].'\' LIMIT 1';
           if (!($result = pg_query($connection, $sql))) 
           {
                print("Failed invQuery: " . pg_last_error($connection));
                exit;
            }
            $row = pg_fetch_array($result);
                $this_location[] = $row;
            
            pg_free_result($row);
    
    
            $locArr[0]=$this_location[0]['locationId'];
    
            //var_dump($this_location[0]['locationId']);
            //exit();
    
    
        }
        else
        {
            if ($data_style['locationIds'] != "") {
                $locArr = explode(",", $data_style['locationIds']);
            }
        }
    
        // print_r($locArr);
        // exit();
    
    
    
    
    
    
    $is_slot = false;
    
    if (!isset($_GET['unitId']) || $_GET['unitId'] != '0') {
    
        $temp = explode('_',$_GET['unitId']);
    
        if(isset($temp[1]))
        {
            $tmp = substr($temp[1], 0,2);
            if($tmp == 'CV' || substr($temp[1], 0,1) == 'C')
            {
                $is_slot = true;
            }
        }
    
        //var_dump ($is_slot);
        //exit();
        
        
    
        $query = 'select * from "tbl_invStorage" WHERE unit=' . "'" . $_GET['unitId'] . "'";
    
        if (!($resultProduct = pg_query($connection, $query))) {
            print("Failed invQuery: " . pg_last_error($connection));
            exit;
        }
        while ($rowProduct = pg_fetch_array($resultProduct)) {
            $data_product[] = $rowProduct;
        }
        pg_free_result($rowProduct);
    
        // echo "<pre>"; print_r($data_product);
        // exit(); 
    
    }
    
    
    
        $sql = 'select distinct "unit" , "wareHouseQty" as "quantity",
               "opt1ScaleId",
               "sizeScaleId" as "mainSizeId",
               "invId","styleId" from "tbl_invStorage" where "styleId"='.$_GET['styleId'].' ORDER BY unit';
        if(!($result=pg_query($connection,$sql)))
        {
            print("Failed StyleQuery: " . pg_last_error($connection));
            exit;
        }
        while($row = pg_fetch_array($result))
        {
            $_store[]=$row; // -------------------------- data_color ---------
        }
        // echo "<pre>"; print_r($_store);
        // exit();
        //----------------------------------Location-----------------------
        $query = '';
        $query = "SELECT * from \"locationDetails\"";
        $query .= "  where \"locationId\"='" . $data_style['locationIds'] . "' ";
      //  $query .= " INNER JOIN \"tbl_invLocation\" ON (\"locationDetails.locationId\"=\"tbl_invLocation.locationId\")";
    if (!($resultProduct = pg_query($connection, $query))) {
            print("Failed invQuery: " . pg_last_error($connection));
            exit;
        }
        while ($row = pg_fetch_array($resultProduct)) {
            $data_location[] = $row;
        }
        pg_free_result($resultProduct);
    //echo "<pre>";print_r($data_location[0]['conveyor']);die();
    
    
    
    
        // echo "<pre>";print_r($data_style['locationIds']);
        // exit();
    
        // if (isset($_GET['unitId']) && $_GET['unitId'] != '0') {
        //     echo "**********************************";
        //     exit();
        // }
    
        // echo "<pre>";print_r($locArr);
        // exit();
    
    
        // if (isset($_GET['unitId']) && $_GET['unitId'] != '0')
        // {
    
        //    $sql = 'select "locationId" from "tbl_invStorage" where unit = \''.$_GET['unitId'].'\' LIMIT 1';
        //    if (!($result = pg_query($connection, $sql))) 
        //    {
        //         print("Failed invQuery: " . pg_last_error($connection));
        //         exit;
        //     }
        //     $row = pg_fetch_array($result);
        //         $this_location[] = $row;
            
        //     pg_free_result($row);
    
    
        //     $locArr[0]=$this_location[0]['locationId'];
    
        //     //var_dump($this_location[0]['locationId']);
        //     //exit();
    
    
        // }
        
    
    
    
    
        // echo "data mainsize :<pre>"; print_r($data_mainSize);
        // echo "<br><br>";
        // echo "data_opt1Size :<pre>"; print_r($data_opt1Size);
        // echo "<br><br>";
        // echo "loc arr :<pre>"; print_r($locArr);
        // echo "<br><br>";
        // echo "data style :<pre>"; print_r($data_style);
        // echo "<br><br>";
        // echo "data inv: <pre>"; print_r($data_inv);
        // exit();
        // echo "<pre>"; print_r($_store);
        // exit();
    
        if (!isset($_GET['unitId']) || $_GET['unitId'] == '0')
        {
            $bckup_data_inv = $data_inv;
            $hash = array();
            for ($i = 0 ; $i<count($data_inv) ; $i++) 
            {
                if(isset($data_inv[$i]['opt1ScaleId']) && isset($data_inv[$i]['sizeScaleId']))
                {
                    $y = $data_inv[$i]['opt1ScaleId'].'_'.$data_inv[$i]['sizeScaleId'];
                    if(!isset($hash[$y]))
                    {
                        $hash[$y] = (int)$data_inv[$i]['quantity'];
                        $data_inv[$i]['locationId'] = 1;
                    }
                    else
                    {
                        $hash[$y] = $hash[$y]+$data_inv[$i]['quantity'];
                        $data_inv[$i]['locationId'] = 1;
                    }
                }
            }
    
            for($i=0; $i<count($data_inv); $i++)
            {
    
                
                if(isset($data_inv[$i]['opt1ScaleId']) && isset($data_inv[$i]['sizeScaleId']))
                {
    
                    $x = $data_inv[$i]['opt1ScaleId'].'_'.$data_inv[$i]['sizeScaleId'];
                    $data_inv[$i]['quantity'] = $hash[$x];
                }
            }
    
            $locArr = array();
            $locArr[0] = 1;
        }
        
    
    
    
    ?>
<script type="text/javascript" src="<?php echo $mydirectory; ?>/js/jquery-ui.min-1.8.2.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory; ?>/js/samplerequest.js"></script>
<script src="<?php echo $mydirectory; ?>/js/modernizr.js"></script>
<script src="<?php echo $mydirectory; ?>/js/tabs.js"></script>
<link href="<?php echo $mydirectory; ?>/css/style.css" rel="stylesheet">
<table width="90%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td align="left"><input type="button" value="Back" onclick="location.href='reports.php'"/></td>
        <td>&nbsp;</td>
        <td align="right"><label>
            <input type="button" name="send-email" id="send-email" value="Send Email"/>
            &nbsp;&nbsp; </label>
        </td>
    </tr>
</table>
<!-- Trigger/Open The Modal -->
<!-- <button id="myBtn">Open Modal</button>
    -->
<!-- The Modal -->
<div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <span id="close_warehouse_f" class="close">&times;</span>
        <div class="main-form">
            <div class="form-left" >
                <ul id="tab">
                    <li class="active">
                        <h2>Warehouse Form <span id="warehouse_err_msg" style="display: none; color: brown;"></span> </h2>
                        <form id="warehouse_new_form" method="post" action="addNewInventory.php">
                            <input type="hidden" name="styleId" value="<?php echo $_GET['styleId']; ?>">
                            <table cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td>
                                        Location
                                    </td>
                                    <td>
                                        <select id="location_dropdown" name="location" class="location_dropdown">
                                            <option value="0">--All Location--</option>
                                            <?php foreach($warehouse_info as $k_info=>$each_info){ ?>
                                            <option value="<?php echo $each_info['locationId']; ?>"><?php echo $each_info['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr id="warehouse_td" style="display: none">
                                    <td>
                                        Warehouse
                                    </td>
                                    <td>
                                        <select name="warehouse" id="warehouse_dropdown">
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Row
                                    </td>
                                    <td>
                                        <input id="warehouse_form_row" type="text" name="row">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Rack
                                    </td>
                                    <td>
                                        <input id="warehouse_form_rack" type="text" name="rack">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Shelf
                                    </td>
                                    <td>
                                        <input id="warehouse_form_shelf" type="text" name="shelf">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        box#
                                    </td>
                                    <td id="location_unit">
                                        <input id="warehouse_form_unit" type="text" name="unit">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        &nbsp;
                                    </td>
                                    <td>
                                        <input class="save-btn" type="submit" value="Save" name="">
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </li>
                    <li>
                        <h2>Container Form <span id="cont_err_msg" style="display: none; color: brown;"></span> </h2>
                        <form id="new_container_form">
                            <table cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td>
                                        Location
                                    </td>
                                    <td>
                                        <select id="container_loc_dropdown">
                                            <option value="0">--All Location--</option>
                                            <?php foreach($containers_location as $k_con=>$k_con_val){ ?>
                                            <option value="<?php echo $k_con_val['locationId']; ?>" ><?php echo $k_con_val['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr id="container_td" style="display:none">
                                    <td>
                                        Container
                                    </td>
                                    <td>
                                        <select id="container_dropdown">
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        box#
                                    </td>
                                    <td>
                                        <input type="text" id="co_unit" name="co_unit">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        &nbsp;
                                    </td>
                                    <td>
                                        <input class="save-btn" type="submit" value="Save" name="">
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </li>
                    <li>
                        <h2>Conveyor Form <span id="conv_err_msg" style="display: none; color: brown;"></span> </h2>
                        <form id="new_conveyor_form">
                            <table cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td>
                                        Location
                                    </td>
                                    <td>
                                        <select id="conveyor_loc_dropdown">
                                            <option value="0">--All Location--</option>
                                            <?php foreach($conveyors_location as $k_conv=>$k_conv_val){ ?>
                                            <option value="<?php echo $k_conv_val['locationId']; ?>" ><?php echo $k_conv_val['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr id="conveyor_td" style="display:none">
                                    <td>
                                        Conveyor
                                    </td>
                                    <td>
                                        <select id="conveyor_dropdown">
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Slot#
                                    </td>
                                    <td>
                                        <input type="text" name="cv_slot">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        &nbsp;
                                    </td>
                                    <td>
                                        <input class="save-btn" type="submit" value="Save" name="">
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </li>
                </ul>
            </div>
            <div class="form-right">
                <ul id="tabs">
                    <li id="warehouse_link" class="active">Warehouse</li>
                    <li id="container_link">container</li>
                    <li id="conveyor_link">conveyor</li>
                </ul>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<table width="100%">
    <tr>
        <td>
            <center>
                <table>
                    <tr>
                        <td>
                            <div align="center" id="message"></div>
                        </td>
                    </tr>
                </table>
            </center>
        </td>
    </tr>
    <tr>
        <td align="center">
            <font size="5">Report</font><font size="5"> View/Edit <br>
            <br>
            </font>
            <fieldset style="margin:10px;">
                <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <!-- print functionality -->
                        <button class="pull-right" id="print"
                            onclick="print_content('<?php echo $_GET['styleId']; ?>'
                            ,'<?php echo $data_loc[$loc_identity]['name'] ?>'
                            ,'<?php if (isset($_GET['unitId'])) echo $_GET['unitId'];
                                else echo 'null' ?>')"
                            class="pull-right">Print
                        </button>
                        <form id="optForm" method="post">
                            <td>Style:</td>
                            <td>
                                <h1><?php echo $data_style['styleNumber']; ?></h1>
                            </td>
                            <?php if ($data_style['barcode'] != "") { ?>
                            <td width="60">Barcode:</td>
                            <td>
                                <h1><img width="100" height="100"
                                    src="../../uploadFiles/inventory/images/<?php echo $data_style['barcode']; ?>">
                                </h1>
                            </td>
                            <?php } ?>
                            <td width="100px">
                                <div class="color">Color:&nbsp;
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
                                    </select>&nbsp;&nbsp;&nbsp;
                                </div>
                            </td>
                            <td>
                                unit #:&nbsp;
                                <select name="unit_num" id="unit_num">
                                    <option value="0">---- All units # ----</option>
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
                            <!--<td>&nbsp;<input  type="button" name="del_qnt" id="del_qnt" value="Delete All Quantities"
                                onclick="javascript:delAllQnts();" class="ui-button ui-widget ui-state-default ui-corner-all"/></td>-->
                        </form>
                        <td><input type="button" value="Main Inventory" onclick="main_inv();"/></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <?php
                            if (!isset($_GET['unitId']) || $_GET['unitId'] != '0')
                            {
                            ?>
                        <?php if(!$is_slot){ ?>
                    <tr id="view_details">
                        <td style="display: none;">Room: <input type="text" id="updateroom" value="<?php //echo $data_product[0]['room']; ?>"/></td>
                        <td>Row: <input type="text" id="updaterow" value="<?php echo $data_product[0]['row']; ?>" /></td>
                        <td>Rack: <input type="text" id="updaterack" value="<?php echo $data_product[0]['rack']; ?>" /></td>
                        <td>Shelf: <input type="text" id="updateshelf" value="<?php echo $data_product[0]['shelf']; ?>" /></strong></td>
                        <td>
                            <button type="button" onclick="Update()" class="btn btn-success" style="color: #0c00d2">
                            Update
                            </button>
                            <button type="button" onclick="Delete()" class="btn btn-danger" style="color: #cd0a0a">
                            Delete
                            </button>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php
                        } else {
                            ?>
                    <tr>
                        <td>
                            <!--button type="button" id="newInventory" class="btn btn-success">Add new Inventory</button--> 
                        </td>
                        <td>&nbsp; </td>
                        <td>&nbsp;</td>
                        <td>&nbsp; </td>
                        <td>&nbsp;</td>
                    </tr>
                    <?php } ?>
                    <tr id="hide">
                        <button class="pull-left" type="button" id="addinventory_new">Add Inventory</button>
                        <!-- <td>
                            <button type="button" id="newInventory" onclick="addInventory()"
                                    class="btn btn-success">Add new Inventory
                            </button>
                            </td>
                            <td>&nbsp; </td>
                            <td>&nbsp;</td>
                            <td>&nbsp; </td>
                            <td>&nbsp;</td> -->
                    </tr>
                    <tr id="inventoryDetails" style="display: none">
                        <!-- <td>unit Name: <input type="text" id="newunit"></td>
                            <td>Room: <input type="text" id="newRoom"></td>
                            <td>Row: <input type="text" id="newRow"></td>
                            <td>Rack: <input type="text" id="newRack"></td>
                            <td>Shelf: <input type="text" id="newShelf"></td>
                            <td>
                                <button type="button" onclick="addNewInventory()" class="btn btn-success"
                                        style="color: #0c00d2">Add
                                </button>
                            <td>
                                <button type="button" onclick="cancelInventory()" class="btn" style="color: #cd0a0a">
                                    Cancel
                                </button> -->
                    </tr>
                    </tr>
                </table>
            </fieldset>
            <?php 
                if(isset($_GET['styleId']) && (!isset($_GET['unitId']) ||$_GET['unitId'] == '0')){
                    $sql ='select * from "tbl_date_interval_setting"';
                            if (!($resultProduct = pg_query($connection, $sql))) {
                            print("Failed invQuery: " . pg_last_error($connection));
                            exit;
                            }
                            while ($row = pg_fetch_array($resultProduct)) {
                                
                            $interval[]=$row;
                           
                            }
                            foreach ($interval as $key => $valueint) {
                               if($valueint['color']=="green"){
                                $green=$valueint['interval'];
                               }
                               if($valueint['color']=="yellow"){
                                $yellow=$valueint['interval'];
                               }
                               if($valueint['color']=="red"){
                                $red=$valueint['interval'];
                               }
                            }
                pg_free_result($resultProduct); 
                
                //pg_free_result($resultoldinv);
                    $sql='';
                    $sql='select * from "tbl_log_updates" where "styleId" ='.$_GET['styleId'].' and "present" = '."'".inventory."'".' order by "updatedDate" desc LIMIT 1';
                        if(!($resultoldinv=pg_query($connection,$sql))){
                           
                        }
                        else{
                            
                        }
                        $rowoldinv = pg_fetch_row($resultoldinv);
                        $oldinv=$rowoldinv;
                        //print_r($oldinv);
                        pg_free_result($resultoldinv);
                        
                        if($oldinv){
                            $empsql='select * from "employeeDB" where "employeeID" ='.$oldinv['2'].' LIMIT 1';
                            if(!($resultemp=pg_query($connection,$empsql))){
                            
                            }
                            else{
                            
                            }
                            $rowemp = pg_fetch_row($resultemp);
                            $oldemp=$rowemp;
                            pg_free_result($resultemp);
                ?>
            <fieldset style="margin:10px;">
                <table width="98%" border="0" cellspacing="1" cellpadding="1">
                    <tbody>
                        <tr>
                            <td width="355" height="25" align="right" valign="top">Date: <br></td>
                            <td width="10">&nbsp;</td>
                            <td align="left" valign="top">
                                <?php echo date('m/d/Y h:i:s',$oldinv['3']);?>
                                <?php 
                                    $date2=date('U');
                                    $date1=$oldinv['4'];
                                    if($oldinv['4']){
                                    $resultday=round(abs($date1-$date2)/86400);
                                    if($resultday<=$green){
                                    $colo= "green";
                                    }
                                    if($resultday<=$yellow && $resultday>$green){
                                    $colo= "yellow";
                                    }
                                    if($resultday>$yellow){
                                    $colo= "red";
                                    }
                                    echo '<div class="tooltip" id="button" style="width:25px; height: 25px; border-radius:100%; background-color:'.$colo.';"></div>';
                                    }
                                    ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="355" height="25" align="right" valign="top">Updated By: <br></td>
                            <td width="10">&nbsp;</td>
                            <td align="left" valign="top">
                                <?php echo $oldemp['1']." ".$oldemp['2'];?>
                            </td>
                        </tr>
                        <tr>
                            <td width="355" height="25" align="right" valign="top">Previous: <br></td>
                            <td width="10">&nbsp;</td>
                            <td align="left" valign="top">
                                <table>
                                    <tr>
                                        <td>Scale1x</td>
                                        <td>Scale2</td>
                                        <td>Value</td>
                                        <td>Unit</td>
                                    </tr>
                                    <?php 
                                        $data=json_decode($oldinv['5']);
                                        foreach ($data as $key => $prevalue) {
                                           //print_r($prevalue);
                                           ?>
                                    <tr>
                                        <td><?php logCheckOStyle($prevalue->sizeScaleId); ?></td>
                                        <td><?php logCheckNStyle($prevalue->opt1ScaleId); ?></td>
                                        <td><?php echo $prevalue->oldinv;?></td>
                                        <td><?php echo $prevalue->unit;?></td>
                                    </tr>
                                    <?php } ?>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td width="355" height="25" align="right" valign="top">Present: <br></td>
                            <td width="10">&nbsp;</td>
                            <td align="left" valign="top">
                                <table>
                                    <tr>
                                        <td>Scale1y</td>
                                        <td>Scale2</td>
                                        <td>Value</td>
                                        <td>Unit</td>
                                    </tr>
                                    <?php 
                                        $data=json_decode($oldinv['5']);
                                        foreach ($data as $key => $prevalue) {
                                            //print_r($prevalue);
                                            ?>
                                    <tr>
                                        <td><?php logCheckOStyle($prevalue->sizeScaleId); ?></td>
                                        <td><?php logCheckNStyle($prevalue->opt1ScaleId); ?></td>
                                        <td><?php echo $prevalue->wareHouseQty;?></td>
                                        <td><?php echo $prevalue->unit;?></td>
                                    </tr>
                                    <?php } ?>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
            <?php
                }
                
                }
                if(isset($_GET['styleId']) && (isset($_GET['unitId']) && $_GET['unitId'] != '0')){
                $sql ='select * from "tbl_date_interval_setting"';
                    if (!($resultProduct = pg_query($connection, $sql))) {
                    print("Failed invQuery: " . pg_last_error($connection));
                    exit;
                    }
                    while ($row = pg_fetch_array($resultProduct)) {
                        
                    $interval[]=$row;
                   
                    }
                    foreach ($interval as $key => $valueint) {
                       if($valueint['color']=="green"){
                        $green=$valueint['interval'];
                       }
                       if($valueint['color']=="yellow"){
                        $yellow=$valueint['interval'];
                       }
                       if($valueint['color']=="red"){
                        $red=$valueint['interval'];
                       }
                    }
                pg_free_result($resultProduct); 
                
                //pg_free_result($resultoldinv);
                $sql='';
                $sql='select * from "tbl_log_updates" where "styleId" ='.$_GET['styleId'].' and  "warehouse" ='."'".$_GET['unitId']."'".' and "present" = '."'".inventory."'".' order by "updatedDate" desc LIMIT 1';
                if(!($resultoldinv=pg_query($connection,$sql))){
                    
                }
                else{
                    
                }
                $rowoldinv = pg_fetch_row($resultoldinv);
                $oldinv=$rowoldinv;
                //print_r($oldinv);
                pg_free_result($resultoldinv);
                
                if($oldinv){
                    $empsql='select * from "employeeDB" where "employeeID" ='.$oldinv['2'].' LIMIT 1';
                    if(!($resultemp=pg_query($connection,$empsql))){
                    
                    }
                    else{
                    
                    }
                    $rowemp = pg_fetch_row($resultemp);
                    $oldemp=$rowemp;
                    pg_free_result($resultemp);?>
            <fieldset style="margin:10px;">
                <table width="98%" border="0" cellspacing="1" cellpadding="1">
                    <tbody>
                        <tr>
                            <td width="355" height="25" align="right" valign="top">Date: <br></td>
                            <td width="10">&nbsp;</td>
                            <td align="left" valign="top">
                                <?php echo date('m/d/Y h:i:s',$oldinv['4']);?>
                                <?php 
                                    $date2=date('U');
                                    $date1=$oldinv['4'];
                                    if($oldinv['4']){
                                    $resultday=round(abs($date1-$date2)/86400);
                                    if($resultday<=$green){
                                    $colo= "green";
                                    }
                                    if($resultday<=$yellow && $resultday>$green){
                                    $colo= "yellow";
                                    }
                                    if($resultday>$yellow){
                                    $colo= "red";
                                    }
                                    echo '<div class="tooltip" id="button" style="width:25px; height: 25px; border-radius:100%; background-color:'.$colo.';"></div>';
                                    }
                                    ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="355" height="25" align="right" valign="top">Updated By: <br></td>
                            <td width="10">&nbsp;</td>
                            <td align="left" valign="top">
                                <?php echo $oldemp['1']." ".$oldemp['2'];?>
                            </td>
                        </tr>
                        <tr>
                            <td width="355" height="25" align="right" valign="top">Previous: <br></td>
                            <td width="10">&nbsp;</td>
                            <td align="left" valign="top">
                                <table>
                                    <tr>
                                        <td>Scale1x</td>
                                        <td>Scale2</td>
                                        <td>Value</td>
                                        <td>Unit</td>
                                    </tr>
                                    <?php 
                                        $data=json_decode($oldinv['5']);
                                        foreach ($data as $key => $prevalue) {
                                           //print_r($prevalue);
                                           ?>
                                    <tr>
                                        <td><?php logCheckOStyle($prevalue->sizeScaleId); ?></td>
                                        <td><?php logCheckNStyle($prevalue->opt1ScaleId); ?></td>
                                        <td><?php echo $prevalue->oldinv;?></td>
                                        <td><?php echo $prevalue->unit;?></td>
                                    </tr>
                                    <?php } ?>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td width="355" height="25" align="right" valign="top">Present: <br></td>
                            <td width="10">&nbsp;</td>
                            <td align="left" valign="top">
                                <table>
                                    <tr>
                                        <td>Scale1y</td>
                                        <td>Scale2</td>
                                        <td>Value</td>
                                        <td>Unit</td>
                                    </tr>
                                    <?php 
                                        $data=json_decode($oldinv['5']);
                                        foreach ($data as $key => $prevalue) {
                                            //print_r($prevalue);
                                            ?>
                                    <tr>
                                        <td><?php logCheckOStyle($prevalue->sizeScaleId); ?></td>
                                        <td><?php logCheckNStyle($prevalue->opt1ScaleId); ?></td>
                                        <td><?php echo $prevalue->wareHouseQty;?></td>
                                        <td><?php echo $prevalue->unit;?></td>
                                    </tr>
                                    <?php } ?>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
            <?php
                }
                }
                
                ?>
            <form id="inventoryForm">
                <?php
                    if(isset($_GET['unitId']) && $_GET['unitId'] != '0'){
                    
                        //$my_data;
                    
                        
                        $sql = 'select str."locationId" , inv.location_details_id from "tbl_invStorage" as str  left join "tbl_inventory" as inv on inv."inventoryId" = str."invId" where str.unit = \''.$_GET['unitId'].'\' ';
                    
                    
                    
                        if (!($result = pg_query($connection, $sql))) 
                        {
                            print("Failed invQuery: " . pg_last_error($connection));
                            exit;
                        }
                        while ($row = pg_fetch_array($result)) {
                            $my_data[] = $row;
                        }
                    
                        $location_id = '';
                        $location_details_id = '';
                        foreach ($my_data as $key => $value)
                        {
                    
                            $location_id = $value['locationId'];
                            if($value['location_details_id'] != '')
                            $location_details_id = $value['location_details_id'];
                        }
                    
                        
                        //$location_id = $my_data[0]['locationId'];
                        //$inventory_id = $my_data[0]['invId'];
                        //$location_details_id = $my_data[0]['location_details_id'];
                    
                        // echo $location_id.'=='.$location_details_id;
                        // exit();
                    
                        // echo "<pre>";print_r($my_data);
                        // exit();
                    }
                    
                    
                    
                    ?>
                <input type="hidden" id="_location_id" name="location_id" value="<?php echo (isset($location_id) && $location_id) > 0 ? $location_id : 'null' ?>">
                <input type="hidden" id="_inventory_id" name="inventory_id" value="<?php echo (isset($inventory_id) && $inventory_id > 0) ? $inventory_id : 'null' ?>">
                <input type="hidden" id="_location_details_id" name="location_details_id" value="<?php echo (isset($location_details_id) && $location_details_id) > 0 ? $location_details_id : 'null' ?>">
                <div id="scrollLinks">
                <fieldset style="margin:10px;">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="10"></td>
                            <td width="170" align="left" valign="top" style="padding:5px;">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td>
                                            <img id="imgView" src="<?php echo $upload_dir_image . trim($imageName); ?>" alt="thumbnail" width="150" height="230" border="1" class="mouseover_left"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="100">&nbsp;</td>
                                    </tr>
                                    <?php if (isset($_SESSION['employeeType']) && $_SESSION['employeeType'] < 4) { ?>
                                    <?php
                                        if (!isset($_GET['unitId']) || $_GET['unitId'] != '0') {
                                            ?>
                                    <tr>
                                        <td></td>
                                    </tr>
                                    <?php } ?>
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
                                                    <table class="HD001" width="250px" style="float:left;" border="0" cellspacing="1" cellpadding="1">
                                                        <tr>
                                                            <td class="gridHeaderReportGrids3">&nbsp;</td>
                                                            <td class="gridHeaderReport">sizes</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="gridHeaderReportGrids3">
                                                                <a class="mouseover_left" href="#">
                                                                <img src="<?php echo $mydirectory; ?>/images/leftArrw.gif" alt="lft" width="33" height="26" border="0"/>
                                                                </a>
                                                                <a class="mouseover_right" href="#">
                                                                <img src="<?php echo $mydirectory; ?>/images/rightArrw.gif" alt="lft" width="30" height="26" border="0"/>
                                                                </a>
                                                            </td>
                                                            <td class="gridHeaderReport">prices</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="gridHeaderReportGrids3">&nbsp;</td>
                                                            <td class="gridHeaderReportGrids2">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="gridHeaderReportGrids3">&nbsp;</td>
                                                            <td colspan="2" class="gridHeaderReportGrids4"> <?php echo $data_optionName['opt1Name']; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="gridHeaderReportGrids3">&nbsp;</td>
                                                            <td class="gridHeaderReportGrids2">
                                                                <span class="gridHeaderReportGrids3">
                                                                <a class="mouseover_up" href="#">
                                                                <img src="<?php echo $mydirectory; ?>/images/upArrw.gif" alt="lft" width="33" height="26" border="0"/>
                                                                </a>
                                                                <a class="mouseover_down" href="#">     <img src="<?php echo $mydirectory; ?>/images/dwnArrw.gif" alt="lft" width="33" height="26" border="0"/>
                                                                </a>
                                                                </span>
                                                            </td>
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
                                                    class="gridHeaderReportGrids2">&nbsp;</td>
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
                                        <div id="wn2" style="position:relative; width:600px; height:<?php echo((count($data_opt1Size) * count($locArr) * 32) + (count($data_loc) * 32)); ?>px;  overflow:hidden; float:left;">
                                            <?php }else{ ?>
                                            <div id="wn2" style="position:relative; width:600px; height:<?php echo(count($locArr) * 64); ?>px;  overflow:hidden; float:left;">
                                                <?php } ?>
                                                <div id="lyr2">
                                                    <div id="values_">
                                                        <table id="values" width="<?php echo $tableWidth . "px"; ?>" border="0" cellspacing="1" cellpadding="1">
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
                                        <table class="HD001" style="float:left; width:250px;" border="0" cellspacing="1" cellpadding="1">
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
            <!-- form print -->
        </td>
    </tr>
</table>
<div class="inventory-box">
    <div class="container">
        <div class="row">
            <form id="inventoryForm">
                <div class="col-md-3 left-sidebar">
                    <img id="imgView" src="<?php echo $upload_dir_image . trim($imageName); ?>" alt="thumbnail" width="150" height="230" border="1" class="mouseover_left"/>
                </div>
                <div class="col-md-9 right-sidebar">
                    <div class="inventory-table">
                        <div class="col-xs-2"></div>
                        <div class="col-xs-10">
                            <div class="row">
                                <div class="col-xs-6 col-sm-4 col-md-3 nopadding">
                                    <div class="each-section">
                                        <span>XXXS</span>
                                        <span>40</span>
                                        <span>40</span>
                                        <span>40</span>
                                        <span>40</span>
                                        <span>40</span>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-4 col-md-3 nopadding">
                                    <div class="each-section">
                                        <span>XXS</span>
                                        <span>30</span>
                                        <span>30</span>
                                        <span>30</span>
                                        <span>30</span>
                                        <span>30</span>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-4 col-md-3 nopadding">
                                    <div class="each-section">
                                        <span>XS</span>
                                        <span>20</span>
                                        <span>20</span>
                                        <span>20</span>
                                        <span>20</span>
                                        <span>20</span>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-4 col-md-3 nopadding">
                                    <div class="each-section">
                                        <span>S</span>
                                        <span>10</span>
                                        <span>10</span>
                                        <span>10</span>
                                        <span>10</span>
                                        <span>10</span>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-4 col-md-3 nopadding">
                                    <div class="each-section">
                                        <span>M</span>
                                        <span>10</span>
                                        <span>10</span>
                                        <span>10</span>
                                        <span>10</span>
                                        <span>10</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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
/*
===================================
    Inventory table responsive
===================================
*/
$(document).ready(function(){
    var windowWidth = $(window).width();
    $(".inventory-table .col-md-3").each(function(i){

        var data = "<div class='row'><div class='title-section'><div class='col-md-12 nopadding'><p>sizes</p></div></div><div class='title-section'><div c00lass='col-md-12 nopadding'><p>prices</p></div></div><div class='title-section'><div class='col-md-12 nopadding'><p>Petite</p></div></div><div class='title-section'><div class='col-md-12 nopadding'><p>Short</p></div></div><div class='title-section'><div class='col-md-12 nopadding'><p>Reg</p></div></div><div class='title-section'><div class='col-md-12 nopadding'><p>Tall</p></div></div></div>"

        if(windowWidth > 991){
            if( i % 4 === 0 ) {
                $(".inventory-table .col-xs-2").append(data);
            }
        }

        if(windowWidth > 767 && windowWidth <= 991){
            if( i % 3 === 0 ) {
                $(".inventory-table .col-xs-2").append(data);
            }
        }

        if(windowWidth <= 767){
            if( i % 2 === 0 ) {
                $(".inventory-table .col-xs-2").append(data);
            }
        }
        
    });
});
/*
=============================================
*/
</script>
<script>
    $(document).ready(function(){
        $('#warehouse_link').hide();
    })
    
    $('#container_link').click(function(e){
        $('#container_link').hide();
        $('#conveyor_link').show();
        $('#warehouse_link').show();
    });
    
    $('#conveyor_link').click(function(e){
        $('#container_link').show();
        $('#conveyor_link').hide();
        $('#warehouse_link').show();
    });
    
    $('#warehouse_link').click(function(e){
        $('#container_link').show();
        $('#conveyor_link').show();
        $('#warehouse_link').hide();
    });
    
    
    
    $('#warehouse_dropdown').select(function(e){
       // alert(this.value);
    });
    
    
    $('#conveyor_loc_dropdown').change(function(e){
        id = this.value;
        if(id != 0)
        {
            $.ajax({
                url: 'conveyor_dropdown.php',
                type:"post",
                dataType : "json",
                data: {
                    id:id
                },
                success: function(data) 
                {
                    //console.log(data);
                    $('#conveyor_td').show();
                    $('#conveyor_dropdown').empty();
                    $('#conveyor_dropdown').append($('<option>',
                    {
                        value: 0,
                        data: "",
                        text : '--All conveyor--'
                    }));
                    $.each (data, function(i){
                        $('#conveyor_dropdown').append($('<option>',
                         {
                            value: data[i].id,
                            text : data[i].conveyor
                        }));
                    });
                }
            }); 
        }
        else{
            $('#conveyor_dropdown').empty();
        }
    
    });
    
    $('#container_loc_dropdown').change(function(e){
        id = this.value;
        if(id != 0)
        {
            $.ajax({
                url: 'container_dropdown.php',
                type:"post",
                dataType : "json",
                data: {
                    id:id
                },
                success: function(data) 
                {
                    //console.log(data);
                    $('#container_td').show();
                    $('#container_dropdown').empty();
                    $('#container_dropdown').append($('<option>',
                    {
                        value: 0,
                        text : '--All container--'
                    }));
                    $.each (data, function(i){
                        $('#container_dropdown').append($('<option>',
                         {
                            value: data[i].id,
                            text : data[i].container
                        }));
                    });
    
                    
                }
            }); 
        }
        else
        {
            $('#container_dropdown').empty();
        }
    });
    
    
    
    $('.location_dropdown').change(function(e){
        id = this.value;
        if(id != 0)
        {
           $.ajax({
                url: 'warehouse_dropdown.php',
                type:"post",
                dataType : "json",
                data: {
                    id:id
                },
                success: function(data) 
                {
    
                    //console.log(data);
                    length = Object.keys(data).length;
                    //console.log(length);
                    $('#warehouse_td').show();
                    $('#warehouse_dropdown').empty();
                    $('#warehouse_dropdown').append($('<option>',
                         {
                            value: 0,
                            text : '--All warehouse--'
                        }));
    
                    $.each (data, function (i) {
                        //console.log (i);
                        $('#warehouse_dropdown').append($('<option>',
                         {
                            value: data[i].id,
                            text : data[i].warehouse
                        }));
                    });   
                }
            }); 
        }
        else
        {
            $('#warehouse_dropdown').empty();
        }
    });
    
    // function warehouse_dropdown()
    //     {
    //         console.log(1);
    //     }
    
    
    $('#new_conveyor_form').submit(function(e){
        e.preventDefault();
        var styleId = document.getElementById('styleId').value;
        var colorId = document.getElementById('colorId').value;
        var slot = $('input[name="cv_slot"]').val();
        var locationId = $('#conveyor_loc_dropdown').val();
        var conveyorId = $('#conveyor_dropdown').val();
        
        //console.log(slot,locationId,conveyorId);
        if(slot == '' || conveyorId == '0' || conveyorId == null || locationId == '0' || locationId == null)
        {
            //console.log(slot,conveyorId,locationId);
        }
        else
        {
            //alert (locationId+' '+styleId);
            console.log(slot,conveyorId,locationId,styleId,colorId);
            $.ajax({
                url: 'add_unit_to_conveyor.php',
                type:"post",
                dataType : "json",
                data: {
                    styleId:styleId,
                    colorId:colorId,
                    slot : slot,
                    locationId : locationId,
                    conveyorId : conveyorId,
                    type : 'slot'
                },
                success: function(data) 
                {
                    //console.log(data);
                    if(data == 'slot not available')
                    {
                        
                        $('#conv_err_msg').empty();
                        $('#conv_err_msg').text(' -- slot not available');
                        $('#conv_err_msg').show();
                        $('#conv_err_msg').delay(3000).fadeOut();
                        
                    } 
                    else
                    {
                        $('#close_warehouse_f').trigger("click");
    
                        window.location.replace("reportViewEdit.php?styleId=" + styleId + "&colorId=" + colorId + "&unitId=" + data);
                        $("#message").html("<div class='successMessage'><strong>Inventory Added. Thank you.</strong></div>");
                    }
                }
            }); 
        }
    });
    
    
    
    
    $('#new_container_form').submit(function(e){
        e.preventDefault();
        var styleId = document.getElementById('styleId').value;
        var colorId = document.getElementById('colorId').value;
        var unit = $('input[name="co_unit"]').val();
        var locationId = $('#container_loc_dropdown').val();
        var containerId = $('#container_dropdown').val();
    
        if(unit == '' || containerId == '0' || containerId == null || locationId == '0' || locationId == null)
        {
            console.log(unit,containerId,locationId);
        }
        else
        {
            //console.log(unit,containerId,locationId,styleId,colorId);
            //alert (locationId+' '+styleId);
            $.ajax({
                url: 'add_unit_to_container.php',
                type:"post",
                dataType : "json",
                data: {
                    styleId:styleId,
                    colorId:colorId,
                    unit : unit,
                    locationId : locationId,
                    containerId : containerId,
                    type : 'unit_c'
                },
                success: function(data) 
                {
                    //console.log(data);
                    if(data == 'box not available')
                    {
                        //alert(data);
                        $('#cont_err_msg').empty();
                        $('#cont_err_msg').text(' -- box not available');
                        $('#cont_err_msg').show();
                        $('#cont_err_msg').delay(3000).fadeOut();
                        //$('#cont_err_msg').empty();
                    }
                    else
                    {
                        $('#close_warehouse_f').trigger("click");
    
                        window.location.replace("reportViewEdit.php?styleId=" + styleId + "&colorId=" + colorId + "&unitId=" + data);
                        $("#message").html("<div class='successMessage'><strong>Inventory Added. Thank you.</strong></div>");
    
                        //alert('success');        
                    }
                }
            }); 
        }
    });
    
    
    
    
    $('#warehouse_new_form').submit(function(e){
        e.preventDefault();
        var styleId = document.getElementById('styleId').value;
        var colorId = document.getElementById('colorId').value;
        var location = $('#location_dropdown').val();
        var warehouse = $('#warehouse_dropdown').val();
        var rack = $('input[name="rack"]').val();
        var row  = $('input[name="row"]').val();
        var shelf  = $('input[name="shelf"]').val();
        var unit = $('input[name="unit"]').val();
        //var location_details_id = $('input[name="location_details_id"]').val();
    
        //alert(location+' '+styleId);
    
        //console.log(warehouse,location);
    
        if(colorId == '' || styleId == '' ||  unit == '' || shelf == '' || row == '' || rack == '' || warehouse == '' || location == null || location == '0')
        {
            $('#close_warehouse_f').trigger("click");
            $("#message").html("<div class='errorMessage'><strong>All fields are mandatory. Please fill all fields and try later.</strong></div>").delay(3000).fadeOut();
        }
        else
        {
                $.ajax({
                    url: 'addNewInventory.php',
                    type:"post",
    
                    data: {
                        location:location,
                        warehouse:warehouse,
                        rack: rack,
                        row: row,
                        shelf: shelf,
                        unit:unit,
                        colorId:colorId,
                        styleId:styleId,
                        type : 'type_w'
                        //location_details_id:location_details_id
                    },
                    success: function (data) 
                    {
                        //console.log(data);
                        
                        if (data != null) 
                        {
                            if(data == "box not available")
                            {
    
                                $('#warehouse_err_msg').empty();
                                $('#warehouse_err_msg').text(' -- unit not available');
                                $('#warehouse_err_msg').show();
                                $('#warehouse_err_msg').delay(3000).fadeOut();
                                console.log('kaudfy---------');
                            }
                            else
                            {
                                console.log('kajdfgaludfgluiyf');
    
                                $('#close_warehouse_f').trigger("click");
    
                                window.location.replace("reportViewEdit.php?styleId=" + styleId + "&colorId=" + colorId + "&unitId=" + data);
                                $("#message").html("<div class='successMessage'><strong>Inventory Added. Thank you.</strong></div>");
                            }
                        } 
                        else 
                        {
                            
                            $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
                        }
                    }
                });
        }
    });
    
    function clear_form_data(){
        
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
        document.getElementById("location_dropdown").options[0].selected=true;
    
        //empty container form
        $('#co_unit').val('');
        $('#container_dropdown').empty();
        $('#container_td').hide();
        $('#cont_err_msg').empty();
        $('#cont_err_msg').hide();
        document.getElementById("container_loc_dropdown").options[0].selected=true;
    
        //empty conveyor form
        $('#cv_slot').val('');
        $('#conveyor_dropdown').empty();
        $('#conveyor_td').hide();
        $('#conv_err_msg').empty();
        $('#conv_err_msg').hide();
        document.getElementById("conveyor_loc_dropdown").options[0].selected=true;
        
    };
    
    // Get the modal
    var modal = document.getElementById('myModal');
    
    // Get the button that opens the modal
    var btn = document.getElementById("addinventory_new");
    
    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];
    
    // When the user clicks the button, open the modal 
    btn.onclick = function() {
        modal.style.display = "block";
    }
    
    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
        clear_form_data();
    }
    
    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
<script>
    $('#newInventory').click(function(e){
    
        $('#inventory_form').show();
        $('#warehouse_form').show();
    
        
    });
        
    function Update() 
    {
        
        room = $('#updateroom').val();
        // if(room == '') {
        //     alert("Please Provide a Room");
        //     return false;
        // }
        var rack = $('#updaterack').val();
        if(rack == '') {
            alert("Please Provide a rack");
            return false;
        }
        var row = $('#updaterow').val();
        if(row == '') {
            alert("Please Provide a row");
            return false;
        }
        var self = $('#updateshelf').val();
        if(self == '') {
            alert("Please Provide a self");
            return false;
        }
        //alert(row);
        $.ajax({
            url: 'editRoom.php',
            type:"post",
            data: {
                room: room,
                rack: rack,
                row: row,
                self: self,
                unitId: "<?php echo $_REQUEST['unitId'];?>",
                styleId: document.getElementById('styleId').value
            },
            success: function (response) {
                if(response == 1) {
                    alert("Updated");
                    location.reload();
                } else {
                    alert("Not Updated! Please Try Again After Some Time");
                }
            }
        });
       // $(location).attr('href', "updateInventory.php?styleId=" + document.getElementById('styleId').value + "&colorId=" + document.getElementById('colorId').value + "<?php if (isset($_REQUEST['unitId']) && $_REQUEST['unitId'] != '') echo '&unitId=' . $_REQUEST['unitId'];?>");
    }
    
    function Delete() 
    {
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
                    //return false;
                    // console.log(response);
                    if (response == 1) {
                        alert("unit Deleted SuccessFully");
                        location.reload();
                    } else {
                        alert("unit Not Deleted Please Empty the unit first");
                    }
                }
            });
        } else {
            console.log('cancel');
        }
    }
    
</script>

<script type="text/javascript">
    var unique_id = 0;
    
    function print_content(stylId, loc, unitId) {
        //alert($('#unique_0').val());
        //alert($('#unique_30').val());
    
        var data_ = "";
        for (i = 0; ; i++) {
            var data = $('#unique_' + i).val();
            if (typeof(data) != "undefined" && data !== null) {
                if (i > 0) data_ += ",";
                data_ += data;
            }
            else {
                break;
            }
        }
        // alert(data_);
    
        if (stylId == 'null' || unitId == 'null') {
            alert('error');
        }
        else {
            //alert(window.location);
            var clrId = $('#color option[selected="selected"]').attr('data-color');
    
            location.assign("print.php" + '?styleId=' + stylId + '&colorId=' + clrId + '&unitId=' + unitId + '&location=' + loc + '&all_data=' + data_);
        }
    }
    
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
    }
    
    
    
    function AddQty(trId,type,cellId,i,j,data,locIndex,rowIndex,qty,invIdValue)
    {
        //alert(qty);
        //console.log(locIndex,cellId,data,type);
        // console.log(i,j,qty);
        //alert(data);
        //alert(invIdValue);
        switch(type)
        {
            case 'qty':
            {
                var abc="Abc:hfdfh \nBcd:jhyf";
                var tr = document.getElementById(trId);     
                var cell = tr.insertCell(cellId);
                var txtunit = document.createElement("input");
                cell.className = 'gridHeaderReportGrids allvaluesingrid';
                txtunit.type = "text";
                txtunit.name = "qty["+locIndex+"]["+rowIndex+"][]";
                txtunit.className = "txBxGrey eachcell"; 
                txtunit.id = 'unique_'+unique_id++;
    
                if(data != -1)
                {
                    data = data.replace(/::/g,'\n');
                    txtunit.title = data;
                }
                
                txtunit.value = qty ;
                cell.appendChild(txtunit);
                
                txtunit = document.createElement("input");           
                txtunit.type = "hidden";
                txtunit.name = "invId["+locIndex+"]["+rowIndex+"][]";
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
            case 'qtyDummy':
            {
                var trd = document.getElementById('qtyDummy'+locIndex);
                if(trd !=null)
                {
                    //alert(trd+' '+locIndex);
                    var cell = trd.insertCell(cellId);
                    cell.className = 'gridHeaderReportGrids2';
                    cell.innerHTML="&nbsp;";
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
    
    
    
    
        if ($('#unit_num').val() == 0 || $('#unit_num').val() == 'undefined') {
            $('#update_inventory').hide();
            $('#print').hide();
        }
        if ($('#unit_num').val() == 0 || $('#unit_num').val() == 'undefined') {
            $('#view_details').hide();
            $("#hide").css("display", "block");
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
            for ($i = 0; $i < count($locArr); $i++, $locIndex++) 
            {
                $rowIndex = 0;
                //echo ">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>".$locIndex."    ";
                if (count($data_opt1Size) > 0) 
                {
                    for ($j = 0; $j < count($data_opt1Size); $j++) 
                    {
                        InsertQty($data_mainSize, $data_inv, $data_opt1Size[$j]['opt1SizeId'], $locArr[$i], $locIndex, $rowIndex,$_store);
                        $rowIndex++;
                    }
                }
                else 
                {
                    InsertQty($data_mainSize, $data_inv, 0, $locArr[$i], $locIndex, $rowIndex,$_store);
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
        
        
        
        function InsertQty($data_mainsize,$data_inv,$rowSizeId,$locId,$locIndex,$rowIndex,$store)
        {
            $mainIndex = 0;
            for($i=0;$i < count($data_mainsize);$i++)
            {
                $invFound=0;
                for($j=0; $j < count($data_inv);$j++)
                {
                    if($rowSizeId > 0)
                    {
                        // if($data_inv[$j]['quantity'] == 10)
                        // {
                        //  echo "|||||||||||||| ".$locId."--".$data_inv[$j]['locationId']."--".$data_inv[$j]['quantity']." |||||||||||";
                        // }
        
                        if(($data_inv[$j]['sizeScaleId'] == $data_mainsize[$i]['mainSizeId']) && 
                            ($locId == $data_inv[$j]['locationId']) && 
                            ($rowSizeId == $data_inv[$j]['opt1ScaleId']))
                        {
                           
        
                        //echo "|||||||||||||| ".$locId."--".$data_inv[$j]['locationId']."--".$data_inv[$j]['quantity']." |||||||||||";
                            
                            $invFound = 1;
        
                            if($data_inv[$j]['inventoryId'] != "")
                            {
                                if($data_inv[$j]['quantity'] != "" )
                                {
                                    echo "StoreInitialValues($locIndex,$rowIndex,'".$data_inv[$j]['quantity']."','".$data_inv[$j]['newQty']."');";
                                    // echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'","qty",'.$mainIndex.','.$locIndex.','.$rowIndex.',"'.$data_inv[$j]['quantity'].'",'.$data_inv[$j]['inventoryId'].');';
        
                                    $data = "";
                                    if(!isset($_GET['unitId']) || $_GET['unitId']=='0')
                                    {
                                        for($ii=0 ; $ii<count($store); $ii++)
                                        {
        
                                            //echo($_store['mainSizeId'].",".);
        
                                            if($store[$ii]['mainSizeId']==$data_mainsize[$i]['mainSizeId'] &&
                                               $store[$ii]['opt1ScaleId']==$data_inv[$j]['opt1ScaleId'])
                                            {
                                                $data .= 'unit='.$store[$ii]['unit']." : ".$store[$ii]['quantity']."::";
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $data = "-1";
                                    }
                                    // echo "***************************************************";
                                    // var_dump($data);
                                    // //var_dump($data_inv[$j]['quantity']);
                                    // echo "***************************************************";
                                    // exit();
                                    if($data == '')
                                        $data = "-1";
        
                                    echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'",
                                                "qty",
                                                '.$mainIndex.',
                                                '.$i.',
                                                '.$j.',
                                                "'.$data.'",
                                                '.$locIndex.',
                                                '.$rowIndex.',
                                                "'.$data_inv[$j]['quantity'].'",
                                                '.$data_inv[$j]['inventoryId'].');';
        
                                    //echo "*****************************************************";
                                    
        
                                }
                                else
                                {
                                    echo "StoreInitialValues($locIndex,$rowIndex,0,'".$data_inv[$j]['newQty']."');";
                                    // echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'","qty",'.$mainIndex.','.$locIndex.','.$rowIndex.',0,'.$data_inv[$j]['inventoryId'].');';
                                    $data = "-1";
                                    echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'",
                                            "qty",
                                            '.$mainIndex.',
                                            '.$i.',
                                            '.$j.',
                                            "'.$data.'",
                                            '.$locIndex.',
                                            '.$rowIndex.',
                                            0,
                                            '.$data_inv[$j]['inventoryId'].');';
                                }
                            }
                            else
                            {
                                echo "StoreInitialValues($locIndex,$rowIndex,0,0);";
                                // echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'","qty",'.$mainIndex.','.$locIndex.','.$rowIndex.',0,0);';
        
        
        
                                $data = "-1";
                                echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'",
                                        "qty",
                                        '.$mainIndex.',
                                        '.$i.',
                                        '.$j.',
                                        "'.$data.'",
                                        '.$locIndex.',
                                        '.$rowIndex.',
                                        0,
                                        0);';
                            }
                            break;
                        }
                        
                    }
                    else
                    {
                        if($data_inv[$j]['sizeScaleId'] == $data_mainsize[$i]['mainSizeId'] && ($locId == $data_inv[$j]['locationId']) && ("" == $data_inv[$j]['opt1ScaleId']))
                        {
                            $invFound = 1;
                            if($data_inv[$j]['inventoryId'] != "")
                            {
                                if($data_inv[$j]['quantity'] != "" )
                                {
                                    echo "StoreInitialValues($locIndex,$rowIndex,'".$data_inv[$j]['quantity']."','".$data_inv[$j]['newQty']."');";
                                    //echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'","qty",'.$mainIndex.','.$locIndex.','.$rowIndex.',"'.$data_inv[$j]['quantity'].'",'.$data_inv[$j]['inventoryId'].');';
        
        
                                    $data = "-1";
                                    echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'",
                                        "qty",
                                        '.$mainIndex.',
                                        '.$i.',
                                        '.$j.',
                                        "'.$data.'",
                                        '.$locIndex.',
                                        '.$rowIndex.',
                                        "'.$data_inv[$j]['quantity'].'",
                                        '.$data_inv[$j]['inventoryId'].');';
        
                                    
                                }
                                else
                                {
                                    echo "StoreInitialValues($locIndex,$rowIndex,0,'".$data_inv[$j]['newQty']."');";
                                    //echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'","qty",'.$mainIndex.','.$locIndex.','.$rowIndex.',0,'.$data_inv[$j]['inventoryId'].');';
        
                                    $data = "-1";
                                    echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'",
                                        "qty",
                                        '.$mainIndex.',
                                        '.$i.',
                                        '.$j.',
                                        "'.$data.'",
                                        '.$locIndex.',
                                        '.$rowIndex.',
                                        0,
                                        '.$data_inv[$j]['inventoryId'].');';
                                }
                                
                            }
                            else
                            {
                                echo "StoreInitialValues($locIndex,$rowIndex,0,0);";
                                //echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'","qty",'.$mainIndex.','.$locIndex.','.$rowIndex.',0,0);';
        
                                $data = "-1";
                                echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'",
                                        "qty",
                                        '.$mainIndex.',
                                        '.$i.',
                                        '.$j.',
                                        "'.$data.'",
                                        '.$locIndex.',
                                        '.$rowIndex.',
                                        0,
                                        0);';
        
                                
                            }
                            break;
                        }
                    }           
                }
                if(!$invFound)
                {
                    echo "StoreInitialValues($locIndex,$rowIndex,0,0);";
                    // echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'","qty",'.$mainIndex.','.$locIndex.','.$rowIndex.',0,0);';
        
                    $data = "-1";
                    echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'",
                            "qty",
                            '.$mainIndex.',
                            '.$i.',
                            '.$j.',
                            "'.$data.'",
                            '.$locIndex.',
                            '.$rowIndex.',
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
    
        $("#unit_num").change(function () {
    
            $("#unitId_mail").val($("#unit_num").val());
            PostRequest();
    
        });
        $("#sConveyor").change(function () {
            PostRequest();
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
            if($("#sConveyor").val() != undefined) {
                conveyor = $("#sConveyor").val();
            }
            var dataString = 'styleId=' + stylId + '&colorId=' + clrId + '&unitId=' + unitId+ '&conveyor='+conveyor;
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
    
    }
    
    // if code supported, link in the style sheet and call the init function onload
    if (dw_scrollObj.isSupported()) {
        //dw_Util.writeStyleSheet('css/scroll.css');
        dw_Event.add(window, 'load', init_dw_Scroll);
    }
</script>
<script type="text/javascript">
    $(function () {
        $("#inventoryForm").submit(function (e) {
            
            var location_id = $('#_location_id').val();
            //var inventory_id = $('#_inventory_id').val();
            var location_details_id = $('#_location_details_id').val();
    
    
            //alert(location_id+' '+location_details_id);
    
            var unitId = 0;
            if ($("#unit_num").val() != undefined) {
                unitId = $("#unit_num").val();
            }
            //alert(unitId);
            var row = "<?php echo $data_product[0]['row']; ?>";
            var room = "<?php echo $data_product[0]['room']; ?>";
            var shelf = "<?php echo $data_product[0]['shelf']; ?>";
            var rack = "<?php echo $data_product[0]['rack']; ?>";
            
            dataString = $("#inventoryForm").serialize();
            dataString += "&type=e";
            dataString += "&unitId=" + unitId;
            dataString += "&location_id=" + location_id;
            //dataString += "&inventory_id=" + inventory_id;
            dataString += "&location_details_id=" + location_details_id;
            //console.log(dataString);
    
            //var data_ = "";
            flag = 0;
            for (i = 0; ; i++) 
            {
                var data = $('#unique_' + i).val();
                if (typeof(data) != "undefined" && data !== null ) 
                {
                    if(data < 0)
                    {
                        flag = 1;
                        break;
                    }
                }                    
                else 
                {
                    break;
                }
            }
    
            if(unitId == '')
            {
                //alert(row+' '+rack+' '+' '+room+' '+shelf);
                //alert('here');
                alert('unitId is null! not submiting the form');
            }
            else if(flag == 1)
            {
                alert('Negative value not accepted!');
            }
            else
            {
                //alert('in else');
                $("#message").html("<div class='errorMessage'><strong>Processing, Please wait...!</strong></div>");
                //alert(location_id)
                //alert('*********');
                //alert(location_id+' '+inventory_id+' '+location_details_id+' '+document.getElementById('styleId').value);
                $.ajax({
                type: "POST",
                url: "invReportSubmit.php",
                data: dataString,
                dataType: "json",
                success: function (data) 
                {
                    
                    
                    //return false;
                    console.log(data);
                    if (data != null) 
                    {
                        if (data[0].name || data[0].error) 
                        {
                            $("#message").html("<div class='errorMessage'><strong>Sorry, " + data[0].name + data[0].error + "</strong></div>");
                            if (data[0].flag) {
                                //console.log('first');
                                $.ajax({
                                    url: "newStorageSubmit.php?type=a&styleId=" + document.getElementById('styleId').value + "&colorId=" + document.getElementById('colorId').value + "&unitId=" + unitId + "&row=" + row + "&rack=" + rack + "&room=" + room + "&shelf=" + shelf,
                                    type: "GET",
                                    success: function (data) {
                                        //return false;
                                        console.log(data);
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
                            }
                        } 
                        else 
                        {
                            if (data[0].flag) {
                                $("#message").html("<div class='successMessage'><strong>Inventory Quantity Updated. Thank you.</strong></div>");
                                $.ajax({
                                    url: "newStorageSubmit.php?type=a&styleId=" + document.getElementById('styleId').value + "&colorId=" + document.getElementById('colorId').value + "&unitId=" + unitId + "&row=" + row + "&rack=" + rack + "&room=" + room + "&shelf=" + shelf,
                                    type: "GET",
                                    success: function (data) {
                                       //return false;
                                       console.log(data);
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
                                $("#message").html("<div class='successMessage'><strong> All inventorys are up to date...</strong></div>");
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
<?php
    function logCheckOStyle($styleId) {
        $server_URL = "http://127.0.0.1:4569";
    $db_server = "localhost";
    $db_name = "php_intranet_uniformsourcing";
    $db_uname= "globaluniformuser";    
    $db_pass= "globaluniformpassword";   
    try{
        $connection = pg_connect("host = $db_server ".
                             "dbname = $db_name ".
                             "user = $db_uname ".
                             "password = $db_pass");
    
    }
    catch(\Exception $e)
    {
        var_dump($e->getMessage());
    }
    
        $sql='';
        $sql='select * from "tbl_invScaleSize" where "sizeScaleId" ='.$styleId.'LIMIT 1';
        if(!($resultoldinv=pg_query($connection,$sql))){
                //echo "no";
            }
            else{
                //echo "yes";
            }
            $rowoldinv = pg_fetch_row($resultoldinv);
            $oldinv=$rowoldinv;
            pg_free_result($resultoldinv);
            echo $oldinv['2'];
          
            
    }
    
    
    
    ?>
<?php
    function logCheckNStyle($styleId) {
        $server_URL = "http://127.0.0.1:4569";
    $db_server = "localhost";
    $db_name = "php_intranet_uniformsourcing";
    $db_uname= "globaluniformuser";    
    $db_pass= "globaluniformpassword";   
    try{
        $connection = pg_connect("host = $db_server ".
                             "dbname = $db_name ".
                             "user = $db_uname ".
                             "password = $db_pass");
    
    }
    catch(\Exception $e)
    {
        var_dump($e->getMessage());
    }
    
        $sql='';
        $sql='select * from "tbl_invScaleSize" where "sizeScaleId" ='.$styleId.'LIMIT 1';
        if(!($resultoldinv=pg_query($connection,$sql))){
                //echo "no";
            }
            else{
                //echo "yes";
            }
            $rowoldinv = pg_fetch_row($resultoldinv);
            $oldinv=$rowoldinv;
            pg_free_result($resultoldinv);
            echo $oldinv['3'];
          
            
    }
    
    
    
    ?>
<?php require('../../trailer.php'); ?>
