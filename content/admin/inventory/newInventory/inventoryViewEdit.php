<?php
require('Application.php');
require('../../../header.php');
if (isset($_GET['styleId']) && $_GET['styleId'] != '' && $_GET['styleId'] != 0) {
    //Select all data for Style
    $sql = '';
    $sql = "SELECT * FROM \"tbl_invStyle\" WHERE \"styleId\" = '" . $_GET['styleId'] . "'";
    if (!($resultStyle = pg_query($connection, $sql))) {
        echo '<h1> Failed style Query: </h1><h2>' . pg_last_error($connection) . '</h2>';
        exit;
    }
    $dataStyle = pg_fetch_array($resultStyle);
    pg_free_result($resultStyle);
    //GET COLOR DATA
    $query = '';
    $query = 'Select * from "tbl_invColor" where "styleId"=' . $dataStyle['styleId'];
    if (!($resultColor = pg_query($connection, $query))) {
        print("<h1>Failed Color Query: </h1><h2>" . pg_last_error($connection)) . '</h2>';
        exit;
    }
    while ($row = pg_fetch_array($resultColor)) {
        $dataColor[] = $row;
    }
    pg_free_result($resultColor);
    //Fetch All Information for the style
    $sql = '';
    $sql = 'SELECT style.*,garment.*,client.* FROM "tbl_invStyle" style ' .
        ' LEFT JOIN "tbl_garment" garment on garment."garmentID" = style."garmentId" ' .
        ' LEFT JOIN "clientDB" client on client."ID" = style."clientId" ' .
        ' WHERE style."styleId"=' . $_GET['styleId'];
    if (!($resultInfo = pg_query($connection, $sql))) {
        echo '<h1> Failed Info Query: </h1><h2>' . pg_last_error($connection) . '</h2>';
        exit;
    }
    $dataInfo = pg_fetch_array($resultInfo);
    pg_free_result($resultInfo);
    //Fetch Size Scale Data
    if ($dataStyle['scaleNameId'] != '') {
        //Fetch Main size
        $query2 = 'Select "sizeScaleId" as "mainSizeId", "scaleSize" from "tbl_invScaleSize" where "scaleId"=' . $dataStyle['scaleNameId'] . ' and "scaleSize" IS NOT NULL  and "scaleSize" <>\'\'  order by "mainOrder","sizeScaleId"';
        if (!($result2 = pg_query($connection, $query2))) {
            print("Failed OptionQuery: " . pg_last_error($connection));
            exit;
        }
        while ($row2 = pg_fetch_array($result2)) {
            $dataMainSize[] = $row2;
        }
        pg_free_result($result2);
        //Fetch Opt size
        $query2 = 'Select "sizeScaleId" as "opt1SizeId", "opt1Size" from "tbl_invScaleSize" where "scaleId"=' . $dataStyle['scaleNameId'] . ' and "opt1Size" IS NOT NULL  and "opt1Size" <>\'\' order by "opt1Order","sizeScaleId"';
        if (!($result2 = pg_query($connection, $query2))) {
            print("Failed OptionQuery: " . pg_last_error($connection));
            exit;
        }
        while ($row2 = pg_fetch_array($result2)) {
            $dataOptSize[] = $row2;
        }
        pg_free_result($result2);
        $dataOptSizeId = array();
        foreach ($dataOptSize as $key => $val) {
            if (isset($val['opt1SizeId'])) {
                $dataOptSizeId[(int)$val['opt1SizeId']] = $val['opt1Size'];
            }
        }
        $dataMainSizeId = array();
        foreach ($dataMainSize as $key => $val) {
            if (isset($val['mainSizeId'])) {
                $dataMainSizeId[(int)$val['mainSizeId']] = $val['scaleSize'];
            }
        }
    } else {
        echo '<h1>No size available</h1>';
        die();
    }
    //Fetch All Location
    $sql = '';
    $sql = 'SELECT details.*,location.* FROM "locationDetails" details ' .
        'INNER JOIN "tbl_invLocation" location on location."locationId" = CAST(details."locationId" as INT)' .
        ' ORDER BY details."locationId" DESC';
    if (!($result = pg_query($connection, $sql))) {
        print("Failed location fetch Query: " . pg_last_error($connection));
        exit;
    }
    while ($row2 = pg_fetch_array($result)) {
        $allStorage[] = $row2;
    }
    pg_free_result($result);
    //Create actual array from the all storage
    $allLocation = [];
    if (count($allStorage) > 0) {
        foreach ($allStorage as $key => $value) {
            $allLocation[$key]['storageId'] = $value['id'];
            $allLocation[$key]['locationId'] = $value['locationId'];
            $allLocation[$key]['locationIdentifier'] = $value['identifier'];
            if ($value['warehouse'] != '') {
                $allLocation[$key]['storage'] = $value['warehouse'];
                $allLocation[$key]['type'] = 'warehouse';
            } elseif ($value['container'] != '') {
                $allLocation[$key]['storage'] = $value['container'];
                $allLocation[$key]['type'] = 'container';
            } else {
                $allLocation[$key]['storage'] = $value['conveyor'];
                $allLocation[$key]['type'] = 'conveyor';
            }
        }
    }
    //Fetch all information for the unit
    $sql = '';
    $sql = 'SELECT unit.*,details.*,location.* FROM "tbl_invUnit" unit' .
        ' INNER JOIN "locationDetails" details on details.id= unit."storageId" ' .
        ' INNER JOIN "tbl_invLocation" location on location."locationId"= CAST(details."locationId" as bigint)' .
        ' where unit."styleId"=' . $_GET['styleId'];
    if (isset($_GET['colorId']) && $_GET['colorId'] != 0) {
        $sql .= ' and unit."colorId"=' . $_GET['colorId'];
    }
    $sql .= ' and unit.merged=0';
    $sql .= ' ORDER BY unit.id';
    if (!($result = pg_query($connection, $sql))) {
        print("Failed location fetch Query: " . pg_last_error($connection));
        exit;
    }
    while ($row2 = pg_fetch_array($result)) {
        $allUnit[] = $row2;
    }
    pg_free_result($result);
    //Fetch Latest updated records for a Style
    $sql = '';
    $sql = 'SELECT * FROM "tbl_invUnit" unit' .
        ' LEFT JOIN "employeeDB" emp ON emp."employeeID"=unit."updatedBy"  ' .
        ' WHERE "styleId"=' . $_GET['styleId'] .
        ' order by "updatedAt" desc limit 1';
    if (!($result = pg_query($connection, $sql))) {
        print("Failed location fetch Query: " . pg_last_error($connection));
        exit;
    }
    $emp = pg_fetch_array($result);
    pg_free_result($result);
    //Get Location for a Unit
    if (isset($_GET['boxId']) && $_GET['boxId'] != 0) {
        $sql = '';
        $sql = 'SELECT * FROM "tbl_invUnit" unit' .
            ' LEFT JOIN "locationDetails" details on details.id = unit."storageId"' .
            ' LEFT JOIN "tbl_invLocation" location on location."locationId" = CAST(details."locationId" as bigint)' .
            ' WHERE unit.id=' . $_GET['boxId'];
        if (!($result = pg_query($connection, $sql))) {
            print("Failed location fetch Query: " . pg_last_error($connection));
            exit;
        }
        $location = pg_fetch_array($result);
        pg_free_result($result);
        $locationName = $location['name'];
        $storageType = $location['type'];
        $boxName = $location['identifier'] . '_' . $location[$storageType] . '_' . $location['box'];
        $rowName = $location['row'];
        $rackName = $location['rack'];
        $shelfName = $location['shelf'];
    } else {
        $locationName = 'All Location';
        $boxName = 'All Box';
        $storageType = 'allBox';
        $rowName = 'All Row';
        $rackName = 'All Rack';
        $shelfName = 'All Shelf';
    }
    //Get all Quantity For A unit
    $sql = '';
    $sql = 'SELECT unit.*,quantity.*,details.*,location.identifier FROM "tbl_invUnit" unit ';
    $sql .= ' LEFT JOIN "tbl_invQuantity" quantity on unit.id = quantity."boxId" ';
    $sql .= ' LEFT JOIN "locationDetails" details on unit."storageId" = details.id';
    $sql .= ' LEFT JOIN "tbl_invLocation" location on location."locationId"= CAST(details."locationId" as INT)';
    $sql .= ' WHERE unit."styleId"=' . $_GET['styleId'];
    if (isset($_GET['boxId']) && $_GET['boxId'] != 0) {
        $sql .= ' and unit.id=' . $_GET['boxId'];
    }
    if (isset($_GET['colorId']) && $_GET['colorId'] != 0) {
        $sql .= ' and unit."colorId"=' . $_GET['colorId'];
    }
    if (!($result = pg_query($connection, $sql))) {
        print("Failed location fetch Query: " . pg_last_error($connection));
        exit;
    }
    while ($row2 = pg_fetch_array($result)) {
        $data[] = $row2;
    }
    pg_free_result($result);
    $dataQuantity = [];
    $dataToolTip = [];
    foreach ($data as $key => $value) {
        $total = 0;
        if (isset($dataQuantity[$value['mainSizeId']][$value['optSizeId']])) {
            $total = $dataQuantity[$value['mainSizeId']][$value['optSizeId']] + $value['qty'];
            $dataQuantity[$value['mainSizeId']][$value['optSizeId']] = $total;
            if($value['qty'] > 0){
                $link = $dataToolTip[$value['mainSizeId']][$value['optSizeId']].'<br/>'.'<a href="http://'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['REQUEST_URI'].'&boxId='.$value[0].'">'.$value['identifier'].'_'.$value[$value['type']].'_'.$value['box'].' : '.$value['qty'].'</a>';
                $dataToolTip[$value['mainSizeId']][$value['optSizeId']] = $link;
            }
        } else {
            $dataQuantity[$value['mainSizeId']][$value['optSizeId']] = $value['qty'];
            if($value['qty'] > 0){
                $link = '<a href="http://'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['REQUEST_URI'].'&boxId='.$value[0].'">'.$value['identifier'].'_'.$value[$value['type']].'_'.$value['box'].' : '.$value['qty'].'</a>';
                $dataToolTip[$value['mainSizeId']][$value['optSizeId']] = $link ;
            }
        }
    }
    if (isset($_GET['boxId']) && $_GET['boxId'] != 0) {
        if(isset($_GET['colorId']) && $_GET['colorId'] != 0){
            $colorId = $_GET['colorId'];
        } else {
            $colorId = $dataColor[0]['colorId'];
        }
        $sql = '';
        $sql = 'SELECT unit.id as number,unit.box,unit.type,details.*,location.identifier FROM "tbl_invUnit" unit ' .
            ' LEFT JOIN "locationDetails" details ON details.id=unit."storageId" ' .
            'LEFT JOIN "tbl_invLocation" location ON location."locationId"= CAST(details."locationId" as INT) ' .
            'WHERE unit."styleId"=' . $_GET['styleId'] . ' and "colorId"='.$colorId.' and unit.id <>' . $_GET['boxId'] . '  and merged=0';
        if (!($result = pg_query($connection, $sql))) {
            print("Failed location fetch Query: " . pg_last_error($connection));
            exit;
        }
        while ($row2 = pg_fetch_array($result)) {
            $mergedLocation[] = $row2;
        }
        pg_free_result($result);
    }
    $sql = '';
    $sql = 'SELECT * FROM "tbl_invLocation"';
    if (!($result = pg_query($connection, $sql))) {
        print("Failed location fetch Query: " . pg_last_error($connection));
        exit;
    }
    while ($row2 = pg_fetch_array($result)) {
        $locationChange[] = $row2;
    }
    pg_free_result($result);
} else {
    echo '<h1>Please select a Style to view the Report</h1>';
    exit();
}
?>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.6/sweetalert2.min.css" />
<style>
    .inputQty {
        background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
        border: medium none;
        display: block;
        padding: 0;
        text-align: center;
        width: 100%;
    }
