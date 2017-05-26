<?php
require('Application.php');
require('../header.php');
$queryVendor = "SELECT \"vendorID\", \"vendorName\", \"active\" " .
        "FROM \"vendor\" " .
        "WHERE \"active\" = 'yes' " .
        "ORDER BY \"vendorName\" ASC ";
if (!($result = pg_query($connection, $queryVendor))) {
    print("Failed VendorQuery: " . pg_last_error($connection));
    exit;
}
while ($row = pg_fetch_array($result)) {
    $data_Vendr[] = $row;
}
$query1 = ("SELECT \"ID\", \"clientID\", \"client\", \"active\" " .
        "FROM \"clientDB\" " .
        "WHERE \"active\" = 'yes' " .
        "ORDER BY \"client\" ASC");
if (!($result1 = pg_query($connection, $query1))) {
    print("Failed query1: " . pg_last_error($connection));
    exit;
}
while ($row1 = pg_fetch_array($result1)) {
    $data1[] = $row1;
}
pg_free_result($result1);
?>
<form action="newemp.php" method="post">
    <table align="center">
        <tr>
            <td colspan=2><font face="arial"><b>Enter New Employee</b></font></td>
        </tr>
        <tr>
            <td><font face="arial" color="red">*(r)</font><font face="arial"><b>Employee Type </b></font></td>
            <td>
                <input type="radio" name="employeeType" value="0"  onclick="setVisibility('');" checked="checked"/>Employee
                <input type="radio" name="employeeType" value="1" onclick="setVisibility('vendor');"/>Vendor
                <input type="radio" name="employeeType" value="2" onclick="setVisibility('client');"/>Client 
                <input type="radio" name="employeeType" value="3" onclick="setVisibility('');"/>Sales
                <input type="radio" name="employeeType" value="4" onclick="setVisibility('');"/>Inventory Group
                <input type="radio" name="employeeType" value="5" onclick="setVisibility('');"/>Sales Person
            </td>
        </tr>
        <tr id="vendor" style="display:none">
            <td><font face="arial" color="red">*(r)</font><font face="arial"><b>Vendor Name </b></font></td>
            <td>
                <select name="vendorName">
                    <?php
                    for ($i = 0; $i < count($data_Vendr); $i++) {
                        echo '<option value="' . $data_Vendr[$i]['vendorID'] . '">' . $data_Vendr[$i]['vendorName'] . '</option>';
                    }
                    ?> 
                </select> 
            </td>
        </tr>
        <tr id="client" style="display:none">
            <td><font face="arial" color="red">*(r)</font><font face="arial"><b>Client Name </b></font></td>
            <td><select name="clinetname">
                    <?php
                    for ($i = 0; $i < count($data1); $i++) {
                        echo '<option value="' . $data1[$i]['ID'] . '">' . $data1[$i]['client'] . '</option>';
                    }
                    ?> 
                </select> </td>
        </tr>
        <tr>
            <td><font face="arial" color="red">*(r)</font><font face="arial"><b>First Name</b></font></td>
            <td><input type="text" name="firstnamenew" size="20"></td>
        </tr>
        <tr>
            <td><font face="arial" color="red">*(r)</font><font face="arial"><b>Last Name</b></font></td>
            <td><input type="text" name="lastnamenew" size="20"></td>
        </tr>
        <tr>
            <td><font face="arial"><b>Title</b></font></td>
            <td><input type="text" name="titlenew" size="20"></td>
        </tr>
        <tr>
            <td><font face="arial" color="red">*(r)</font><font face="arial"><b>Address</b></font></td>
            <td><input type="text" name="addressnew" size="30"></td>
        </tr>
        <tr>
            <td><font face="arial" color="red">*(r)</font><font face="arial"><b>City</b></font></td>
            <td><input type="text" name="citynew" size="30"></td>
        </tr>
        <tr>
            <td><font face="arial" color="red">*(r)</font><font face="arial"><b>State</b><font></td>
            <td><input type="text" name="statenew" size="3"></td>
        </tr>
        <tr>
            <td><font face="arial" color="red">*(r)</font><font face="arial"><b>Zip</b></font></td>
            <td><input type="text" name="zipnew" size="10"></td>
        </tr>
        <tr>
            <td><font face="arial" color="red">*(r)</font><font face="arial"><b>Phone</b></font></td>
            <td><input type="text" name="phonenew" size="20"></td>
        </tr>
        <tr>
            <td><font face="arial"><b>Pager</b></font></td>
            <td><input type="text" name="pagernew" size="20"></td>
        </tr>
        <tr>
            <td><font face="arial"><b>Alpha Pager</b></font></td>
            <td><input type="text" name="alphapagernew" size="20"></td>
        </tr>
        <tr>
            <td><font face="arial" color="red">*(r)</font><font face="arial"><b>Cellular</b></font></td>
            <td><input type="text" name="cellnew" size="20"></td>
        </tr>
        <tr>
            <td><font face="arial" color="red">*(r)</font><font face="arial"><b>Email</b></font></td>
            <td><input type="text" name="emailnew" size="20"></td>
        </tr>
        <tr>
            <td><font face="arial" color="red">*(r)</font><font face="arial"><b>Date Hired</b></font></td>
            <td><select name="monthhirednew">
                    <option value="month">Month</option>
                    <option value="01">January</option>
                    <option value="02">February</option>
                    <option value="03">March</option>
                    <option value="04">April</option>
                    <option value="05">May</option>
                    <option value="06">June</option>
                    <option value="07">July</option>
                    <option value="08">August</option>
                    <option value="09">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
                <select name="dayhirednew">
                    <option value="day">Day</option>
                    <option value="01">01</option>
                    <option value="02">02</option>
                    <option value="03">03</option>
                    <option value="04">04</option>
                    <option value="05">05</option>
                    <option value="06">06</option>
                    <option value="07">07</option>
                    <option value="08">08</option>
                    <option value="09">09</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                    <option value="13">13</option>
                    <option value="14">14</option>
                    <option value="15">15</option>
                    <option value="16">16</option>
                    <option value="17">17</option>
                    <option value="18">18</option>
                    <option value="19">19</option>
                    <option value="20">20</option>
                    <option value="21">21</option>
                    <option value="22">22</option>
                    <option value="23">23</option>
                    <option value="24">24</option>
                    <option value="25">25</option>
                    <option value="26">26</option>
                    <option value="27">27</option>
                    <option value="28">28</option>
                    <option value="29">29</option>
                    <option value="30">30</option>
                    <option value="31">31</option>
                </select>
                <select name="yearhirednew">  
                    <option value="year">Year</option>

                    <?php
                    for ($i = date('Y'); $i >= 1995; $i--) {
                        echo "<option value='" . $i . "'>" . $i . "</option>";
                    }
                    ?>
                </select></td>
        </tr>
        <tr>
            <td><font face="arial"><b>Salary</b></font></td>
            <td><select name="salarynew">
                    <option value="no">No</option>
                    <option value="yes">Yes</option>
                </select></td>
        </tr>
        <tr>
            <td><font face="arial"><b>Wage</b></font></td>
            <td><input type="text" name="wagenew" size="20"></td>
        </tr>
        <tr>
            <td><font face="arial" color="red">*(r)</font><font face="arial"><b>Username</b></font></td>
            <td><input type="text" name="newusername" size="20"></td>
        </tr>
        <tr>
            <td><font face="arial" color="red">*(r)</font><font face="arial"><b>Password</b></font></td>
            <td><input type="text" name="newpassword" size="20"></td>
        </tr>
        <tr>
            <td><font face="arial" color="red">*(r)</font><font face="arial"><b>POP Password</b></font></td>
            <td><input type="password" name="newpoppassword" size="20"></td>
        </tr>
    </table>
    <table width="80%">
        <tr>
            <td colspan=5 align="center"><br>
                <br>
                <input type="Submit" value="     Enter New Employee     "></td>
        </tr>
    </table>
</form>
<script type="text/javascript">
    function setVisibility(id)
    {				
        switch(id)
        {
            case 'vendor':
                {
                    document.getElementById('client').style.display="none";
                    document.getElementById('vendor').style.display="";
                    break;
                }
            case 'client':
                {
                    document.getElementById('client').style.display="";
                    document.getElementById('vendor').style.display="none";
                    break;
                }
            default:
                {
                    document.getElementById('client').style.display="none";
                    document.getElementById('vendor').style.display="none";
                }
        }
    }
</script>
<?php
require('../trailer.php');
?>