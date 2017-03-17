<?php require('Application.php');
require('../../header.php');
$sql ='select * from "inventoryLocation" WHERE "id"='.$_GET['locationId'];
if(!($result=pg_query($connection,$sql))){
    $return_arr[0]['error'] = pg_last_error($connection);
    echo json_encode($return_arr);
    return;
}
$row = pg_fetch_array($result);
$data_location[] = $row;
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
                            <input type="hidden" id="dataId" value="<?php echo $data_location[0]['id'] ?>">
                            <tr>
                                <td>Location Name: </td>
                                <td><input type="text" value="<?php echo $data_location[0]['location'] ?>" id="location" readonly style="background-color: #999999"></td>
                            </tr>
                            <tr>
                                <td>Location Identifier: </td>
                                <td><input type="text" value="<?php echo $data_location[0]['locIdn'] ?>" id="identifier" readonly style="background-color: #999999"></td>
                            </tr>
                            <tr>
                                <td>Total Warehouse: </td>
                                <td><input type="text" value="<?php echo $data_location[0]['totalWarehouse'] ?>" id="warehouse"></td>
                            </tr>
                            <tr>
                                <td>Total Container: </td>
                                <td><input type="text" value="<?php echo $data_location[0]['totalContainer'] ?>" id="container"></td>
                            </tr>
                            <tr>
                                <td>Total Conveyor: </td>
                                <td><input type="text" value="<?php echo $data_location[0]['tatalConveyor'] ?>" id="conveyor"></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td><button onclick="addLocation()">Edit Location</button></td>
                                <td><a href="location.php"><button>Cancel</button></a></td>
                            </tr>
                        </table>
                    </center>
            </td>
        </tr>
    </table>
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
            var warehouse = document.getElementById('warehouse').value;
            if(warehouse == ''){
                warehouse = 0;
            }
            var container = document.getElementById('container').value;
            if(container == ''){
                container = 0;
            }
            var conveyor = document.getElementById('conveyor').value;
            if(conveyor == ''){
                conveyor = 0;
            }
            $.ajax({
                url: "submitEditLocation.php",
                type: "post",
                data:{
                    id: document.getElementById('dataId').value,
                    warehouse: warehouse,
                    container: container,
                    conveyor: conveyor,
                },
                success: function (response) {
                    if(response==1){
                        $("#message").html("<div class='successMessage'><strong>Location Added Successfully. Please Wait....</strong></div>");
                        setTimeout(function(){
                            $(location).attr('href', 'location.php');
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