<?php require('Application.php');
require('../../header.php'); ?>
    <table width="100%">
        <tr>
            <td align="center"><font face="arial">
                    <center><font size="5">Edit Location</font>
                        <br/>
                        <br/>
                        <br/>
                        <table width="50%" border="0" cellspacing="1" cellpadding="1">
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        </table>
                        <br/><br/>
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>Location Name: </td>
                                <td><input type="text" id="location" readonly></td>
                            </tr>
                            <tr>
                                <td>Location Identifier: </td>
                                <td><input type="text" id="identifier" readonly></td>
                            </tr>
                            <tr>
                                <td>Total Warehouse: </td>
                                <td><input type="text" id="warehouse"></td>
                            </tr>
                            <tr>
                                <td>Total Container: </td>
                                <td><input type="text" id="container"></td>
                            </tr>
                            <tr>
                                <td>Total Conveyor: </td>
                                <td><input type="text" id="conveyor"></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td><button>Add Location</button></td>
                                <td><a href="location.php"><button>Cancel</button></a></td>
                            </tr>
                        </table>
                    </center>
            </td>
        </tr>
    </table>
<?php require('../../trailer.php');
?>