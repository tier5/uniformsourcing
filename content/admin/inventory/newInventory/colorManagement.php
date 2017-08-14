<?php
require('Application.php');
/*
 * Get Details of Selected Style
 */
$sql = '';
$sql = 'SELECT * FROM "tbl_invStyle" WHERE "styleId"='.$_GET['styleId'];
if(!($result_cnt=pg_query($connection,$sql))){
    print("Failed Sql: " . pg_last_error($connection));
    exit;
}
$data_style = pg_fetch_array($result_cnt);
pg_free_result($result_cnt);
/*
 * Get Color details Respect to a Style
 */
$sql = '';
$sql = 'SELECT * FROM "tbl_invColor" WHERE "styleId"='.$data_style['styleId'];
if(!($result_cnt=pg_query($connection,$sql))){
    print("Failed Sql: " . pg_last_error($connection));
    exit;
}
while($row_cnt = pg_fetch_array($result_cnt)) {
    $data_color[] = $row_cnt;
}
pg_free_result($result_cnt);
/*
 * Add Header Section
 */
require('../../../header.php');
?>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.6/sweetalert2.min.css" />
<div class="container">
    <div class="page-header">
        <div class="row">
            <div class="col-md-1">
                <button class="btn btn-success btn-xs" id="back"><i class="glyphicon glyphicon-circle-arrow-left"></i> Back</button>
            </div>
            <div class="col-md-11">
                <h3 class="text-center">Color Management (<small><?php echo $data_style['styleNumber']; ?></small>)</h3>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-2 pull-right">
                    <button class="btn btn-success" data-toggle="modal" data-target="#addColorModal"><i class="glyphicon glyphicon-plus"></i> Add Color</button>
                </div>
            </div>
        </div>
        <div class="table">
            <table class="table table-bordered text-center">
                <thead>
                    <td>Color Name</td>
                    <td>Image</td>
                    <td>Action</td>
                </thead>
                <tbody>
                <?php
                foreach ($data_color as $color) {
                    ?>
                    <tr>
                        <td><?php echo $color['name']; ?></td>
                        <td>
                            <img src="<?php echo $upload_dir_image.$color['image']; ?>" alt="image" width="100" height="100" border="1"/>
                        </td>
                        <td>
                            <button data-id="<?php echo $color['colorId']; ?>" data-name="<?php echo $color['name']; ?>" data-image="<?php echo $color['image']; ?>" class="btn btn-info editColor"><i class="glyphicon glyphicon-pencil"></i></button>
                            <button data-color="<?php echo $color['colorId']; ?>" class="btn btn-danger deleteColor"><i class="glyphicon glyphicon-trash"></i></button>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Modal -->
<div id="addColorModal" class="modal fade" role="dialog">
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
<!-- Modal -->
<div class="modal fade" id="editColorModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Color ( <small><span class="colorNameShow"></span></small> ) </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-8">
                            <form class="form-horizontal" id="editColorForm" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label class="control-label col-sm-4 hide_error" for="editColorName">Name:</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="editColorName" name="colorName" placeholder="Enter Color Name">
                                    </div>
                                </div>
                                <input id="editColorId" name="editColorId" type="hidden">
                                <input id="is_change" name="is_change" value="0" type="hidden"/>
                                <div class="form-group">
                                    <label class="control-label col-sm-4" for="editColorImage">Color Image:</label>
                                    <div class="col-sm-8">
                                        <input onchange="readURL(this)" type="file" class="form-control hide_error" id="editColorImage" name="colorImage" placeholder="Enter Image for color">
                                    </div>
                                </div>
                                <div class="form-group" id="edit-error" style="color: red;display: none">
                                    <label class="control-label col-sm-4" for="edit-error_text">Error:</label>
                                    <div class="col-sm-8">
                                        <h4 id="edit-error_text"></h4>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="button" id="editColorButton" class="btn btn-lg btn-success pull-right">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <div class="imagePreview"></div>
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
<script>
    $('#back').on('click',function () {
        window.location.href = 'inventoryManagement.php';
    });
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
        var styleId = '<?php echo $data_style['styleId']; ?>';
        dataNew.append('styleId',styleId);
        $.ajax({
            type: "POST",
            url: "addColor.php",
            cache : false,
            contentType : false,
            processType : false,
            processData : false,
            data: dataNew,
            success: function (response) {
                var responseData = JSON.parse(response);
                if(responseData.status == true) {
                    swal({
                        title: "Added!",
                        text: responseData.message,
                        type: "success"
                    }).then( function(){
                        window.location.reload();
                    },function (dismiss) {
                        window.location.reload();
                    });
                } else {
                    $('#error_text').text(responseData.message);
                    $('#error').show();
                }
            }
        });
    });
    $('.deleteColor').on('click',function () {
        var color = $(this).data('color');
        var styleId = '<?php echo $data_style['styleId']; ?>';
        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then(function () {
            $.ajax({
                type: "POST",
                url: "deleteColor.php",
                data: {
                    colorId : color,
                    styleId : styleId
                },
                success: function (response) {
                    var responseData = JSON.parse(response);
                    if(responseData.success == true) {
                        swal({
                            title: "Deleted!",
                            text: responseData.message,
                            type: "success"
                        }).then( function(){
                            window.location.reload();
                        },function (dismiss) {
                            window.location.reload();
                        });
                    } else {
                        swal(
                            'Oops...',
                            responseData.message,
                            'error'
                        )
                    }
                }
            });
        })
    });
    $('.editColor').on('click',function () {
        var colorName = $(this).data('name');
        var colorId = $(this).data('id');
        var colorImage = $(this).data('image');
        $('.imagePreview').replaceWith('<div class="imagePreview"><label class="control-label">Color:</label><br/><img src="<?php echo $upload_dir_image ?>/'+ colorImage+'" height="100" width="120" /></div>')
        $('.colorNameShow').text(colorName);
        $('#editColorId').val(colorId);
        $('#editColorName').val(colorName);
        $('#is_change').val(0);
        $('#editColorModal').modal('show');
    });
    $('#editColorButton').on('click',function () {
        var dataNew = new FormData();
        var name = $('#editColorName').val();
        if(name == ''){
            $('#edit-error_text').text('Please provide a Color name');
            $('#edit-error').show();
            return false;
        }
        dataNew.append('name',name);
        var fileName = $('#editColorImage')[0].files[0];
        dataNew.append('file',fileName);
        var styleId = '<?php echo $data_style['styleId']; ?>';
        dataNew.append('styleId',styleId);
        var colorId = $('#editColorId').val();
        dataNew.append('colorId',colorId);
        var is_change = $('#is_change').val();
        dataNew.append('is_change',is_change);
        $.ajax({
            type: "POST",
            url: "editColor.php",
            cache : false,
            contentType : false,
            processType : false,
            processData : false,
            data: dataNew,
            success: function (response) {
                var responseData = JSON.parse(response);
                if(responseData.status == true) {
                    swal({
                        title: "Edited!",
                        text: responseData.message,
                        type: "success"
                    }).then( function(){
                        window.location.reload();
                    },function (dismiss) {
                        window.location.reload();
                    });
                } else {
                    $('#edit-error_text').text(responseData.message);
                    $('#edit-error').show();
                }
            }
        });
    });
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('.imagePreview').replaceWith('<div class="imagePreview"><label class="control-label">Color:</label><br/><img src="'+e.target.result+'" height="100" width="120" /></div>');
                $('#is_change').val(1);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>