<?php
    // Load Main Application with Database Connection
    require('Application.php');
    // Load Header File
    require('../../../header.php');
    //Get All the Information for The Selected Style With StyleId Parameter from URL
    $sql = '';
    $sql = 'SELECT * FROM "tbl_invStyle" WHERE "styleId"='.$_GET['styleId'];
    if(!($result_cnt=pg_query($connection,$sql))){
        print("Failed query1: " . pg_last_error($connection));
        exit;
    }
    $data_style=pg_fetch_array($result_cnt);
    pg_free_result($result_cnt);
    // Get All Color Data for a style
    $sql = '';
    $sql = 'SELECT * FROM "tbl_invColor" WHERE "styleId"='.$_GET['styleId'];
    if(!($result_cnt=pg_query($connection,$sql))){
        print("Failed query1: " . pg_last_error($connection));
        exit;
    }
    while($row_cnt = pg_fetch_array($result_cnt)){
        $data_color[]=$row_cnt;
    }
    pg_free_result($result_cnt);
    // Get All Scale Information from table tbl_invScaleName
    $query1='Select Distinct "scaleName","scaleId" from "tbl_invScaleName" where "isActive"=1';
    if(!($result_cnt=pg_query($connection,$query1))){
        print("Failed query1: " . pg_last_error($connection));
        exit;
    }
    while($row_cnt = pg_fetch_array($result_cnt)){
        $data_scaleN[]=$row_cnt;
    }
    pg_free_result($result_cnt);
    //Get All Garment Types form table tbl_garment
    $query2='Select "garmentID","garmentName" from "tbl_garment" where status=1';
    if(!($result_cnt1=pg_query($connection,$query2))){
        print("Failed query1: " . pg_last_error($connection));
        exit;
    }
    while($row_cnt = pg_fetch_array($result_cnt1)){
        $data_garment[]=$row_cnt;
    }
    pg_free_result($result_cnt1);
    // Fetch all Fabric type form table tbl_fabrics
    $query3='Select "fabricID","fabName" from "tbl_fabrics" where status=1';
    if(!($result_cnt2=pg_query($connection,$query3))){
        print("Failed query1: " . pg_last_error($connection));
        exit;
    }
    while($row_cnt = pg_fetch_array($result_cnt2)){
        $data_fab[]=$row_cnt;
    }
    pg_free_result($result_cnt2);
    // Fetch All client information form table clientDB
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
<!--load Bootstrap -->
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<!-- Main Page Container -->
<div class="container">
    <div class="page-header">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-1 pull-left">
                    <button class="btn btn-success btn-xs" onclick="javascript:location.href='../reports.php';" type="button">
                        <i class="glyphicon glyphicon-arrow-left"></i> Back
                    </button>
                </div>
                <div class="col-md-10">
                    <h3 class="text-center">Edit Style</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-10">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="styleNumber">Style Number:</label>
                            <div class="col-sm-8">
                                <input type="text" value="<?php echo $data_style['styleNumber']; ?>" onkeyup="hideMainError()" class="form-control" id="styleNumber" name="styleNumber" placeholder="Enter style Number">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="barcode">Barcode:</label>
                            <div class="col-sm-8">
                                <input onchange="readURL(this)" type="file" class="form-control" id="barcode" name="barcode" placeholder="Enter Barcode">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="sizeScale">Size Scale:</label>
                            <div class="col-sm-8">
                                <select onchange="hideMainError()" class="form-control" id="sizeScale" name="sizeScale">
                                    <option value="">-----Select Size Scale-----</option>
                                    <?php
                                        foreach ($data_scaleN as $scale) {
                                          echo '<option value="'.$scale['scaleId'].'"';
                                          if($scale['scaleId'] == $data_style['scaleNameId'])
                                            echo 'selected=selected ';
                                          echo '>'.$scale['scaleName'].'</option>';
                                        }
                                     ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="garment">Garment:</label>
                            <div class="col-sm-8">
                                <select onchange="hideMainError()" class="form-control" id="garment" name="garment">
                                    <option value="">-----Select Garment-----</option>
                                    <?php
                                        foreach ($data_garment as $garment) {
                                          echo '<option value="'.$garment['garmentID'].'"';
                                          if($garment['garmentID'] == $data_style['garmentId'])
                                            echo 'selected=selected ';
                                          echo '>'.$garment['garmentName'].'</option>';
                                        }
                                     ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label onchange="hideMainError()" class="control-label col-sm-4">Color:</label>
                            <div class="col-sm-8">
                                <button type="button" onclick="hideMainError()" class="form-control btn btn-default" data-toggle="modal" data-target="#addColor">Add Color</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="fabric">Fabric:</label>
                            <div class="col-sm-8">
                                <select onchange="hideMainError()" class="form-control" id="fabric" name="fabric">
                                    <option value="">-----Select Fabric-----</option>
                                    <?php
                                        foreach ($data_fab as $fabric) {
                                          echo '<option value="'.$fabric['fabricID'].'"';
                                          if($fabric['fabricID'] == $data_style['fabricId'])
                                            echo 'selected=selected ';
                                          echo '>'.$fabric['fabName'].'</option>';
                                        }
                                     ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="sex">Gender:</label>
                            <div class="col-sm-8">
                                <select onchange="hideMainError()" class="form-control" id="sex" name="sex">
                                    <option value="">-----Select Gender-----</option>
                                    <option <?php echo ($data_style['sex']=='male')?'selected="selected"':'' ?> value="male">Male</option>
                                    <option <?php echo ($data_style['sex']=='female')?'selected="selected"':'' ?> value="female">Female</option>
                                    <option <?php echo ($data_style['sex']=='unisex')?'selected="selected"':'' ?> value="unisex">Unisex</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="client">Client:</label>
                            <div class="col-sm-8">
                                <select onchange="hideMainError()" class="form-control" id="client" name="client">
                                    <option value="">-----Select Client-----</option>
                                    <?php
                                        foreach ($data_client as $client) {
                                          echo '<option value="'.$client['ID'].'"';
                                          if($client['ID'] == $data_style['clientId'])
                                            echo 'selected=selected ';
                                          echo '>'.$client['client'].'</option>';
                                        }
                                     ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="notes">Notes:</label>
                            <div class="col-sm-8">
                                <textarea onkeyup="hideMainError()" rows="3" class="form-control" id="notes" name="notes">
                                  <?php echo $data_style['notes']; ?>
                                </textarea>
                            </div>
                        </div>
                        <div class="form-group" id="main_error" style="color: red;display: none">
                            <label class="control-label col-sm-4" for="main_error_text">Error:</label>
                            <div class="col-sm-8">
                                <h4 id="main_error_text"></h4>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-8">
                                <button type="button" id="editStyleButton" class="btn btn-success form-control">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-2">
                    <div id="barcodeImage">
                      <?php
                        if ($data_style['barcode'] != null) {
                            echo '<label class="control-label">Barcode:</label><br/>';
                              echo '<img src="'.$upload_dir_image.$data_style['barcode'].'" height="100" width="120" />';
                        }
                      ?>
                    </div>
                    <br/><br/>
                    <div >
                      <label class="col-md-12 control-label center-block">Available Colors:</label>
                    <?php
                        foreach ($data_color as $key => $color) {
                          echo '<div class="col-md-6"><span>'. $color['name'] .'</span>';
                          echo '<img src="../../../uploadFiles/inventory/images/'. $color['image'] .'" height="50" width="50"><span>';
                          echo '<a href="javascript:void(0)" onclick="deleteColor('. $key .')">delete</a></div>';
                        }
                     ?>
                    </div>
                    <div id="colorsPreview">
                      
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Add Color modal -->
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
                        <label class="control-label col-sm-4 hide_error" for="colorName">Name:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="colorName" name="colorName" placeholder="Enter Color Name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="colorImage">Color Image:</label>
                        <div class="col-sm-8">
                            <input type="file" class="form-control hide_error" id="colorImage" name="colorImage" placeholder="Enter Image for color">
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
<!--load Footer Section -->
<?php require('../../../trailer.php'); ?>
<!-- load Jquery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- Load Bootstrap JS -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
<!--All Js Scripts -->
<script>
    // Hide Error message
    $('.hide_error').on('click',function () {
        $('#error').hide();
    });
    //Click event on Add Color Button
    var colordata = [];
    $('#addColorButton').on('click',function () {
        var dataNew = new FormData();
        var name = $('#colorName').val();
        if(name == ''){
            $('#error_text').text('Please provide a Color name');
            $('#error').show();
            return false;
        }
        dataNew.append('name',name);
        var fileName = $('#colorImage')[0].files[0];
        if(fileName == ''){
            $('#error_text').text('Please provide a Color Image');
            $('#error').show();
            return false;
        }
        dataNew.append('file',fileName);
        $.ajax({
            type: "POST",
            url: "uploadColor.php",
            cache : false,
            contentType : false,
            processType : false,
            processData : false,
            data: dataNew,
            success: function (response) {

                var responseData = JSON.parse(response);
                if(responseData.status == true) {
                    console.log(responseData.data);
                    $('#addColorForm')[0].reset();
                    $('#addColor').modal('hide');
                    colordata.push(responseData.data);
                    previewImages(colordata);
                } else {
                    $('#error_text').text(responseData.message);
                    $('#error').show();
                }
            }
        });
    });
    // Click event on Edit Style Button
    $('#editStyleButton').on('click',function () {
        var formData = new FormData();
        var colors = [];
        $.each(colordata, function (index, value) {
            colors[index] = value.id;
        });
        if(colors.length == 0){
            $('#main_error_text').text('Please add at-least one color');
            $('#main_error').show();
            return false;
        }
        formData.append('colors',colors);
        var styleNumber = $('#styleNumber').val();
        if(styleNumber == ''){
            $('#main_error_text').text('Please enter Style number');
            $('#main_error').show();
            return false;
        }
        formData.append('styleNumber',styleNumber);
        var barcode = $('#barcode')[0].files[0];
        formData.append('barCode',barcode);
        var sizeScale = $('#sizeScale').val();
        if(sizeScale == ''){
            $('#main_error_text').text('Please Select Size Scale');
            $('#main_error').show();
            return false;
        }
        formData.append('sizeScale',sizeScale);
        var garment = $('#garment').val();
        if(garment == ''){
            $('#main_error_text').text('Please Select Garment type');
            $('#main_error').show();
            return false;
        }
        formData.append('garment',garment);
        var fabric = $('#fabric').val();
        if(fabric == ''){
            $('#main_error_text').text('Please Select a Fabric Type');
            $('#main_error').show();
            return false;
        }
        formData.append('fabric',fabric);
        var sex = $('#sex').val();
        if(sex == ''){
            $('#main_error_text').text('Please Select Gender');
            $('#main_error').show();
            return false;
        }
        formData.append('sex',sex);
        var client = $('#client').val();
        if(client == ''){
            $('#main_error_text').text('Please Select a Client');
            $('#main_error').show();
            return false;
        }
        formData.append('client',client);
        var note = $('#notes').val();
        if(note == ''){
            $('#main_error_text').text('Please enter Note');
            $('#main_error').show();
            return false;
        }
        formData.append('notes',note);
        $.ajax({
            type: "POST",
            url: "addStyleSubmit.php",
            cache : false,
            contentType : false,
            processType : false,
            processData : false,
            data: formData,
            success: function (response) {
                var responseData = JSON.parse(response);
                if(responseData.status == true) {
                    window.location = 'inventoryViewEdit.php?styleId='+responseData.data
                } else {
                    $('#main_error_text').text(responseData.message);
                    $('#main_error').show();
                }
            }
        });
    });

   function previewImages(data) {
        var html = '';
        
        if(data.length > 0) {
           // html += '<label class="col-md-12 control-label center-block">Available Colors:</label>';
            $.each(data, function (index, value) {
                html += '<div class="col-md-6"><span>' + value.name + '</span><img src="../../../uploadFiles/inventory/images/' + value.path + '" height="50" width="50"><span><a href="javascript:void(0)" onclick="deleteColor(' + index + ')">delete</a></div>'
            });
        }
        $('#colorsPreview').html(html);
    }
    function deleteColor(key) {
        console.log(colordata);
        colordata.splice(key, 1);
        console.log(colordata);
        previewImages(colordata);
    }
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#barcodeImage').replaceWith('<div id="barcodeImage"><label class="control-label">Barcode:</label><br/><img src="'+e.target.result+'" height="100" width="120" /></div>');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    function hideMainError() {
        $('#main_error').hide();
    }
</script>
