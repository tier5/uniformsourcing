<?php require('Application.php');
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
$query = "SELECT warehouse from \"locationDetails\"";
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
$query = "SELECT container from \"locationDetails\"";
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
$query = "SELECT conveyor from \"locationDetails\"";
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
                            <tr><button onclick="addWarehouse()">Add Warehouse</button></tr>
                            <tr><button onclick="addContainer()">Add Container</button></tr>
                            <tr><button onclick="addConveyor()">Add Conveyor</button></tr>
                        </table>
                        <br/><br/><br/>
                        <table width="25%" border="1" style="float: left">
                            <thead><tr><th>Warehouse</th></tr></thead>
                            <tbody>
                            <?php
                            if (count($data_warehouse) > 0) {
                                for ($i=0;$i<count($data_warehouse);$i++){
                                    if($data_warehouse[$i]['warehouse'] != null) { ?>
                                        <tr><td align="center"><?php echo $data_warehouse[$i]['warehouse']; ?></td></tr>
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
                                            <tr><td align="center"><?php echo $data_container[$i]['container']; ?></td></tr>
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
                                        <tr><td align="center"><?php echo $conveyor[$i]['conveyor']; ?></td></tr>
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
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript">
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
                    } else {
                        $("#message").html("<div class='errorMessage'><strong>Something wrong Please try again later</strong></div>");
                    }
                }
            });
        };
        function addWarehouse() {
            $.ajax({
                url: "addWarehouse.php",
                type: "post",
                data: {
                    id: document.getElementById('dataId').value,
                },
                success: function (data) {
                    if(data==1){
                        $("#message").html("<div class='successMessage'><strong>Warehouse Added Successfully. Please Wait....</strong></div>");
                        setTimeout(function(){
                            location.reload();
                        }, 2000);
                    } else {
                        $("#message").html("<div class='errorMessage'><strong>Something wrong Please try again later</strong></div>");
                    }
                }
            });
        };
        function addContainer() {
            $.ajax({
                url: "addContainer.php",
                type: "post",
                data: {
                    id: document.getElementById('dataId').value,
                },
                success: function (data) {
                    if(data==1){
                        $("#message").html("<div class='successMessage'><strong>Container Added Successfully. Please Wait....</strong></div>");
                        setTimeout(function(){
                              location.reload();
                        }, 2000);
                    } else {
                        $("#message").html("<div class='errorMessage'><strong>Something wrong Please try again later</strong></div>");
                    }
                }
            });
        };
        function addConveyor() {
            $.ajax({
                url: "addConveyor.php",
                type: "post",
                data: {
                    id: document.getElementById('dataId').value,
                },
                success: function (data) {
                    if(data==1){
                        $("#message").html("<div class='successMessage'><strong>Conveyor Added Successfully. Please Wait....</strong></div>");
                        setTimeout(function(){
                            location.reload();
                        }, 2000);
                    } else {
                        $("#message").html("<div class='errorMessage'><strong>Something wrong Please try again later</strong></div>");
                    }
                }
            });
        }
    </script>
<?php require('../../trailer.php');
?>