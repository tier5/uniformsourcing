<?php
require('Application.php');
require('../../../header.php');
$query1='Select Distinct "scaleName","scaleId" from "tbl_invScaleName" where "isActive"=1';
if(!($result_cnt=pg_query($connection,$query1))){
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
while($row_cnt = pg_fetch_array($result_cnt)){
    $data_scaleN[]=$row_cnt;
}
pg_free_result($result_cnt);
$query2='Select "garmentID","garmentName" from "tbl_garment" where status=1';
if(!($result_cnt1=pg_query($connection,$query2))){
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
while($row_cnt = pg_fetch_array($result_cnt1)){
    $data_garment[]=$row_cnt;
}
pg_free_result($result_cnt1);
$query3='Select "fabricID","fabName" from "tbl_fabrics" where status=1';
if(!($result_cnt2=pg_query($connection,$query3))){
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
while($row_cnt = pg_fetch_array($result_cnt2)){
    $data_fab[]=$row_cnt;
}
pg_free_result($result_cnt2);
$query5=("SELECT \"ID\", \"clientID\", \"client\", \"active\" ".
    "FROM \"clientDB\" ".
    "WHERE \"active\" = 'yes' ".
    "ORDER BY \"client\" ASC");
if(!($result=pg_query($connection,$query5))){
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
while($row1 = pg_fetch_array($result)){
    $data_client[]=$row1;
}
pg_free_result($result);
?>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<div class="container">
    <div class="page-header">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-2 pull-left">
                    <button class="btn btn-success btn-xs" onclick="javascript:location.href='../inventory.php';" type="button">
                        Back
                    </button>
                </div>
                <div class="col-md-10">
                    <h3 class="text-center">Add Style</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-8">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="styleNumber">Style Number:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="styleNumber" name="styleNumber" placeholder="Enter style Number">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="barcode">Barcode:</label>
                            <div class="col-sm-8">
                                <input type="file" class="form-control" id="barcode" name="barcode" placeholder="Enter Barcode">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="sizeScale">Size Scale:</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="sizeScale" name="sizeScale">
                                    <option value="">-----Select Size Scale-----</option>
                                    <?php
                                    foreach ($data_scaleN as $size){
                                        echo '<option value="'.$size['scaleNameId'].'">'.$size['scaleName'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="garment">Garment:</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="garment" name="garment">
                                    <option value="">-----Select Garment-----</option>
                                    <?php
                                    foreach ($data_garment as $garment){
                                        echo '<option value="'.$garment['garmentID'].'">'.$garment['garmentName'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4">Color:</label>
                            <div class="col-sm-8">
                               <button type="button" class="form-control btn btn-default" data-toggle="modal" data-target="#addColor">Add Color</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="fabric">Fabric:</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="fabric" name="fabric">
                                    <option value="">-----Select Fabric-----</option>
                                    <?php
                                    foreach ($data_fab as $feb){
                                        echo '<option value="'.$feb['fabricID'].'">'.$feb['fabName'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="sex">Gender:</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="sex" name="sex">
                                    <option value="">-----Select Gender-----</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="unisex">Unisex</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="client">Client:</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="client" name="client">
                                    <option value="">-----Select Client-----</option>
                                    <?php
                                        foreach ($data_client as $client){
                                            echo '<option value="'.$client['ID'].'">'.$client['client'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="button" class="btn btn-lg btn-success pull-right">Submit</button>
                                <button type="button" class="btn btn-lg btn-warning pull-left">cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="addColor" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Color</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="addColorForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="colorName">Name:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="colorName" name="colorName" placeholder="Enter Color Name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="colorImage">Color Image:</label>
                        <div class="col-sm-8">
                            <input type="file" class="form-control" id="colorImage" name="colorImage" placeholder="Enter Image for color">
                        </div>
                    </div>
                    <div class="form-group" id="error" style="color: red;display: none">
                        <label class="control-label col-sm-4" for="error_text">Error:</label>
                        <div class="col-sm-8">
                            <h4 id="error_text"></h4>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="button" id="addColorButton" class="btn btn-lg btn-success pull-right">Submit</button>
                        </div>
                    </div>
                </form>
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
<script>
    $('#addColorButton').on('click',function () {
        var name = $('#colorName').val();
        if(name == ''){
            $('#error_text').text('Please provide a Color name');
            $('#error').show();
            return false;
        }
        var fileName = $('#colorImage').prop('files');
        if(fileName == ''){
            $('#error_text').text('Please provide a Color Image');
            $('#error').show();
            return false;
        }
        $.ajax({
            type: "POST",
            url: "uploadColor.php",
            contentType: false,
            processData: false,
            data: {
                name: name,
                file: fileName
            },
            success: function () {
                alert("Data Uploaded: ");
            }
        });
    });
</script>