<?php
require('Application.php');
require('../header.php');
$empid=$_GET['employeeID'];
$query1=("SELECT * ".
		 "FROM \"employeeDB\" ".
		 "WHERE \"employeeID\" = '$empid'");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1=pg_fetch_array($result1)){
	$data1=$row1;
}
$datehired=$data1[0]['datehired'];
$datehired1=$datehired;
$monthnamehired=date("F", $datehired1);
$monthhired=date("m", $datehired1);
$dayhired=date("d", $datehired1);
$yearhired=date("Y", $datehired1);
$query2=("SELECT * ".
		 "FROM \"permissions\" ".
		 "WHERE \"employee\" = '$empid'");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2)){
	$data2[]=$row2;
}
$queryVendor="SELECT \"vendorID\", \"vendorName\", \"active\" ".
		 "FROM \"vendor\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER BY \"vendorName\" ASC ";
	if(!($result=pg_query($connection,$queryVendor))){
	print("Failed VendorQuery: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_Vendr[]=$row;
}
$query1=("SELECT \"ID\", \"clientID\", \"client\", \"active\" ".
		 "FROM \"clientDB\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER BY \"client\" ASC");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data_client[]=$row1;
}
pg_free_result($result1);
?>
<form action="editemp.php" method="post">
<table width="70%" align="center">
<tr bgcolor="#C0C0C0">
<td colspan="2"><font face="arial"><b>Edit Employee Record</b></font></td>
</tr>

<tr>
                <td align="right"><font face="arial" ></font><font face="arial"><b>Employee Type </b></font></td>
                <td align="left"> 
<?php
				if($data1['employeeType']==1)
				{
?>
                    Employee <input type="radio" name="employeeType" value="0"  onclick="setVisibility('');"/>
                    Vendor <input type="radio" name="employeeType" value="1" onclick="setVisibility('vendor');" checked="checked" /> 
                    Client <input type="radio" name="employeeType" value="2" onclick="setVisibility('client');"/> 
                    Sales <input type="radio" name="employeeType" value="3"  onclick="setVisibility('');"/>
                    Inventory <input type="radio" name="employeeType" value="4"  onclick="setVisibility('');" />
                    Sales Person  <input type="radio" name="employeeType" value="5"  onclick="setVisibility('');" />
<?php 
				}
				else if($data1['employeeType']==0)
				{
?>
                    Employee<input type="radio" name="employeeType" value="0"  onclick="setVisibility('');" checked="checked" />
                    Vendor  <input type="radio" name="employeeType" value="1" onclick="setVisibility('vendor');"/> 
                    Client <input type="radio" name="employeeType" value="2" onclick="setVisibility('client');"/> 
                    Sales <input type="radio" name="employeeType" value="3"  onclick="setVisibility('');"/>
                    Inventory <input type="radio" name="employeeType" value="4"  onclick="setVisibility('');" />
                    Sales Person  <input type="radio" name="employeeType" value="5"  onclick="setVisibility('');" />
<?php
				}
				else if($data1['employeeType']==2)
				{
?>
                    Employee<input type="radio" name="employeeType" value="0"  onclick="setVisibility('');" />
                    Vendor  <input type="radio" name="employeeType" value="1" onclick="setVisibility('vendor');"/> 
                    Client <input type="radio" name="employeeType" value="2" onclick="setVisibility('client');" checked="checked"/> 
                    Sales <input type="radio" name="employeeType" value="3"  onclick="setVisibility('');"/>
                    Inventory <input type="radio" name="employeeType" value="4"  onclick="setVisibility('');" />
                    Sales Person  <input type="radio" name="employeeType" value="5"  onclick="setVisibility('');" />
<?php
				}
				else if($data1['employeeType']==3)
				{
?>
                    Employee<input type="radio" name="employeeType" value="0"  onclick="setVisibility('');" />
                    Vendor  <input type="radio" name="employeeType" value="1" onclick="setVisibility('vendor');"/> 
                    Client <input type="radio" name="employeeType" value="2" onclick="setVisibility('client');" />
                    Sales <input type="radio" name="employeeType" value="3"  onclick="setVisibility('');" checked="checked"/> 
                    Inventory <input type="radio" name="employeeType" value="4"  onclick="setVisibility('');" />
                    Sales Person  <input type="radio" name="employeeType" value="5"  onclick="setVisibility('');" />  
<?php
				}
				else if($data1['employeeType']==4)
				{
?>
                    Employee<input type="radio" name="employeeType" value="0"  onclick="setVisibility('');" />
                    Vendor  <input type="radio" name="employeeType" value="1" onclick="setVisibility('vendor');"/> 
                    Client <input type="radio" name="employeeType" value="2" onclick="setVisibility('client');" />
                    Sales <input type="radio" name="employeeType" value="3"  onclick="setVisibility('');"/>
                    Inventory <input type="radio" name="employeeType" value="4"  onclick="setVisibility('');" checked="checked" />
                    Sales Person  <input type="radio" name="employeeType" value="5"  onclick="setVisibility('');" />  
 <?php                    
}else if($data1['employeeType']==5)
				{
?>
                    Employee<input type="radio" name="employeeType" value="0"  onclick="setVisibility('');" />
                    Vendor  <input type="radio" name="employeeType" value="1" onclick="setVisibility('vendor');"/> 
                    Client <input type="radio" name="employeeType" value="2" onclick="setVisibility('client');" />
                    Sales <input type="radio" name="employeeType" value="3"  onclick="setVisibility('');"/>
                    Inventory <input type="radio" name="employeeType" value="4"  onclick="setVisibility('');"/>
                    Sales Person  <input type="radio" name="employeeType" value="5"  onclick="setVisibility('');" checked="checked" />
<?php
				}
