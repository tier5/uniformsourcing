<style>
    .error {
        border: solid 2px #FF0000;
    }
    .modal-box {
        display: none;
        position: absolute;
        z-index: 1000;
        width: 98%;
        background: white;
        border-bottom: 1px solid #aaa;
        border-radius: 4px;
        box-shadow: 0 3px 9px rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(0, 0, 0, 0.1);
        background-clip: padding-box;
    }
    @media (min-width: 32em) {

        .modal-box { width: 70%; }
    }
    .modal-box header,
    .modal-box .modal-header {
        padding: 1.25em 1.5em;
        border-bottom: 1px solid #ddd;
    }
    .modal-box header h3,
    .modal-box header h4,
    .modal-box .modal-header h3,
    .modal-box .modal-header h4 { margin: 0; }
    .modal-box .modal-body { padding: 2em 1.5em; }
    .modal-box footer,
    .modal-box .modal-footer {
        padding: 1em;
        border-top: 1px solid #ddd;
        background: rgba(0, 0, 0, 0.02);
        text-align: right;
    }
    .modal-overlay {
        opacity: 0;
        filter: alpha(opacity=0);
        position: absolute;
        top: 0;
        left: 0;
        z-index: 900;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.3) !important;
    }
    a.close {
        line-height: 1;
        font-size: 1.5em;
        position: absolute;
        top: 5%;
        right: 2%;
        text-decoration: none;
        color: #bbb;
    }
    a.close:hover {
        color: #222;
        -webkit-transition: color 1s ease;
        -moz-transition: color 1s ease;
        transition: color 1s ease;
    }
</style>
<script type="text/javascript"
        src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script></div>
<?php
    require('Application.php');
    require('../../header.php');
    $sql ='select * from "tbl_invLocation" WHERE "locationId"='.$_GET['locationId'];
    if(!($result=pg_query($connection,$sql))){
        $return_arr[0]['error'] = pg_last_error($connection);
        echo json_encode($return_arr);
        return;
    }
    $row = pg_fetch_array($result);
    $data_location[] = $row;
    $query = '';
    $query = "SELECT id,warehouse from \"locationDetails\"";
    $query .= "  where \"locationId\"='" . $_GET['locationId'] . "' ";
    if (!($resultProduct = pg_query($connection, $query))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($resultProduct)) {
        $data_warehouse[] = $row;
    }
    pg_free_result($result);
    pg_free_result($resultProduct);
    $query = '';
    $query = "SELECT id,container from \"locationDetails\"";
    $query .= "  where \"locationId\"='" . $_GET['locationId'] . "' ";
    if (!($resultProduct = pg_query($connection, $query))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($resultProduct)) {
        $data_container[] = $row;
    }
    pg_free_result($resultProduct);
    $query = '';
    $query = "SELECT id,conveyor from \"locationDetails\"";
    $query .= "  where \"locationId\"='" . $_GET['locationId'] . "' ";
    if (!($resultProduct = pg_query($connection, $query))) {
        print("Failed invQuery: " . pg_last_error($connection));
        exit;
    }
    while ($row = pg_fetch_array($resultProduct)) {
        $conveyor[] = $row;
    }
    pg_free_result($resultProduct);