</style>
<style>
    table tr td{
        position: relative;
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
        transform: translate(-50%,0);
        left: 50%;
    }
    .tool:hover .tooltext {
        visibility: visible;
    }
    A:link {
        color: #fff;
        text-decoration: none;
    }
    a:focus, a:hover{color: #337ab7; text-decoration: underline;}
</style>
<div class="container">
    <div class="page-header">
        <h1 class="text-center">Report View / Edit (
            <small> <?php echo $dataStyle['styleNumber'] ?></small>
            )
        </h1>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-4 pull-left">
                <div class="form-group">
                    <label for="color" class="col-md-2 control-label"> Color: </label>
                    <div class="col-md-10">
                        <select class="form-control" name="color" id="color">
                            <?php
                            foreach ($dataColor as $color) {
                                echo '<option value="' . $color['colorId'] . '" data-image="' . $color['image'] . '"';
                                if (isset($_GET['colorId']) && $_GET['colorId'] == $color['colorId']) {
                                    echo ' selected="selected" ';
                                }
                                echo '>' . $color['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <label for="allBoxSelect" class="col-md-2 control-label">Box#:</label>
                <div class="col-md-10">
                    <select class="form-control" name="allBox" id="allBoxSelect">
                        <option value="0">------- All Box -------</option>
                        <?php
                        foreach ($allUnit as $unit) {
                            echo '<option value="' . $unit[0] . '" ';
                            if (isset($_GET['boxId']) && $_GET['boxId'] == $unit[0]) {
                                echo ' selected="selected" ';
                            }
                            echo '>' . $unit['box'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <?php
            if (isset($_GET['boxId']) && $_GET['boxId'] != 0) {
                ?>
                <div class="col-md-2">
                    <button type="button" class="btn btn-info btn-info btn-lg" id="mainInventory">
                        Main Inventory
                    </button>
                </div>
                <?php
            }
            ?>
            <div class="col-md-2 pull-right">
                <?php
                if (isset($_GET['boxId']) && $_GET['boxId'] != 0) {
                    ?>
                    <button class="btn btn-lg btn-success" data-box="<?php echo $_GET['boxId']; ?>" id="print">
                        Print
                    </button>
                    <?php
                } else {
                    ?>
                    <button class="btn btn-lg btn-success" data-toggle="modal" data-target="#addInventory">Add
                        Inventory
                    </button>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="center-block">
            <span id="errorMessage"></span>
        </div>
    </div>
    <div style="margin-top:15px"></div>
    <div class="panel panel-default panel-success">
        <div class="panel-heading ">
            <div class="panel-heading ">
                <div class="row">
                    <div class="col-md-10 pull-left">
                        <div class="row">
                            <div class="col-md-3 pull-left">
                                Style:
                                <strong>
                                    <?php echo $dataInfo['styleNumber']; ?>
                                </strong>
                            </div>
                            <div class="col-md-3">
                                Garment Type:
                                <strong>
                                    <?php echo $dataInfo['garmentName']; ?>
                                </strong>
                            </div>
                            <div class="col-md-3">
                                Client:
                                <strong>
                                    <?php echo $dataInfo['client']; ?>
                                </strong>
                            </div>
                            <div class="col-md-3 pull-right">
                                Gender:
                                <strong>
                                    <?php echo $dataInfo['sex'] ?>
                                </strong>
                            </div>
                        </div>
                        <div style="margin-top:20px;"></div>
                        <div class="row">
                            <div class="col-md-3 pull-left">
                                Color:
                                <strong>
                                    <span id="colorName"></span>
                                </strong>
                            </div>
                            <div class="col-md-3">
                                Employee:
                                <strong>
                                    <?php
                                    if ($emp != null) {
                                        echo $emp['username'];
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </strong>
                            </div>
                            <div class="col-md-3">
                                Date Entered:
                                <strong>
                                    <?php
                                    if ($emp != null) {
                                        echo date("F j, Y, g:i a", strtotime($emp['updatedAt']));
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </strong>
                            </div>
                            <div class="col-md-3 pull-right">
                                Location:
                                <strong>
                                    <?php
                                    echo $locationName;
                                    ?>
                                </strong>
                            </div>
                        </div>
                        <div style="margin-top:20px;"></div>
                        <div class="row">
                            <div class="col-md-3 pull-left">
                                Box#:
                                <strong>
                                    <?php
                                    echo $boxName;
                                    ?>
                                </strong>
                            </div>
                            <?php
                            if (isset($_GET['boxId']) && $_GET['boxId'] != 0) {
                                    ?>
                                    <div class="col-md-4" id="mergeDiv" style="display: none;">
                                        <?php
                                        if(count($mergedLocation) > 0) {
                                            ?>
                                            <label for="mergedLocation">Select a Box to Merge</label>
                                            <select name="merge" id="mergedLocation" class="form-control">
                                                <option value="0">Select a Box</option>
                                                <?php
                                                for ($i = 0; $i < count($mergedLocation); $i++) {
                                                    echo '<option value="' . $mergedLocation[$i]['number'] . '">' . $mergedLocation[$i]['identifier'] . '_' . $mergedLocation[$i][$mergedLocation[$i]['type']] . '_' . $mergedLocation[$i]['box'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                            <?php
                                        } else {
                                            ?>
                                            <div class="col-md-12">
                                                <strong>No Box to merge</strong>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                <?php
                                if(count($mergedLocation) > 0) {
                                    ?>
                                    <div class="col-md-3">
                                        <button type="button" id="merge" class="btn btn-warning btn-md">
                                            Merge
                                        </button>
                                    </div>
                                    <?php
                                }
                                ?>
                                <div class="col-md-3">
                                    <button type="button" id="deleteBox" class="btn btn-danger btn-md">
                                        Delete
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" data-toggle="modal" data-target="#locationUpdateModal" class="btn btn-info btn-md">
                                        Update Location
                                    </button>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <div style="margin-top:20px;"></div>
                        <div class="row">
                            <?php
                            if (isset($_GET['boxId']) && $_GET['boxId'] != 0) {
                                if ($storageType == 'warehouse' || $storageType == 'allBox') {
                                    ?>
                                    <div class="col-md-3">
                                        Row:
                                        <strong>
                                            <input type="text" id="updateRow" value="<?php echo $rowName; ?>">
                                        </strong>
                                    </div>
                                    <div class="col-md-3">
                                        Rack:
                                        <strong>
                                            <input type="text" id="updateRack" value="<?php echo $rackName; ?>">
                                        </strong>
                                    </div>
                                    <div class="col-md-3">
                                        Shelf:
                                        <strong>
                                            <input type="text" id="updateShelf" value="<?php echo $shelfName; ?>">
                                        </strong>
                                    </div>
                                    <?php
                                    if (isset($_GET['boxId']) && $_GET['boxId'] != 0) {
                                        ?>
                                        <div class="col-md-3 pull-right">
                                            <button type="button" id="updateRowRackShelf" class="btn btn-info btn-md">
                                                Update
                                            </button>
                                        </div>
                                        <?php
                                    }
                                }
                            } else {
                                if ($storageType == 'warehouse' || $storageType == 'allBox') {
                                    ?>
                                    <div class="col-md-3">
                                        Row:
                                        <strong>
                                            <?php echo $rowName; ?>
                                        </strong>
                                    </div>
                                    <div class="col-md-3">
                                        Rack:
                                        <strong>
                                            <?php echo $rackName; ?>
                                        </strong>
                                    </div>
                                    <div class="col-md-3">
                                        Shelf:
                                        <strong>
                                            <?php echo $shelfName; ?>
                                        </strong>
                                    </div>
                                    <?php
                                    if (isset($_GET['boxId']) && $_GET['boxId'] != 0) {
                                        ?>
                                        <div class="col-md-3 pull-right">
                                            <button type="button" id="updateRowRackShelf" class="btn btn-info btn-md">
                                                Update
                                            </button>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col-md-2 pull-right">
                        <span id="mainImage"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="table">
                <form id="tableUpdate">
                    <table class="table table-bordered text-center">
                        <?php
                        $element = '';
                        $element .= '<tr><th>#</th>';
                        foreach ($dataMainSizeId as $key => $value) {
                            ;
                            $element .= '<th class="text-center">' . $value . '</th>';
                        }
                        $element .= '</tr>';
                        if (count($dataOptSizeId) > 0) {
                            foreach ($dataOptSizeId as $key1 => $value1) {
                                $element .= '<tr>';
                                $element .= '<td class="text-left">' . $value1 . '</td>';
                                foreach ($dataMainSizeId as $key2 => $value2) {
                                    if (isset($dataQuantity[$key2][$key1]) && $dataQuantity[$key2][$key1] > 0) {
                                        $element .= '<td class="text-center"><span class="tool"><input type="text" class="inputQty click" id="updateInput_' . $key2 . '_' . $key1 . '" name="qty[]" value="' . $dataQuantity[$key2][$key1] . '"> <p class="tooltext">'.$dataToolTip[$key2][$key1].'</p></span>      ';
                                        $element .= '<input type="hidden" name="mainSizeId[]" value="' . $key2 . '" />';
                                        $element .= '<input type="hidden" name="optSizeId[]" value="' . $key1 . '" />';
                                        $element .= '<input type="hidden" name="is_change[]" id="update_' . $key2 . '_' . $key1 . '" value="0" /></td>';
                                    } else {
                                        $element .= '<td class="text-center"><input type="text" class="inputQty click" id="updateInput_' . $key2 . '_' . $key1 . '" name="qty[]" value="0" width="80%">';
                                        $element .= '<input type="hidden" name="mainSizeId[]" value="' . $key2 . '" />';
                                        $element .= '<input type="hidden" name="optSizeId[]" value="' . $key1 . '" />';
                                        $element .= '<input type="hidden" name="is_change[]" id="update_' . $key2 . '_' . $key1 . '" value="0" /></td>';
                                    }
                                }
                                $element .= '</tr>';
                            }
                        } else {
                            $element .= '<tr>';
                            $element .= '<td class="text-left">Qty</td>';
                            foreach ($dataMainSizeId as $key2 => $value2) {
                                if (isset($dataQuantity[$key2][0]) && $dataQuantity[$key2][0] > 0) {
                                    $element .= '<td class="text-center"><span class="tool"><input type="text" class="inputQty click" id="updateInput_' . $key2 . '_' . 0 . '" name="qty[]" value="' . $dataQuantity[$key2][0] . '"> <p class="tooltext">'.$dataToolTip[$key2][0].'</p></span>';
                                    $element .= '<input type="hidden" name="mainSizeId[]" value="' . $key2 . '" />';
                                    $element .= '<input type="hidden" name="optSizeId[]" value="' . 0 . '" />';
                                    $element .= '<input type="hidden" name="is_change[]" id="update_' . $key2 . '_' . 0 . '" value="0" /></td>';
                                } else {
                                    $element .= '<td class="text-center"><input type="text" class="inputQty click" id="updateInput_' . $key2 . '_' . 0 . '" name="qty[]" value="0" width="80%">';
                                    $element .= '<input type="hidden" name="mainSizeId[]" value="' . $key2 . '" />';
                                    $element .= '<input type="hidden" name="optSizeId[]" value="' . 0 . '" />';
                                    $element .= '<input type="hidden" name="is_change[]" id="update_' . $key2 . '_' . 0 . '" value="0" /></td>';
                                }
                            }
                            $element .= '</tr>';
                        }
                        echo $element;
                        ?>
                    </table>
                </form>
            </div>
        </div>
        <?php
        if (isset($_GET['boxId']) && $_GET['boxId'] != 0) {
            ?>
            <div class="panel-footer">
                <button class="center-block" id="UpdateInventoryButton">
                    <img src="<?php echo $mydirectory; ?>/images/updtInvbutton.jpg" alt="Update Inventory"/>
                </button>
            </div>
            <?php
        }
        ?>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="addInventory" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center"> Add Unit for <?php echo $dataStyle['styleNumber'] ?></h4>
            </div>
            <div class="modal-body">
                <div class="panel panel-default panel-warning">
                    <div class="panel-heading ">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-3 pull-left">
                                        Style: <strong><?php echo $dataInfo['styleNumber']; ?></strong>
                                    </div>
                                    <div class="col-md-3">
                                        Garment Type: <strong><?php echo $dataInfo['garmentName']; ?></strong>
                                    </div>
                                    <div class="col-md-3">
                                        Gender: <strong><?php echo $dataInfo['sex'] ?></strong>
                                    </div>
                                    <div class="col-md-3 pull-right">
                                        Client: <strong><?php echo $dataInfo['client']; ?></strong>
                                    </div>
                                </div>
                                <div style="margin-top:20px;"></div>
                                <div class="row">
                                    <div class="col-md-6 pull-left">
                                        <div class="col-md-2" style="font-size: 15px;">
                                            Color:
                                        </div>
                                        <div class="col-md-10">
                                            <select class="form-control" name="color" id="newColor">
                                                <option value="" data-image="0">--------SELECT--------</option>
                                                <?php
                                                foreach ($dataColor as $color) {
                                                    echo '<option value="' . $color['colorId'] . '" data-image="' . trim($color['image']) . '">' . $color['name'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pull-right">
                                        <div class="col-md-3" style="font-size: 15px;">
                                            Location:
                                        </div>
                                        <div class="col-md-9">
                                            <select class="form-control" name="location" id="newLocation">
                                                <option value="">--------SELECT--------</option>
                                                <?php
                                                foreach ($allLocation as $arrallValue) {
                                                    echo '<option value="' . $arrallValue['storageId'] . '" data-type="' . $arrallValue['type'] . '">' . $arrallValue['locationIdentifier'] . '_' . $arrallValue['storage'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-top:20px;"></div>
                                <div class="row" id="boxDiv" style="display: none;">
                                    <div class="col-md-6 center-block">
                                        <div class="col-md-2">
                                            Box:
                                        </div>
                                        <div class="col-md-10">
                                            <input type="text" id="newBox" class="form-control"/>
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-top:20px;"></div>
                                <div class="row" id="warehouseDiv" style="display: none;">
                                    <div class="col-md-4">
                                        <div class="col-md-4">
                                            Row:
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row">
                                                <input type="text" id="newRow" class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="col-md-4">
                                            Rack:
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row">
                                                <input type="text" id="newRack" class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="col-md-4">
                                            Shelf:
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row">
                                                <input type="text" id="newShelf" class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <span id="imageSpan"></span>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <form id="addNewInventory">
                                <table class="table table-bordered text-center">
                                    <?php
                                    $count = 0;
                                    $element = '';
                                    $element .= '<tr><th>#</th>';
                                    foreach ($dataMainSizeId as $key => $value) {
                                        ;
                                        $element .= '<th class="text-center">' . $value . '</th>';
                                    }
                                    $element .= '</tr>';
                                    if (count($dataOptSizeId) > 0) {
                                        foreach ($dataOptSizeId as $key1 => $value1) {
                                            $element .= '<tr>';
                                            $element .= '<td class="text-left">' . $value1 . '</td>';
                                            foreach ($dataMainSizeId as $key2 => $value2) {
                                                ;
                                                $element .= '<td><input type="text" class="inputQty clickNew" name="qty[]" id="input_' . $key2 . '_' . $key1 . '" value="0"/>';
                                                $element .= '<input type="hidden" name="mainSizeId[]" value="' . $key2 . '" />';
                                                $element .= '<input type="hidden" name="optSizeId[]" value="' . $key1 . '" />';
                                                $element .= '<input type="hidden" name="is_change_new[]" id="new_' . $key2 . '_' . $key1 . '" value="0" /></td>';
                                                $count++;
                                            }
                                            $element .= '</tr>';
                                        }
                                    } else {
                                        $element .= '<tr>';
                                        $element .= '<td class="text-left">Qty</td>';
                                        foreach ($dataMainSizeId as $key2 => $value2) {
                                            $element .= '<td><input type="text" class="inputQty clickNew" name="qty[]" id="input_' . $key2 . '_' . 0 . '" value="0"/>';
                                            $element .= '<input type="hidden" name="mainSizeId[]" value="' . $key2 . '" />';
                                            $element .= '<input type="hidden" name="optSizeId[]" value="0" />';
                                            $element .= '<input type="hidden" name="is_change_new[]" id="new_' . $key2 . '_0" value="0" /></td>';
                                            $count++;
                                        }
                                        $element .= '</tr>';
                                    }
                                    echo $element;
                                    ?>
                                </table>
                            </form>
                        </div>
                        <span id="newError"></span>
                    </div>
                    <div class="panel-footer">
                        <button class=" btn btn-md btn-success form-control center-block addNewInventoryButton">
                            Add Unit
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="locationUpdateModal" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Update Location</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Select A location</label>
                            </div>
                            <div class="col-md-5">
                                <select name="location" id="location" class="form-control">
                                    <option value="">----Select A Location----</option>
                                    <?php
                                    foreach ($locationChange as $locationValue) {
                                        ?>
                                        <option
                                                value="<?php echo $locationValue['locationId']; ?>"><?php echo $locationValue['name']; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div style="padding-bottom: 15px"></div>
                        <div class="row">
                            <div id="loader" style="line-height: 115px; text-align: center; display: none;">
                                <img alt="activity indicator" src="../../../images/ajax-loader.gif">
                            </div>
                        </div>
                        <div style="padding-bottom: 15px"></div>
                        <div class="row" id="secondSelect" style="display: none;">
                            <div class="col-md-4">
                                <label>Select a Storage</label>
                            </div>
                            <div class="col-md-5">
                                <select name="storage" id="storage" class="form-control">
                                    <option value="">----Select A Storage----</option>
                                </select>
                            </div>
                        </div>
                        <div style="padding-bottom: 15px"></div>
                        <div class="row">
                            <span class="col-md-10" id="error" style="display: none;color: red; font-size: medium;">This Location is Empty Please Add a Storage</span>
                        </div>
                        <div style="padding-bottom: 15px"></div>
                        <div class="row">
                            <button id="changeLocationSubmit" class="btn btn-md btn-success center-block">
                                Change
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php require('../../../trailer.php'); ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.6/sweetalert2.min.js"></script>
<!--Add new Inventory Scripts -->
<script>
    $(document).ready(function () {
        $('#newLocation').change(function () {
            $('#newError').hide();
            var type = $(this).find(':selected').data('type');
            if (type == 'warehouse') {
                $('#boxDiv').show();
                $('#warehouseDiv').show();
            } else if (type == 'container' || type == 'conveyor') {
                $('#boxDiv').show();
                $('#warehouseDiv').hide();
            } else {
                $('#boxDiv').hide();
                $('#warehouseDiv').hide();
            }
        });
        $('#newColor').change(function () {
            $('#newError').hide();
            var image = $(this).find(':selected').data('image');
            if (image != '0') {
                $('#imageSpan').html('<img src="<?php echo $upload_dir_image ?>' + image + '" alt="image" width="100" height="100" border="1">');
                $('#imageSpan').show();
            } else {
                $('#imageSpan').hide();
            }

        });
        $('#newBox').keyup(function () {
            $('#newError').hide();
        });
        $('.clickNew').keyup(function () {
            var id = this.id;
            var change = id.slice(5);
            $('#new' + change).val(1);
        });
        /*
         *Submit Add Inventory Form
         *
         *For adding New Box
         */
        $('.addNewInventoryButton').click(function () {
            $('#newError').hide();
            var datastring = $('#addNewInventory').serialize();
            var color = $('#newColor').val();
            if (color == '') {
                $('#newError').html('<h3 style="color: red">Please Select a Color</h3>');
                $('#newError').show();
                return false;
            }
            datastring += '&colorId=' + color;
            var location = $('#newLocation').val();
            if (location == '') {
                $('#newError').html('<h3 style="color: red">Please Select a Location</h3>');
                $('#newError').show();
                return false;
            }
            datastring += '&locationId=' + location;
            var box = $('#newBox').val();
            if (box == '') {
                $('#newError').html('<h3 style="color: red">Please Enter box number</h3>');
                $('#newError').show();
                return false;
            }
            var regx = /^[A-Za-z0-9]+$/;
            if (!regx.test(box)) {
                $("#newError").html("<h3 style='color: red;'>Alphanumeric only allowed for Box number !</h3>");
                $('#newError').show();
                return false;
            }
            datastring += '&box=' + box;
            var type = $('#newLocation').find(':selected').data('type');
            datastring += '&type=' + type;
            if (type == 'warehouse') {
                var row = $('#newRow').val();
                datastring += '&row=' + row;
                var rack = $('#newRack').val();
                datastring += '&rack=' + rack;
                var shelf = $('#newShelf').val();
                datastring += '&shelf=' + shelf;
            }
            var style = "<?php echo $_GET['styleId']; ?>";
            datastring += '&styleId=' + style;
            $.ajax({
                url: "addNewInventory.php",
                type: "POST",
                data: datastring,
                success: function (data) {
                    var dataPase = $.parseJSON(data);
                    if (dataPase.success) {
                        var dataString2 = 'styleId=' + dataPase.data.styleId + '&colorId=' + dataPase.data.colorId + '&boxId=' + dataPase.data.id;
                        swal({
                            title: "Added!",
                            text: dataPase.message,
                            type: "success"
                        }).then( function(){
                            window.location.replace('inventoryViewEdit.php?' + dataString2);
                        },function (dismiss) {
                            window.location.replace('inventoryViewEdit.php?' + dataString2);
                        });
                    } else {
                        $('#newError').html('<h3 style="color: red;">' + dataPase.message + '</h3>');
                        $('#newError').show();
                    }
                }
            });
        });
    });
</script>
<!-- Change Box Event -->
<script>
    $(document).ready(function () {
        var image = $('#color').find(':selected').data('image');
        $('#mainImage').html('<img src="<?php echo $upload_dir_image ?>' + image + '" alt="image" width="150" height="170" border="2">');
        $('#colorName').html($('#color').find(':selected').text());
        setTimeout(function () {
            $('#errorMessage').hide();
        }, 15000);
        <?php if(!isset($_REQUEST['boxId']) || $_REQUEST['boxId'] == 0): ?>
        $('.tool').bind({
            mouseenter: function(){
                $(this).children('.tooltext').show();
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
    });
    function changeUrl() {
        var color = $('#color').val();
        var box = $('#allBoxSelect').val();
        var style = "<?php echo $_GET['styleId']; ?>";
        var dataString = 'styleId=' + style + '&colorId=' + color + '&boxId=' + box;
        $.ajax
        ({
            type: "POST",
            url: "confirmBoxColor.php",
            data: dataString,
            success: function (data) {
                var dataParse = $.parseJSON(data);
                if (dataParse.success) {
                    $(location).attr('href', 'inventoryViewEdit.php?' + dataString);
                } else {
                    $('#errorMessage').html('<h2 style="color: red;">' + dataParse.message + '</h2>');
                }
            }
        });
    };
    $('#location').change(function () {
        var location = $(this).val();
        jQuery.ajax({
            type: "POST",
            url: "bulkLocationIdentifierList.php",
            data: {
                id: location,
            },
            beforeSend: function(){ jQuery("#loader").show(); },
            complete: function(){ jQuery("#loader").hide(); },
            success: function(response){
                if (response == 0){
                    jQuery("#storage").html('');
                    $('#changeLocationSubmit').attr('disabled','disabled');
                    $('#error').show();
                    jQuery("#secondSelect").hide();
                } else {
                    $('#error').hide();
                    $('#changeLocationSubmit').removeAttr('disabled');
                    jQuery("#storage").html(response);
                    jQuery("#secondSelect").show();
                }
            }
        });
    });
    $('#changeLocationSubmit').on('click',function () {
         var storage = $('#storage').val();
         var boxId = "<?php echo $_GET['boxId']; ?>";
         $.ajax({
             url: "changeLocation.php",
             type: "POST",
             data: {
                 storage: storage,
                 boxId: boxId
             },
             success:function (data) {
                 if(data == 1){
                     swal({
                         title: "Success!",
                         text: "Location Changed Successfully",
                         type: "success"
                     }).then( function(){
                        window.location.reload();
                     },function (dismiss) {
                        window.location.reload();
                     });
                 } else {
                     swal({
                         title: "Error!",
                         text: "Location not Changed ! Please Try After Some Time....",
                         type: "error"
                     });
                 }
             }
         });
    });
    $('#allBoxSelect').change(function () {
        changeUrl();
    });
    $('#color').change(function () {
        var color = $('#color').val();
        var style = "<?php echo $_GET['styleId']; ?>";
        var dataString = 'styleId=' + style + '&colorId=' + color;
        $(location).attr('href', 'inventoryViewEdit.php?' + dataString);
    });
    $('#mainInventory').on('click', function () {
        var styleId = "<?php echo $_GET['styleId'];?>";
        var colorId = $('#color').val();
        window.location.replace('inventoryViewEdit.php?styleId=' + styleId + '&colorId=' + colorId);
    });
    $('#updateRowRackShelf').on('click', function () {
        var row = $('#updateRow').val();
        var rack = $('#updateRack').val();
        var shelf = $('#updateShelf').val();
        var boxId = "<?php echo $_GET['boxId'] ?>";
        if (boxId == '' || boxId == undefined) {
            $('#errorMessage').html('<h2 style="color: red;">Error !! please Select a Box..</h2>');
            return false;
        }
        $.ajax({
            type: "POST",
            url: "updateRowRackShelf.php",
            data: {
                row: row,
                rack: rack,
                shelf: shelf,
                boxId: boxId
            },
            success: function (data) {
                var dataParse = $.parseJSON(data);
                if (dataParse.success) {
                    $('#updateRow').val(row);
                    $('#updateRack').val(rack);
                    $('#updateShelf').val(shelf);
                    $('#errorMessage').html('<h2 style="color: blue;">' + dataParse.message + '</h2>');
                } else {
                    $('#errorMessage').html('<h2 style="color: red;">' + dataParse.message + '</h2>');
                }
            }
        });
    });
    $('#deleteBox').on('click', function () {
        swal({
            title: 'Are you sure?',
            text: 'You will not be able to recover this Box!',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, keep it'
        }).then(function() {
            var boxId = "<?php echo $_GET['boxId'] ?>";
            if (boxId == '' || boxId == undefined) {
                $('#errorMessage').html('<h2 style="color: red;">Error !! please Select a Box..</h2>');
                return false;
            }
            var color = $('#color').val();
            var style = "<?php echo $_GET['styleId']; ?>";
            var dataString = 'styleId=' + style + '&colorId=' + color + '&boxId=' + boxId;
            $.ajax({
                type: "POST",
                url: "deleteUnit.php",
                data: dataString,
                success: function (data) {
                    var dataParse = $.parseJSON(data);
                    if (dataParse.success) {
                        swal({
                            title: "Deleted!",
                            text: dataParse.message,
                            type: "success"
                        }).then( function(){
                            $(location).attr('href', 'inventoryViewEdit.php?styleId=' + style + '&colorId=' + color);
                        },function (dismiss) {
                            $(location).attr('href', 'inventoryViewEdit.php?styleId=' + style + '&colorId=' + color);
                        });
                    } else {
                        swal("Error", dataParse.message, "error");
                    }
                }
            });
        }, function(dismiss) {
            console.log(dismiss);
        });
    });
    $('.click').keyup(function () {
        var id = this.id;
        var change = id.slice(11);
        $('#update' + change).val(1);
    });
    $('#UpdateInventoryButton').on('click', function () {
        var dataString = $('#tableUpdate').serialize();
        var style = "<?php echo $_GET['styleId']; ?>";
        dataString += '&styleId=' + style;
        var color = $('#color').val();
        if (color == '') {
            $('#errorMessage').html('<h3 style="color: red">Please Select a Color</h3>');
            return false;
        }
        var boxId = "<?php echo $_GET['boxId'] ?>";
        if (boxId == '' || boxId == undefined) {
            $('#errorMessage').html('<h2 style="color: red;">Error !! please Select a Box..</h2>');
            return false;
        }
        dataString += '&boxId=' + boxId;
        dataString += '&colorId=' + color;
        $.ajax({
            type: "POST",
            url: "updateInventory.php",
            data: dataString,
            success: function (data) {
                var dataParse = $.parseJSON(data);
                if (dataParse.success) {
                    swal({
                        title: "Updated!",
                        text: dataParse.message,
                        type: "success"
                    }).then( function(){
                        window.location.reload();
                    },function (dismiss) {
                        window.location.reload();
                    });
                } else {
                    swal("Error", dataParse.message, "error");
                }
            }
        });
    });
    $('#print').on('click', function () {
        var boxId = $(this).data('box');
        var styleId = "<?php echo $_GET['styleId']; ?>";
        var color = $('#color').val();
        window.open(
            "print.php" + '?styleId=' + styleId + '&colorId=' + color + '&boxId=' + boxId,
            '_blank' // <- This is what makes it open in a new window.
        );
        redirectWindow.location;
    });
    $('#merge').on('click', function () {
        $('#mergeDiv').show();
        $('#merge').hide();
    });
    $('#mergedLocation').change(function () {
        var targetBox = $(this).val();
        if (targetBox == '0') {
            return false;
        }
        var currentBox = "<?php echo $_GET['boxId']; ?>";
        $.ajax({
            url: "mergeBox.php",
            type: "POST",
            data: {
                currentBox: currentBox,
                targetBox: targetBox
            },
            success: function (data) {
                var dataParse = $.parseJSON(data);
                if (dataParse.success) {
                    var color = dataParse.info.colorId;
                    var style = dataParse.info.styleId;
                    var dataString = 'styleId=' + style + '&colorId=' + color + '&boxId=' + targetBox;
                    swal({
                        title: "Merged!",
                        text: dataParse.message,
                        type: "success"
                    }).then( function(){
                        $(location).attr('href', 'inventoryViewEdit.php?' + dataString);
                    },function (dismiss) {
                        $(location).attr('href', 'inventoryViewEdit.php?' + dataString);
                    });
                } else {
                    swal({
                        title: "Error!",
                        text: dataParse.message,
                        type: "error"
                    });
                }
            }
        });
    });
</script>