?>
                   </td>
              </tr>
              <tr id="vendor" style="display:none">
                <td align="right"><font face="arial" ></font><font face="arial"><b>Vendor Name </b></font></td>
                <td align="left"><select name="vendorName">
                   <?php
					for($i=0; $i <count($data_Vendr); $i++){
						if($data1['employee_type_id']==$data_Vendr[$i]['vendorID'])
						echo '<option value="'.$data_Vendr[$i]['vendorID'].'" selected="selected">'.$data_Vendr[$i]['vendorName'].'</option>';
						else
							echo '<option value="'.$data_Vendr[$i]['vendorID'].'">'.$data_Vendr[$i]['vendorName'].'</option>';
							}
					?> 
                </select> </td>
              </tr>
               <tr id="client" style="display:none">
                <td align="right"><font face="arial" color="red">*(r)</font><font face="arial"><b>Client Name </b></font></td>
                <td align="left"><select name="clinetname">
                   <?php
					for($i=0; $i <count($data_client); $i++){
						if($data1['employee_type_id']==$data_client[$i]['ID'])
							echo '<option value="'.$data_client[$i]['ID'].'" selected="selected">'.$data_client[$i]['client'].'</option>';
						else 
							echo '<option value="'.$data_client[$i]['ID'].'">'.$data_client[$i]['client'].'</option>';
							}
					?> 
                </select> </td>
              </tr>
