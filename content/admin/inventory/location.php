<?php require('Application.php');
require('../../header.php');
$sql ='select * from "tbl_invLocation" order by "locationId"';
if(!($result=pg_query($connection,$sql))){
    $return_arr[0]['error'] = pg_last_error($connection);
    echo json_encode($return_arr);
    return;
}
while ($row = pg_fetch_array($result)) {
    $data_location[] = $row;
}
pg_free_result($result);
?>


    <table width="100%">
        <tr>
            <td align="center"><font face="arial">
                    <center><font size="5">Location</font>
                        <br/>
                        <br/>
                        <br/>
                        <div id="message"></div>
                        <table width="75%" border="0" cellspacing="1" cellpadding="1">
                            <tr>
                                <td>Location: <input type="text" id="addlocation"></td>
                                <td>Identifier: <input type="text" id="addidentifier"></td>
                                <td><button onclick="addLocation()">Add Location</button></td>
                                <!--<td><a href="editLocation.php"><button>Edit Location</button></a></td>-->
                            </tr>
                        </table>
                        <br/><br/>
                        <table width="100%" border="5" cellpadding="0" cellspacing="0">
                            <thead>
                                <th>Location</th>
                                <th>Identifier</th>
                                <!--<th>Warehouse</th>
                                <th>Container</th>
                                <th>Conveyor</th>-->
                                <th>Edit</th>
                                <th>Delete</th>
                            </thead>
                            <tbody>
                            <?php
                            for ($i=0;$i<count($data_location);$i++) {
                                ?>
                                <tr>
                                    <td align="center"><?php echo $data_location[$i]['name'] ?></td>
                                    <td align="center"><?php echo $data_location[$i]['identifier'] ?></td>
                                    <!--<td align="center"><?php /*echo $data_location[$i]['totalWarehouse'] */?></td>
                                    <td align="center"><?php /*echo $data_location[$i]['totalContainer'] */?></td>
                                    <td align="center"><?php /*echo $data_location[$i]['tatalConveyor'] */?></td>-->
                                    <td align="center">
                                        <a href="editLocation.php?locationId=<?php echo $data_location[$i]['locationId'] ?>">
                                            <button>Edit</button>
                                        </a>
                                    </td>
                                    <td align="center">
                                        <button>Delete</button>
                                    </td>
                                </tr>
                                <?php
                            }
 ?>
                            </tbody>
                        </table>
                    </center>
            </td>
        </tr>
    </table>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript">
        function addLocation() {
            var locationName = document.getElementById('addlocation').value;
            if(locationName == '') {
                alert("Please Provide a location");
                return false;
            }
            var identifier = document.getElementById('addidentifier').value;
            if(identifier == '') {
                alert("please provide a Location Identifier");
                return false;
            }
            if(identifier.length != 3){
                alert("Location Identifier Length should be 3");
                return false;
            }
            $.ajax({
                url: "submitAddLocation.php",
                type: "post",
                data:{
                    name: locationName,
                    identifier: identifier,
                },
                success: function (response) {
                    if(response==1){
                        $("#message").html("<div class='successMessage'><strong>Location Added Successfully. Please Wait....</strong></div>");
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