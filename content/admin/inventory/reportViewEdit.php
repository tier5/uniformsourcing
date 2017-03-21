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

    if (isset($_GET['boxId']) && $_GET['boxId'] != '0') {
        $search .= " and st.\"box\"='" . $_GET['boxId'] . "'";
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


    $sql = 'select distinct box from "tbl_invStorage" where "styleId"=' . $_GET['styleId'];
    if ($_GET['colorId'] > 0) {
        $sql .= ' and "colorId"=' . $_GET['colorId'];
    } else if (count($data_color) > 0) {
        $sql .= ' and "colorId"=' . $data_color[0]['colorId'];
    }
    $sql .= ' order by box asc';
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
//echo "<pre>"; print_r($data_mainSize);


$tableWidth = $totalScale * 100;


//query changed -- added 'where "styleId"='.$_GET['styleId']';

$sql = 'select "inventoryId",quantity,"newQty","isStorage" from "tbl_inventory" where "styleId"='.$_GET['styleId'];

if (!($result = pg_query($connection, $sql))) {
    print("Failed invQuery: " . pg_last_error($connection));
    exit;
}
while ($row = pg_fetch_array($result)) {
    $data_inv[] = $row;
}

// echo "<pre>";print_r($data_inv);
// exit();

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
        if (isset($_GET['boxId']) && $_GET['boxId'] != '0') {
            $query .= ',st."wareHouseQty" as st_quantity ';
        }

        $query .= ',inv.quantity, inv."newQty" from "tbl_inventory" as inv ';
        if (isset($_GET['boxId']) && $_GET['boxId'] != '0') {
            $query .= ' left join "tbl_invStorage" as st on st."invId"=inv."inventoryId" ';
        }
        $query .= ' where inv."styleId"=' . $data_style['styleId'] . ' and inv."isActive"=1' . $search . ' order by "inventoryId"';
    } else {
        $clrId = $data_color[0]['colorId'];
        $query = 'select "inventoryId", "sizeScaleId", price, "locationId","opt1ScaleId", "opt2ScaleId", quantity, "newQty" from "tbl_inventory" where "styleId"=' . $data_style['styleId'] . ' and "colorId"=' . $data_color[0]['colorId'] . '  and "isActive"=1 order by "inventoryId"';
    }
    // echo $query;
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
if ($data_style['locationIds'] != "") {
    $locArr = explode(",", $data_style['locationIds']);
}
if (!isset($_GET['boxId']) || $_GET['boxId'] != '0') {
    $query = 'select * from "tbl_invStorage" WHERE box=' . "'" . $_GET['boxId'] . "'";

    if (!($resultProduct = pg_query($connection, $query))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($rowProduct = pg_fetch_array($resultProduct)) {
        $data_product[] = $rowProduct;
    }
    pg_free_result($rowProduct);

}


    $sql = 'select distinct "box" , "wareHouseQty" as "quantity",
           "opt1ScaleId",
           "sizeScaleId" as "mainSizeId",
           "invId","styleId" from "tbl_invStorage" where "styleId"='.$_GET['styleId'].' ORDER BY box';
    if(!($result=pg_query($connection,$sql)))
    {
        print("Failed StyleQuery: " . pg_last_error($connection));
        exit;
    }
    while($row = pg_fetch_array($result))
    {
        $_store[]=$row; // -------------------------- data_color ---------
    }
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

?>
<script type="text/javascript" src="<?php echo $mydirectory; ?>/js/jquery-ui.min-1.8.2.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory; ?>/js/samplerequest.js"></script>




<table width="90%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td align="left"><input type="button" value="Back" onclick="location.href='reports.php'"/></td>
        <td>&nbsp;</td>
        <td align="right"><label>
                <input type="button" name="send-email" id="send-email" value="Send Email"/>
                &nbsp;&nbsp; </label></td>
    </tr>
</table>


<div id="inventory_form" class="col-md-10" style="display: none">

    <div id="warehouse_form" style="display: none">
        <label>Location: </label>
        <select>
            <option>---All Locations---</option>
            <option>Location 1</option>
            <option>Location 2</option>
            <option>Location 3</option>
        </select>
        <br>

        <label>Warehouse :</label>
        <select>
            <option>---All Warehouse---</option>
            <option>Warehouse 1</option>
            <option>Warehouse 2</option>
            <option>Warehouse 3</option>
        </select>
        <br>

        <label>Box# </label>
        <input type="text" name="_box">
        <br>

        <label>Row# </label>
        <input type="text" name="_row">
        <br>

        <label>Rack# </label>
        <input type="text" name="_rack">
        <br>

        <label>Shelf# </label>
        <input type="text" name="_shelf">
        <br>

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
        <td align="center"><font size="5">Report</font><font size="5"> View/Edit <br>
                <br>
            </font>
            <fieldset style="margin:10px;">
                <table width="95%" border="0" cellspacing="0" cellpadding="0">

                    <tr>


                        <!-- print functionality -->
                        <button class="pull-right" id="print"
                                onclick="print_content('<?php echo $_GET['styleId']; ?>'
                                    ,'<?php echo $data_loc[$loc_identity]['name'] ?>'
                                    ,'<?php if (isset($_GET['boxId'])) echo $_GET['boxId'];
                                else echo 'null' ?>')"
                                class="pull-right">Print
                        </button>


                        <form id="optForm" method="post">
                            <td>Style:</td>

                            <td><h1><?php echo $data_style['styleNumber']; ?></h1></td>

                            <?php if ($data_style['barcode'] != "") { ?>

                                <td width="60">Barcode:</td>

                                <td><h1><img width="100" height="100"
                                             src="../../uploadFiles/inventory/images/<?php echo $data_style['barcode']; ?>">
                                    </h1></td>
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
                                    </select>&nbsp;&nbsp;&nbsp;</div>
                            </td>
                            <td>
                                Box #:&nbsp;<select name="box_num" id="box_num">
                                    <option value="0">---- All Boxes # ----</option>
                                    <?php
                                    for ($i = 0; $i < count($data_storage); $i++) {
                                        if ($data_storage[$i]['box'] != "")
                                            echo '<option value="' . $data_storage[$i]['box'] . '"';
                                        if (isset($_REQUEST['boxId']) && $_REQUEST['boxId'] == $data_storage[$i]['box']) echo ' selected="selected" ';
                                        echo '>' . $data_storage[$i]['box'] . '</option>';
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
                        if (!isset($_GET['boxId']) || $_GET['boxId'] != '0')
                        {
                        ?>
                    <tr id="view_details">
                        <td>Room: <input type="text" id="updateroom" value="<?php echo $data_product[0]['room']; ?>"/></td>
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
                    <?php
                    } else {
                        ?>
                        <tr>
                            <td>
                                <!--button type="button" id="newInventory" class="btn btn-success">Add new Inventory</button--> </td>
                            <td>&nbsp; </td>
                            <td>&nbsp;</td>
                            <td>&nbsp; </td>
                            <td>&nbsp;</td>
                        </tr>
                    <?php } ?>
                    <tr id="hide">
                        <td>
                            <button type="button" id="newInventory" onclick="addInventory()"
                                    class="btn btn-success">Add new Inventory
                            </button>
                        </td>
                        <td>&nbsp; </td>
                        <td>&nbsp;</td>
                        <td>&nbsp; </td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr id="inventoryDetails" style="display: none">
                        <td>Box Name: <input type="text" id="newBox"></td>
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
                            </button>
                    </tr>
                    </tr>

                </table>
            </fieldset>
            <form id="inventoryForm">
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
                                if (!isset($_GET['boxId']) || $_GET['boxId'] != '0') {
                                    ?>
                                    <tr>
                                        <td>
                                            <input id="update_inventory" width="117" height="98"
                                                   type="image"
                                                   src="<?php echo $mydirectory; ?>/images/updtInvbutton.jpg"
                                                   alt="Submit button"/>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>


                            <?php } ?>
                                    </table>
                                </td>
                                <td width="10"></td>
                                <td>
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
                                                            <td class="gridHeaderReportGrids3"><?php echo $data_loc[$loc_i]['name'];
                                                                $loc_identity = $loc_i; ?></td>
                                                            <?php
                                                            if (count($data_opt1Size) > 0) {
                                                                for ($j = 0; $j < count($data_opt1Size); $j++) {
                                                                    if ($j != 0) {
                                                                        ?>
                                                                        <tr>
                                                                            <td class="gridHeaderReportGrids3">
                                                                                &nbsp;</td>
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
        <input type="hidden" name="boxId" id="boxid_mail" value="<?php echo $_GET['boxId']; ?>"/>
        <input type="hidden" name="styleId" id="styleId_mail" value="<?php echo $_GET['styleId']; ?>"/>
    </form>
</div>


<script>


    $('#newInventory').click(function(e){

        $('#inventory_form').show();
        $('#warehouse_form').show();

        
    });
        
    function Update() {
        var room = $('#updateroom').val();
        if(room == '') {
            alert("Please Provide a Room");
            return false;
        }
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
        $.ajax({
            url: 'editRoom.php',
            type:"post",
            data: {
                room: room,
                rack: rack,
                row: row,
                self: self,
                boxId: "<?php echo $_REQUEST['boxId'];?>",
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
       // $(location).attr('href', "updateInventory.php?styleId=" + document.getElementById('styleId').value + "&colorId=" + document.getElementById('colorId').value + "<?php if (isset($_REQUEST['boxId']) && $_REQUEST['boxId'] != '') echo '&boxId=' . $_REQUEST['boxId'];?>");
    }

    function Delete() {
        if (confirm("Are you Sure you want to delete this box") == true) {
            $.ajax({
                url: "deleteInventory.php",
                type: "post",
                data: {
                    styleId: document.getElementById('styleId').value,
                    colorId: document.getElementById('colorId').value,
                    boxId: "<?php echo $_REQUEST['boxId'];?>"
                },
                success: function (response) {
                    //return false;
                    // console.log(response);
                    if (response == 1) {
                        alert("Box Deleted SuccessFully");
                        location.reload();
                    } else {
                        alert("Box Not Deleted Please Empty the box first");
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

    function print_content(stylId, loc, boxId) {
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

        if (stylId == 'null' || boxId == 'null') {
            alert('error');
        }
        else {
            //alert(window.location);
            var clrId = $('#color option[selected="selected"]').attr('data-color');

            location.assign("print.php" + '?styleId=' + stylId + '&colorId=' + clrId + '&boxId=' + boxId + '&location=' + loc + '&all_data=' + data_);
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
                var txtBox = document.createElement("input");
                txtBox.type = "text";
                txtBox.className = "txBxWhite";
                txtBox.value = "";
                cell.appendChild(txtBox);
                break;
            }
            case 'price': {
                var trPrice = document.getElementById('priceTop');
                var cell = trPrice.insertCell(cellId);
                cell.className = 'gridHeaderReportGrids2';
                var txtBox = document.createElement("input");
                txtBox.type = "text";
                txtBox.className = "txBxWhite";
                txtBox.name = 'price[]';
                txtBox.value = value;
                cell.appendChild(txtBox);
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
        //console.log(cellId,data);
        //console.log(i,j,qty);
        //alert(data);
        //alert(invIdValue);
        switch(type)
        {
            case 'qty':
            {
                var abc="Abc:hfdfh \nBcd:jhyf";
                var tr = document.getElementById(trId);     
                var cell = tr.insertCell(cellId);
                var txtBox = document.createElement("input");
                cell.className = 'gridHeaderReportGrids';
                txtBox.type = "text";
                txtBox.name = "qty["+locIndex+"]["+rowIndex+"][]";
                txtBox.className = "txBxGrey eachcell"; 
                txtBox.id = 'unique_'+unique_id++;

                if(data != -1)
                {
                    data = data.replace(/::/g,'\n');
                    txtBox.title = data;
                }
                
                txtBox.value = qty ;
                cell.appendChild(txtBox);
                
                txtBox = document.createElement("input");           
                txtBox.type = "hidden";
                txtBox.name = "invId["+locIndex+"]["+rowIndex+"][]";
                txtBox.value = invIdValue;
                cell.appendChild(txtBox);   
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

        $(location).attr('href', "storage.php?styleId=" + document.getElementById('styleId').value + "&colorId=" + document.getElementById('colorId').value + "&invId=" + inventoryId + "<?php if (isset($_REQUEST['boxId']) && $_REQUEST['boxId'] != '') echo '&boxId=' . $_REQUEST['boxId'];?>");
    }
</script>
    <script type="text/javascript">
        $(document).ready(function () {


            if ($('#box_num').val() == 0 || $('#box_num').val() == 'undefined') {
                $('#update_inventory').hide();
                $('#print').hide();
            }
            if ($('#box_num').val() == 0 || $('#box_num').val() == 'undefined') {
                $('#view_details').hide();
                $("#hide").css("display", "block");
            } else {
                $("#hide").css("display", "none");
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
                    if (count($data_opt1Size) > 0) {
                        for ($j = 0; $j < count($data_opt1Size); $j++) {
                            InsertQty($data_mainSize, $data_inv, $data_opt1Size[$j]['opt1SizeId'], $locArr[$i], $locIndex, $rowIndex,$_store);
                            $rowIndex++;
                        }
                    } else {
                        InsertQty($data_mainSize, $data_inv, 0, $locArr[$i], $locIndex, $rowIndex,$_store);
                        $rowIndex++;
                    }
                    echo 'AddQty("dummy","qtyDummy",' . $mainIndex . ',' . $locIndex . ',' . $rowIndex . ',0,0);';
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
                            if(($data_inv[$j]['sizeScaleId'] == $data_mainsize[$i]['mainSizeId']) && 
                                ($locId == $data_inv[$j]['locationId']) && 
                                ($rowSizeId == $data_inv[$j]['opt1ScaleId']))
                            {
                                $invFound = 1;

                                if($data_inv[$j]['inventoryId'] != "")
                                {
                                    if($data_inv[$j]['quantity'] != "" )
                                    {
                                        echo "StoreInitialValues($locIndex,$rowIndex,'".$data_inv[$j]['quantity']."','".$data_inv[$j]['newQty']."');";
                                        // echo 'AddQty("qty_'.$locIndex.'_'.$rowIndex.'","qty",'.$mainIndex.','.$locIndex.','.$rowIndex.',"'.$data_inv[$j]['quantity'].'",'.$data_inv[$j]['inventoryId'].');';

                                        $data = "";
                                        if(!isset($_GET['boxId']) || $_GET['boxId']=='0')
                                        {
                                            for($ii=0 ; $ii<count($store); $ii++)
                                            {

                                                //echo($_store['mainSizeId'].",".);

                                                if($store[$ii]['mainSizeId']==$data_mainsize[$i]['mainSizeId'] &&
                                                   $store[$ii]['opt1ScaleId']==$data_inv[$j]['opt1ScaleId'])
                                                {
                                                    $data .= 'box='.$store[$ii]['box']." : ".$store[$ii]['quantity']."::";
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
                                        // // exit();
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

            $("#box_num").change(function () {

                $("#boxid_mail").val($("#box_num").val());
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

                var boxId = 0;
                if ($("#box_num").val() != undefined) {
                    boxId = $("#box_num").val();
                }
                var conveyor = 0;
                if($("#sConveyor").val() != undefined) {
                    conveyor = $("#sConveyor").val();
                }
                var dataString = 'styleId=' + stylId + '&colorId=' + clrId + '&boxId=' + boxId+ '&conveyor='+conveyor;
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
            $("#inventoryForm").submit(function () {
                var boxId = 0;
                if ($("#box_num").val() != undefined) {
                    boxId = $("#box_num").val();
                }
                var row = "<?php echo $data_product[0]['row']; ?>";
                var room = "<?php echo $data_product[0]['room']; ?>";
                var shelf = "<?php echo $data_product[0]['shelf']; ?>";
                var rack = "<?php echo $data_product[0]['rack']; ?>";
                $("#message").html("<div class='errorMessage'><strong>Processing, Please wait...!</strong></div>");
                dataString = $("#inventoryForm").serialize();
                dataString += "&type=e";
                dataString += "&boxId=" + boxId;
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
                                    //console.log('first');
                                    $.ajax({
                                        url: "newStorageSubmit.php?type=a&styleId=" + document.getElementById('styleId').value + "&colorId=" + document.getElementById('colorId').value + "&boxId=" + boxId + "&row=" + row + "&rack=" + rack + "&room=" + room + "&shelf=" + shelf,
                                        type: "GET",
                                        success: function (data) {
                                            return false;
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
                                    //$(location).attr('href',"newStorageSubmit.php?type=a&styleId="+document.getElementById('styleId').value+"&colorId="+document.getElementById('colorId').value+"&boxId="+boxId+"&row="+row+"&rack="+rack+"&room="+room+"&shelf="+shelf);
                                }
                            } else {
                                if (data[0].flag) {
                                    $("#message").html("<div class='successMessage'><strong>Inventory Quantity Updated. Thank you.</strong></div>");
                                    $.ajax({
                                        url: "newStorageSubmit.php?type=a&styleId=" + document.getElementById('styleId').value + "&colorId=" + document.getElementById('colorId').value + "&boxId=" + boxId + "&row=" + row + "&rack=" + rack + "&room=" + room + "&shelf=" + shelf,
                                        type: "GET",
                                        success: function (data) {
                                           return false;
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
                                    //$(location).attr('href',"newStorageSubmit.php?type=a&styleId="+document.getElementById('styleId').value+"&colorId="+document.getElementById('colorId').value+"&boxId="+boxId+"&row="+row+"&rack="+rack+"&room="+room+"&shelf="+shelf);
                                } else {
                                    $("#message").html("<div class='successMessage'><strong> All inventorys are up to date...</strong></div>");
                                }
                            }
                        } else {
                            $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
                        }
                    }
                });
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
            var boxId = $("#newBox").val();
            if (boxId == '') {
                alert("Please Provide a box name");
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
                    box: boxId,
                    room: room,
                    row: row,
                    shelf: shelf,
                    rack: rack,
                    styleId: styleId,
                    colorId: colorId
                },
                success: function (data) {
                    if (data == 1) {
                        $(location).attr('href', "reportViewEdit.php?styleId=" + styleId + "&colorId=" + colorId + "&boxId=" + boxId);
                        $("#message").html("<div class='successMessage'><strong>Inventory Added. Thank you.</strong></div>");
                    } else {
                        $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
                    }
                }
            })

        }

    </script>

<?php require('../../trailer.php'); ?>