<?php
	echo "<tr>";
	echo "<td width=\"40%\" align=\"right\"><font face=\"arial\"><b>First Name</b></font></td>";
	echo "<td align=\"left\"><input type=\"text\" name=\"firstname\" size=\"46\" value=\"".$data1['firstname']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\"><b>Last Name</b></font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"lastname\" SIZE=46 VALUE=\"".$data1['lastname']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	$title=str_replace("\"","&quot;",$data1['title']);
	echo "<td align=\"right\"><font face=\"arial\"><b>Title</b></font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"lastname\" SIZE=46 VALUE=\"".$title."\"></td>";
	echo "</tr>";
	echo "<tr>";
	$address=str_replace("\"","&quot;",$data1['address']);
	echo "<td align=\"right\"><font face=\"arial\"><b>Address</b></font></td>";
	echo "<td align=\"left\"><input type=\"text\" name=\"address\" size=30 value=\"".$address."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\"><b>City</b></font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"city\" SIZE=30 VALUE=\"".$data1['city']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\"><b>State</b></font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"state\" SIZE=3 VALUE=\"".$data1['state']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\"><b>Zip</b></font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"zip\" SIZE=10 VALUE=\"".$data1['zip']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\"><b>Phone</b></font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"phone\" SIZE=20 VALUE=\"".$data1['phone']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\"><b>Pager</b></font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"pager\" SIZE=46 VALUE=\"".$data1['pager']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\"><b>Alpha Pager</b></font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"alphapager\" SIZE=46 VALUE=\"".$data1['alphapager']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\"><b>Cellular</b></font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"cell\" SIZE=46 VALUE=\"".$data1['cell']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\"><b>Email</b></font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"email\" SIZE=46 VALUE=\"".$data1['email']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\"><b>Title</b></font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"title\" SIZE=46 VALUE=\"".$data1['title']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\"><b>Date Hired</b></font></td>";
	echo "<td align=\"left\"><select NAME=\"monthhired\">";
	echo "<option value=\"$monthhired\">$monthnamehired</option>";
	for($a=1; $a <= 12; $a++){
		$monthnum=date("m", mktime(1, 1, 1, $a, date("d"), date("Y")));
		$monthnam=date("F", mktime(1, 1, 1, $a, date("d"), date("Y")));
		echo "<option value=\"".$monthnum."\">$monthnam</option>";
	}
	echo "</select> / ";
	echo "<select name=\"dayhired\">";
	echo "<option value=\"".$dayhired."\">$dayhired</option>";
	for($b=1; $b <= 31; $b++){
		$daynum=date("d", mktime(1, 1, 1, date("m"), $b, date("Y")));
		echo "<option value=\"".$daynum."\">$daynum</option>";
	}
	echo "</select> / ";
	echo "<select name=\"yearhired\">";
	echo "<option value=\"".$yearhired."\">$yearhired</option>";
	for($b=0; $b <= 15; $b++){
		$yearnum=date("Y", mktime(1, 1, 1, date("m"), date("d"), date("Y")-$b));
		echo "<option value=\"".$yearnum."\">$yearnum</option>";
	}
	echo "</select>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\"><b>Salary</b></font></td>";
	echo "<td align=\"left\"><select name=\"salary\">";
	if($data1[$c]['salary'] == "No"){
		echo "<option value=\"".$data1[$c]['salary']."\" selected>No</option>";
		echo "<option value=\"Yes\">Yes</option>";
	}else{
		echo "<option value=\"Yes\" selected>Yes</option>";
		echo "<option value=\"No\">No</option>";
	}
	echo "</select>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\"><b>Wage</b></font></td>";
	echo "<td align=\"left\"><input type=\"text\" name=\"wage\" size=\"20\" value=\"".$data1['wage']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td  align=\"right\">
	<font face=\"arial\"><b>Username</b></font></td>";
	echo "<td align=\"left\"><input type=\"text\" name=\"usernamenew\" size=\"30\" value=\"".$data1['username']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\"><b>Password</b></font></td>";
	echo "<td align=\"left\"><input type=\"password\" name=\"passwordnew\" size=\"30\" value=\"".$data1['password']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\"><b>POP Password</b></font></td>";
	echo "<td align=\"left\"><input type=\"password\" name=\"poppassword\" size=\"30\" value=\"".$data1['poppassword']."\"></td>";
	echo "</tr>";
	echo "</table>";
echo "<input type=\"hidden\" name=\"employeeID\" value=\"".$data1['employeeID']."\">";
echo "<table width=\"80%\">";
echo "<tr>";
echo "<td colspan=\"5\" align=\"center\"><br><br><input type=\"Submit\" name=\"Edit Employees\" value=\"    Edit Employee   \"></td>";
echo "</tr>";
?>
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
<?php if($data1['employeeType']==1)
	echo "setVisibility('vendor');";
	else if($data1['employeeType']==2)
	echo "setVisibility('client');";
?>

</script>
<?php 
require('../trailer.php');
?>