?>
    <table width="100%">
        <tr>
            <td align="center"><font face="arial">
                    <center><font size="5">Edit Location</font>
                        <br/>
                        <br/>
                        <br/>
                        <table width="50%" border="0" cellspacing="1" cellpadding="1">
                            <tr>
                                <td>&nbsp;<div id="message"></div></td>
                                <td>&nbsp;</td>
                            </tr>
                        </table>
                        <br/><br/>
                        <table border="0" cellpadding="0" cellspacing="0">
                            <input type="hidden" id="dataId" value="<?php echo $data_location[0]['locationId'] ?>">
                            <tr>
                                <td>Location Name: </td>
                                <td><input type="text" value="<?php echo $data_location[0]['name'] ?>" id="location" readonly style="background-color: #999999"></td>
                                <td>Location Identifier: </td>
                                <td><input type="text" value="<?php echo $data_location[0]['identifier'] ?>" id="identifier"></td>
                                <td><button onclick="addLocation()">Update Location</button></td>
                                <!--<td><a href="location.php"><button>Cancel</button></a></td>-->
                            </tr>
                        </table>
                        <br /><br/><br/>
                        <table width="50%">
                            <tr><button id="addWarehouse">Add Warehouse</button></tr>
                            <tr><button id="addContainer">Add Container</button></tr>
                            <tr><button id="addconveyor">Add Conveyor</button></tr>
                        </table>
                        <br/><br/><br/>
                        <table with="50%" style="font-size: 12px;">
                            <tr id="warehouseIdentifier" style="display: none;">
                                <td style="color: #0c00d2"><strong>Add Warehouse Identifier</strong></td>
                                <td><input type="text" id="warehouse"></td>
                                <td><button onclick="addWarehouse()">Add</button></td>
                            </tr>
                            <tr id="containerIdentifier" style="display: none;">
                                <td style="color: #0c00d2"><strong>Add Container Identifier</strong></td>
                                <td><input type="text" id="container"></td>
                                <td><button onclick="addContainer()">Add</button></td>
                            </tr>
                            <tr id="conveyorIdentifier" style="display: none;">
                                <td style="color: #0c00d2"><strong>Add Conveyor Identifier</strong></td>
                                <td><input type="text" id="conveyor"></td>
                                <td><button onclick="addConveyor()">Add</button></td>
                            </tr>
                        </table>
                        <br/><br/><br/>
                        <table width="25%" border="1" style="float: left">
                            <thead><tr><th>Warehouse</th></tr></thead>
                            <tbody>
                            <?php
                            if (count($data_warehouse) > 0) {
                                for ($i=0;$i<count($data_warehouse);$i++){
                                    if($data_warehouse[$i]['warehouse'] != null) { ?>
                                        <tr>
                                            <td align="center" style="font-size: 16px;">
                                                <strong><?php echo $data_warehouse[$i]['warehouse']; ?></strong>
                                                <a href="javascript:void(0)" data-modal-name="<?php echo $data_warehouse[$i]['warehouse']; ?>" data-modal-id="popup1" data-modal-locationId="<?php echo $data_warehouse[$i]['id']; ?>" style="float: right; color: #00A0D1;font-size: 11px;">|&nbsp;&nbsp;Edit&nbsp;&nbsp;</a>
                                                <a href="javascript:void(0)" onclick="deleteStorage(<?php echo $data_warehouse[$i]['id']; ?>,'warehouse')" style="float: right; color: red;font-size: 11px;">Delete  &nbsp;&nbsp;|</a>
                                            </td>
                                        </tr>
                                    <?php  }
                                }
                            } else {
                                ?><tr><td>No Warehouse found</td></tr><?php
                            }
                            ?>
                            </tbody>
                        </table>
                        <table width="25%" border="1" style="float: left ">
                            <thead><tr><th>Container</th></tr></thead>
                            <tbody>
                            <?php
                                if (count($data_container) > 0) {
                                    for ($i=0;$i<count($data_container);$i++){
                                        if($data_container[$i]['container'] != null) { ?>
                                            <tr>
                                                <td align="center" style="font-size: 16px;">
                                                    <strong ><?php echo $data_container[$i]['container']; ?></strong>
                                                    <a href="javascript:void(0)" data-modal-name="<?php echo $data_container[$i]['container']; ?>" data-modal-id="popup2" data-modal-locationId="<?php echo $data_container[$i]['id']; ?>" style="float: right; color: #00A0D1;font-size: 11px;">|&nbsp;&nbsp;Edit&nbsp;&nbsp;</a>
                                                    <a herf="javascript:void(0)" onclick="deleteStorage(<?php echo $data_container[$i]['id']; ?>,'container')" style="float: right; color: red; font-size: 11px;">Delete  &nbsp;&nbsp;|</a>
                                                </td>
                                            </tr>
                                        <?php  }
                                    }
                                }else {
                                    echo "<tr><td>No container found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <table width="25%" border="1" style="float: left ">
                            <thead><tr><th>Conveyor</th></tr></thead>
                            <tbody>
                            <?php
                            if (count($conveyor) > 0) {
                                for ($i=0;$i<count($conveyor);$i++){
                                    if($conveyor[$i]['conveyor'] != null) { ?>
                                        <tr>
                                            <td align="center" style="font-size: 16px;">
                                                <strong ><?php echo $conveyor[$i]['conveyor']; ?></strong>
                                                <a href="javascript:void(0)" data-modal-name="<?php echo $conveyor[$i]['conveyor']; ?>" data-modal-id="popup3" data-modal-locationId="<?php echo $conveyor[$i]['id']; ?>" style="float: right; color: #00A0D1;font-size: 11px;"> |&nbsp;&nbsp;Edit &nbsp;&nbsp;</a>
                                                <a href="javascript:void(0)" onclick="javascript:deleteStorage(<?php echo $conveyor[$i]['id']; ?>,'conveyor')" style="float: right; color: red;font-size: 11px"> Delete &nbsp;&nbsp;|</a>
                                            </td>
                                        </tr>
                                    <?php  }
                                }
                            } else {
                                echo "<tr><td>No Conveyor found</td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </center>
            </td>
        </tr>
    </table>
    <br/><br/>
    <div id="popup1" class="modal-box">
        <header> <a href="#" class="js-modal-close close">×</a>
            <h3>Edit Warehouse</h3>
        </header>
        <div class="modal-body">
            <p style="text-align: center;">
                <input type="text" id="warehouseEdit">
                <button id="warehouseButton">Submit</button>
            </p>
        </div>
        <footer> <a href="#" class="btn btn-small js-modal-close">Close</a> </footer>
    </div>
    <div id="popup2" class="modal-box">
        <header> <a href="#" class="js-modal-close close">×</a>
            <h3>Edit Container</h3>
        </header>
        <div class="modal-body">
            <p style="text-align: center;">
                <input type="text" id="containerEdit">
                <button id="containerButton">Submit</button>
            </p>
        </div>
        <footer> <a href="#" class="btn btn-small js-modal-close">Close</a> </footer>
    </div>
    <div id="popup3" class="modal-box">
        <header> <a href="#" class="js-modal-close close">×</a>
            <h3>Edit Conveyor</h3>
        </header>
        <div class="modal-body">
            <p style="text-align: center;">
                <input type="text" id="conveyorEdit">
                <button id="conveyorButton">Submit</button>
            </p>
        </div>
        <footer> <a href="#" class="btn btn-small js-modal-close">Close</a> </footer>
    </div>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script>
    $(function(){

        var appendthis =  ("<div class='modal-overlay js-modal-close'></div>");

        $('a[data-modal-id]').click(function(e) {
            e.preventDefault();
            $("body").append(appendthis);
            $(".modal-overlay").fadeTo(500, 0.7);
            var modalBox = $(this).attr('data-modal-id');
            var locationId = $(this).attr('data-modal-locationId');
            var name = $(this).attr('data-modal-name');
            if(modalBox == 'popup1'){
                $('#warehouseEdit').val(name);
                $('#warehouseButton').attr('onclick','editStorage('+locationId+',"warehouse");');
            } else if(modalBox == 'popup2'){
                $('#containerEdit').val(name);
                $('#containerButton').attr('onclick','editStorage('+locationId+',"container");');
            } else {
                $('#conveyorEdit').val(name);
                $('#conveyorButton').attr('onclick','editStorage('+locationId+',"conveyor");');
            }
            $('#'+modalBox).fadeIn($(this).data());
        });
        $(".js-modal-close, .modal-overlay").click(function() {
            $(".modal-box, .modal-overlay").fadeOut(500, function() {
                $(".modal-overlay").remove();
            });
        });
        $(window).resize(function() {
            $(".modal-box").css({
                top: ($(window).height() - $(".modal-box").outerHeight()) / 2,
                left: ($(window).width() - $(".modal-box").outerWidth()) / 2
            });
        });
        $(window).resize();
    });
</script>
    <script type="text/javascript">

    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-36251023-1']);
    _gaq.push(['_setDomainName', 'jqueryscript.net']);
    _gaq.push(['_trackPageview']);
    (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
</script>
    <script type="text/javascript">
        $('#addWarehouse').on('click',function () {
            $('#warehouseIdentifier').show();
            $('#containerIdentifier').hide();
            $('#conveyorIdentifier').hide();
        });
        $('#addContainer').on('click',function () {
            $('#warehouseIdentifier').hide();
            $('#containerIdentifier').show();
            $('#conveyorIdentifier').hide();
        });
        $('#addconveyor').on('click',function () {
            $('#warehouseIdentifier').hide();
            $('#containerIdentifier').hide();
            $('#conveyorIdentifier').show();
        });
        function addLocation() {
            var locationName = document.getElementById('location').value;
            if(locationName == '') {
                alert("Please Provide a location");
                return false;
            }
            var identifier = document.getElementById('identifier').value;
            if(identifier == '') {
                alert("please provide a Location Identifier");
                return false;
            }
            if(identifier.length > 3){
                alert("Location Identifier Length should be maximum 3");
                return false;
            }
            var regex =  new RegExp("^[a-zA-Z0-9\-]{1,3}$");
            if(!regex.test(identifier)){
                alert("Only Alpha-Numeric value allowed with maximum length 3");
                return false;
            }
            $.ajax({
                url: "submitEditLocation.php",
                type: "post",
                data:{
                    id: document.getElementById('dataId').value,
                    identifier: identifier,
                },
                success: function (response) {
                    if(response==1){
                        $("#message").html("<div class='successMessage'><strong>Location updated Successfully. Please Wait....</strong></div>");
                        setTimeout(function(){
                            location.reload();
                        }, 2000);
                    } else if(response == 2){
                        $('#message').html('<h1 style="color: red;">Location Identifier is Already Exists</h1>')
                        $('#message').show();
                    } else {
                        $("#message").html("<div class='errorMessage'><strong>Something wrong Please try again later</strong></div>");
                    }
                }
            });
        };
        $('#warehouse').keyup(function () {
            $('#message').hide();
            $('#warehouse').removeClass('error');
        });
        $('#container').keyup(function () {
            $('#message').hide();
            $('#container').removeClass('error');
        })
        ;$('#conveyor').keyup(function () {
            $('#message').hide();
            $('#conveyor').removeClass('error');
        });
        function addWarehouse() {
            var name = $('#warehouse').val();
            if(name == ''){
                $('#warehouse').addClass('error');
                $('#message').html('<h2 style="color:red;">Please Enter Warehouse Identifier<h3>');
                return false;
            }
            var regex =  new RegExp("^[a-zA-Z0-9\-]{1,10}$");
            if(!regex.test(name)){
                alert("Only Alpha-Numeric value allowed with maximum length 10");
                return false;
            }
            $.ajax({
                url: "addWarehouse.php",
                type: "post",
                data: {
                    id: document.getElementById('dataId').value,
                    name: name
                },
                success: function (data) {
                    if(data==1){
                        $("#message").html("<div class='successMessage'><strong>Warehouse Added Successfully. Please Wait....</strong></div>");
                        $('#message').show();
                        setTimeout(function(){
                            location.reload();
                        }, 2000);
                    } else if(data == 2) {
                        $('#message').html('<h1 style="color: red;">Storage Identifier is Already Exists</h1>')
                        $('#message').show();
                    } else {
                        $("#message").html("<div class='errorMessage'><strong>Something wrong Please try again later</strong></div>");
                        $('#message').show();
                    }
                }
            });
        };
        function addContainer() {
            var name = $('#container').val();
            if(name == ''){
                $('#container').addClass('error');
                $('#message').html('<h2 style="color:red;">Please Enter Container Identifier<h3>');
                return false;
            }
            var regex =  new RegExp("^[a-zA-Z0-9\-]{1,10}$");
            if(!regex.test(name)){
                alert("Only Alpha-Numeric value allowed with maximum length 10");
                return false;
            }
            $.ajax({
                url: "addContainer.php",
                type: "post",
                data: {
                    id: document.getElementById('dataId').value,
                    name: name
                },
                success: function (data) {
                    if(data==1){
                        $("#message").html("<div class='successMessage'><strong>Container Added Successfully. Please Wait....</strong></div>");
                        $('#message').show();
                        setTimeout(function(){
                              location.reload();
                        }, 2000);
                    } else if(data == 2) {
                        $('#message').html('<h1 style="color: red;">Storage Identifier is Already Exists</h1>')
                        $('#message').show();
                    } else {
                        $("#message").html("<div class='errorMessage'><strong>Something wrong Please try again later</strong></div>");
                        $('#message').show();
                    }
                }
            });
        };
        function addConveyor() {
            var name = $('#conveyor').val();
            if(name == ''){
                $('#conveyor').addClass('error');
                $('#message').html('<h2 style="color: red">Please Enter Conveyor Identifier<h3>');
                return false;
            }
            var regex =  new RegExp("^[a-zA-Z0-9\-]{1,10}$");
            if(!regex.test(name)){
                alert("Only Alpha-Numeric value allowed with maximum length 10");
                return false;
            }
            $.ajax({
                url: "addConveyor.php",
                type: "post",
                data: {
                    id: document.getElementById('dataId').value,
                    name: name
                },
                success: function (data) {
                    if(data==1){
                        $("#message").html("<div class='successMessage'><strong>Conveyor Added Successfully. Please Wait....</strong></div>");
                        $('#message').show();
                        setTimeout(function(){
                            location.reload();
                        }, 2000);
                    } else if(data == 2) {
                        $('#message').html('<h1 style="color: red;">Storage Identifier is Already Exists</h1>')
                        $('#message').show();
                    } else {
                        $("#message").html("<div class='errorMessage'><strong>Something wrong Please try again later</strong></div>");
                        $('#message').show();
                    }
                }
            });
        };
        function editStorage(id,type) {
            var name = '';
            if(type == 'warehouse'){
                name = $('#warehouseEdit').val();
            } else if(type == 'container'){
                name = $('#containerEdit').val();
            } else {
                name = $('#conveyorEdit').val();
            }
            var regex =  new RegExp("^[a-zA-Z0-9\-]{1,10}$");
            if(!regex.test(name)){
                alert("Only Alpha-Numeric value allowed with maximum length 10");
                return false;
            }
            $.ajax({
                url: "editStorage.php",
                type: "post",
                data: {
                    id: id,
                    type: type,
                    name: name,
                },
                success: function (response) {
                    $(".modal-box, .modal-overlay").fadeOut(500, function() {
                        $(".modal-overlay").remove();
                    });
                    if (response == 1) {
                        $("#message").html("<div class='successMessage'><strong>"+type+" Edited Successfully. Please Wait....</strong></div>");
                        $('#message').show();
                        setTimeout(function(){
                            location.reload();
                        }, 2000);
                    } else if(response == 2){
                        $('#message').html('<h1 style="color: red;">Storage Identifier is Already Exists</h1>')
                        $('#message').show();
                    } else {
                        $("#message").html("<div class='errorMessage'><strong>Something wrong Please try again later</strong></div>");
                        $('#message').show();
                    }
                }
            });
        };
        function deleteStorage(id,type) {
            if(type != '' && type != undefined){
                if (confirm("Are you sure you want to Delete this "+ type) == true) {
                    $.ajax({
                        url: "deleteStorage.php",
                        type: "post",
                        data: {
                            id: id,
                            type: type,
                        },
                        success: function (response) {
                            console.log(response);
                            if (response == 1) {
                                alert( type+" Delete Successfull");
                                window.location.reload();
                            } else if(response == 2){
                                alert("This Location is Not Empty Please Empty The Location first");
                            } else {
                                alert('Internal server error please try again after some time');
                            }
                        }
                    });
                } else {
                    console.log('cancel');
                }
            }
        };
    </script>
<?php require('../../trailer.php');
?>