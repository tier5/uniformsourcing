<?php
require('Application.php');
$querya=("SELECT * ".
		 "FROM \"vendor\" ".
		 "WHERE \"active\" = 'yes' ").
		" ORDER BY \"vendorName\" ASC";
if(!($resulta=pg_query($connection,$querya))){
	print("Failed querya: " . pg_last_error($connection));
	exit;
}
while($rowa = pg_fetch_array($resulta)){
	$dataa[]=$rowa;
}
//$_SESSION['vendorID']=$dataa['vendorID'];
require('../header.php');?>
<center>
<form action="Vendredit.php" method="get" onsubmit="javascript: if(document.getElementById('vendorID').options[document.getElementById('vendorID').selectedIndex].value==0) return false;" >
<table>
<tr>
<td width="110px" class="grid001">Vendor: </td>
    <td class="grid001">
   
    <select id="vendorID" class="vid" name="vendorID" style="width:200px;">
                          <option value="0">---- Select Vendor ----</option>
                           <?php 
						  	for($i=0; $i<count($dataa); $i++)
							{
						  ?>
                          <option value="<?php echo $dataa[$i]['vendorID'];?>"><?php echo $dataa[$i]['vendorName'];?></option>
                          <?php 
							}
						  ?>    
                        </select>
                        </td> <td class="grid001">
	<input type="Submit" value="Edit Vendor" ></td>
    </tr>
                         
                </table></form>
                </center>
<form action="vendrDeactivatd.php" method="post">
<table align="center">
<tr>
	<td >INTERNAL DIRECTORY ADMINISTRATION</td>
</tr>
<tr>
	<td align=center>FUNCTION</td>
	<td>&nbsp;</td><td>&nbsp;</td>

<td align=center>NAME</td>
</tr>
<?php 
	for($i=0; $i < count($dataa); $i++){?>
	<tr>
	<td bgcolor=C0C0C0>
	<a href="Vendredit.php?vendorID='<?php echo $dataa[$i]['vendorID'];?>'">
	<font face=\"arial\" size=\"-2\">EDIT</font></a><br>
	<a href="Vendrdelete.php?vendorID='<?php echo $dataa[$i]['vendorID'];?>'">
	<font face=\"arial\" size=\"-2\">DEACTIVATE</font></a>
	</td>
	<td>&nbsp;</td><td>&nbsp;</td>
  <?php echo "<td bgcolor=C0C0C0>".$dataa[$i]['vendorName']." </td>";
	
	echo "</tr>";
}
?></table>
<input name="submit" type="submit" value="View Deactivated Vendors"  />
</form>
<?php
require('../trailer.php');
?>