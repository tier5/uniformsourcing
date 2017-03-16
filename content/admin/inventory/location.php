<?php require('Application.php');
require('../../header.php');
$sql ='select * from "inventoryLocation" order by "id"';
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
                        <table width="50%" border="0" cellspacing="1" cellpadding="1">
                            <tr>
                                <td><a href="addLocation.php"><button>Add Location</button></a></td>
                                <td><a href="editLocation.php"><button>Edit Location</button></a></td>
                            </tr>
                        </table>
                        <br/><br/>
                        <table width="100%" border="5" cellpadding="0" cellspacing="0">
                            <thead>
                                <th>Location</th>
                                <th>Identifier</th>
                                <th>Warehouse</th>
                                <th>Container</th>
                                <th>Conveyor</th>
                                <th>Edit</th>
                            </thead>
                            <tbody>
                            <?php
                            for ($i=0;$i<count($data_location);$i++) {
                                ?>
                                <tr>
                                    <td align="center"><?php echo $data_location[$i]['location'] ?></td>
                                    <td align="center"><?php echo $data_location[$i]['locIdn'] ?></td>
                                    <td align="center"><?php echo $data_location[$i]['totalWarehouse'] ?></td>
                                    <td align="center"><?php echo $data_location[$i]['totalContainer'] ?></td>
                                    <td align="center"><?php echo $data_location[$i]['tatalConveyor'] ?></td>
                                    <td align="center">
                                        <button>Edit</button>
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
<?php require('../../trailer.php');
?>