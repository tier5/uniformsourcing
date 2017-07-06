<?php
require('Application.php');
require('../../../header.php');
if(isset($_GET['styleId']) && $_GET['styleId'] != '' && $_GET['styleId'] != 0){
    //Select all data for Style
    $sql = '';
    $sql = "SELECT * FROM \"tbl_invStyle\" WHERE \"styleId\" = '".$_GET['styleId']."'";
    if (!($resultStyle = pg_query($connection,$sql))){
        echo '<h1> Failed style Query: </h1><h2>'. pg_last_error($connection).'</h2>';
        exit;
    }
    $dataStyle = pg_fetch_array($resultStyle);
    pg_free_result($resultStyle);
    //GET COLOR DATA
    $query = '';
    $query = 'Select * from "tbl_invColor" where "styleId"=' . $dataStyle['styleId'];
    if (!($resultColor = pg_query($connection, $query))) {
        print("<h1>Failed Color Query: </h1><h2>" . pg_last_error($connection)).'</h2>';
        exit;
    }
    while ($row = pg_fetch_array($resultColor)) {
        $dataColor[] = $row;
    }
    pg_free_result($resultColor);
    //Fetch All Information for the style
    $sql = '';
    $sql = 'SELECT style.*,garment.*,client.* FROM "tbl_invStyle" style '.
        ' LEFT JOIN "tbl_garment" garment on garment."garmentID" = style."garmentId" '.
        ' LEFT JOIN "clientDB" client on client."ID" = style."clientId" '.
        ' WHERE style."styleId"='.$_GET['styleId'];
    if (!($resultInfo = pg_query($connection,$sql))){
        echo '<h1> Failed Info Query: </h1><h2>'. pg_last_error($connection).'</h2>';
        exit;
    }
    $dataInfo = pg_fetch_array($resultInfo);
    pg_free_result($resultInfo);
    //Fetch Size Scale Data
    if($dataStyle['scaleNameId'] != ''){
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
        foreach ($dataMainSize as $key => $val){
            if (isset($val['mainSizeId'])){
                $dataMainSizeId[(int)$val['mainSizeId']] = $val['scaleSize'];
            }
        }
    } else {
        echo '<h1>No size available</h1>';
        die();
    }
    //Fetch All Location
    $sql = '';
    $sql = 'SELECT details.*,location.* FROM "locationDetails" details '.
        'INNER JOIN "tbl_invLocation" location on location."locationId" = CAST(details."locationId" as INT)'.
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
    if(count($allStorage) > 0){
        foreach ($allStorage as $key=>$value){
            $allLocation[$key]['storageId'] = $value['id'];
            $allLocation[$key]['locationId'] = $value['locationId'];
            $allLocation[$key]['locationIdentifier'] = $value['identifier'];
            if($value['warehouse'] != ''){
                $allLocation[$key]['storage'] = $value['warehouse'];
                $allLocation[$key]['type'] = 'warehouse';
            } elseif ($value['container'] != ''){
                $allLocation[$key]['storage'] = $value['container'];
                $allLocation[$key]['type'] = 'container';
            } else {
                $allLocation[$key]['storage'] = $value['conveyor'];
                $allLocation[$key]['type'] = 'conveyor';
            }
        }
    }
} else {
    echo '<h1>Please select a Style to view the Report</h1>';
    exit();
}
?>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<div class="container">
    <div class="page-header">
        <h1 class="text-center">Report View / Edit (<small> <?php echo $dataStyle['styleNumber'] ?></small>)</h1>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-4 pull-left">
                <div class="form-group">
                    <label for="color" class="col-md-2 control-label"> Color:    </label>
                    <div class="col-md-10">
                        <select class="form-control" name="color" id="color">
                            <?php
                            foreach ($dataColor as $color){
                                echo '<option value="'.$color['colorId'].'">'.$color['name'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-2 pull-right">
                <button class="btn btn-lg btn-success" data-toggle="modal" data-target="#addInventory">Add Inventory</button>
            </div>
        </div>
    </div>
    <div style="margin-top:15px"></div>
    <div class="panel panel-default panel-success">
        <div class="panel-heading ">
            <div class="panel-heading ">
                <div class="row">
                    <div class="col-md-3 pull-left">
                        Style:  <strong><?php echo $dataInfo['styleNumber']; ?></strong>
                    </div>
                    <div class="col-md-3">
                        Garment Type:  <strong><?php echo $dataInfo['garmentName']; ?></strong>
                    </div>
                    <div class="col-md-3">
                        Gender: <strong><?php echo $dataInfo['sex'] ?></strong>
                    </div>
                    <div class="col-md-3 pull-right">
                        Client: <strong><?php echo $dataInfo['client']; ?></strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <?php
                    $element = '';
                    $element .= '<tr><th>#</th>';
                    foreach ($dataMainSizeId as $key => $value){;
                        $element .= '<th class="text-center">'.$value.'</th>';
                    }
                    $element .= '</tr>';
                    if(count($dataOptSizeId) > 0){
                        foreach ($dataOptSizeId as $key1=>$value1){
                            $element .= '<tr>';
                            $element .= '<td class="text-left">'.$value1.'</td>';
                            foreach ($dataMainSizeId as $key2 => $value2){;
                                if (isset($data_set[$key2][$key1]) && $data_set[$key2][$key1] >0) {
                                    $element .= '<td class="text-center">' . $data_set[$key2][$key1] . '</td>';
                                } else {
                                    $element .= '<td>0</td>';
                                }
                            }
                            $element .= '</tr>';
                        }
                    } else {
                        $element .= '<tr>';
                        $element .= '<td class="text-left">Qty</td>';
                        foreach ($dataMainSizeId as $key2 => $value2){
                            if (isset($data_set[$key2][0]) && $data_set[$key2][0] > 0) {
                                $element .= '<td class="text-center">' . $data_set[$key2][0] . '</td>';
                            } else {
                                $element .= '<td>0</td>';
                            }
                        }
                        $element .= '</tr>';
                    }
                    echo $element;
                    ?>
                </table>
            </div>
        </div>
        <div class="panel-footer">
            <button class="center-block">
                <img src="<?php echo $mydirectory; ?>/images/updtInvbutton.jpg" alt="Update Inventory"/>
            </button>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="addInventory" role="dialog"  data-backdrop="static" data-keyboard="false">
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
                            <div class="col-md-3 pull-left">
                                Style:  <strong><?php echo $dataInfo['styleNumber']; ?></strong>
                            </div>
                            <div class="col-md-3">
                                Garment Type:  <strong><?php echo $dataInfo['garmentName']; ?></strong>
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
                                        <option value="">--------SELECT--------</option>
                                        <?php
                                        foreach ($dataColor as $color){
                                            echo '<option value="'.$color['colorId'].'">'.$color['name'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 pull-right">
                                <div class="col-md-2" style="font-size: 15px;">
                                    Location:
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control" name="location" id="newLocation">
                                        <option value="">--------SELECT--------</option>
                                        <?php
                                        foreach ($allLocation as $arrallValue) {
                                            echo '<option value="'.$arrallValue['storageId'].'" data-type="'.$arrallValue['type'].'">'.$arrallValue['locationIdentifier'].'_'.$arrallValue['storage'].'</option>';
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
                                    <input type="text" class="form-control"/>
                                </div>
                            </div>
                        </div>
                        <div style="margin-top:20px;"></div>
                        <div class="row" id="warehouseDiv" style="display: none;">
                            <div class="col-md-4">
                                <div class="col-md-2">
                                    Row:
                                </div>
                                <div class="col-md-10">
                                    <input type="text" class="form-control"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-md-2">
                                    Rack:
                                </div>
                                <div class="col-md-10">
                                    <input type="text" class="form-control"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-md-2">
                                    Shelf:
                                </div>
                                <div class="col-md-10">
                                    <input type="text" class="form-control"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <?php
                                $count = 0;
                                $element = '';
                                $element .= '<tr><th>#</th>';
                                foreach ($dataMainSizeId as $key => $value){;
                                    $element .= '<th class="text-center">'.$value.'</th>';
                                }
                                $element .= '</tr>';
                                if(count($dataOptSizeId) > 0){
                                    foreach ($dataOptSizeId as $key1=>$value1){
                                        $element .= '<tr>';
                                        $element .= '<td class="text-left">'.$value1.'</td>';
                                        foreach ($dataMainSizeId as $key2 => $value2){;
                                            $element .= '<td><input type="text" name="qty[]" id="clickNew" />';
                                            $element .= '<input type="hidden" name="mainSizeId[]" value="'.$key2.'" />';
                                            $element .= '<input type="hidden" name="optSizeId[]" value="'.$key1.'" />';
                                            if($count == 0){
                                                $element .= '<input type="hidden" name="is_change_new[]" id="new_'.$key2.'_'.$key1.'" value="1" /></td>';
                                            } else {
                                                $element .= '<input type="hidden" name="is_change_new[]" id="new_'.$key2.'_'.$key1.'" value="0" /></td>';
                                            }
                                            $count++;
                                        }
                                        $element .= '</tr>';
                                    }
                                } else {
                                    $element .= '<tr>';
                                    $element .= '<td class="text-left">Qty</td>';
                                    foreach ($dataMainSizeId as $key2 => $value2){
                                        if (isset($data_set[$key2][0]) && $data_set[$key2][0] > 0) {
                                            $element .= '<td class="text-center">' . $data_set[$key2][0] . '</td>';
                                        } else {
                                            $element .= '<td>0</td>';
                                        }
                                    }
                                    $element .= '</tr>';
                                }
                                echo $element;
                                ?>
                            </table>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <button class=" btn btn-md btn-success form-control center-block">
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

<?php require('../../../trailer.php'); ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script>
    $(document).ready(function () {
        $('#newLocation').change(function () {
            var type = $(this).find(':selected').data('type');
            if(type == 'warehouse'){
                $('#boxDiv').show();
                $('#warehouseDiv').show();
            } else if(type == 'container' || type == 'conveyor') {
                $('#boxDiv').show();
                $('#warehouseDiv').hide();
            } else {
                $('#boxDiv').hide();
                $('#warehouseDiv').hide();
            }
        });
    });
</script>