<?php
require('Application.php');
require('../header.php');
$vendorID=$_GET['vendorID'];
$_SESSION['vendorID']=$vendorID;//echo $vendorID."</br>";

$query1=("SELECT * ".
		 "FROM \"vendor\" ".
		 "WHERE \"vendorID\"= $vendorID ");
//echo $query1;	 	 
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1=pg_fetch_array($result1)){
	$data1=$row1;
}
echo "<form action=\"Vendredit1.php\" method=\"post\">";
echo "<table width=\"70%\">";
echo "<tr bgcolor=C0C0C0>";
echo "<td colspan=\"2\" align=\"center\"><font face=\"arial\"><b>Edit Vendor Record</b></font></td>";
echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\">Vendor Name</font></td>";
	echo "<td align=\"left\"><input type=\"text\" name=\"vendorName\" size=\"46\" value=\"".$data1['vendorName']."\"></td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\">Address</font></td>";
	echo "<td align=\"left\"><input type=\"text\" name=\"address\" size=30 value=\"".$data1['address']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\">City</font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"city\" SIZE=30 VALUE=\"".$data1['city']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\">State</font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"state\" SIZE=3 VALUE=\"".$data1['state']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\">Zip</font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"zip\" SIZE=10 VALUE=\"".$data1['zip']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\">Country</font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"country\" SIZE=10 VALUE=\"".$data1['country']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\">Account NUmber</font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"account\" SIZE=10 VALUE=\"".$data1['accountNumber']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\">Phone</font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"phone\" SIZE=20 VALUE=\"".$data1['phone']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\">Fax</font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"pager\" SIZE=46 VALUE=\"".$data1['pager']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\">Skype Account</font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"alphapager\" SIZE=46 VALUE=\"".$data1['alphapager']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\">Cellular</font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"cell\" SIZE=46 VALUE=\"".$data1['cell']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\">Email</font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"email\" SIZE=46 VALUE=\"".$data1['email']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\">Username</font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"newusername\" SIZE=46 VALUE=\"".$data1['username']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\">Password</font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"newpassword\" SIZE=46 VALUE=\"".$data1['password']."\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\">Url</font></td>";
	echo "<td align=\"left\"><INPUT TYPE=\"text\" NAME=\"www\" SIZE=46 VALUE=\"".$data1['www']."\"></td>";
	echo "</tr>";	
	echo "<tr>";
	echo "<td align=\"right\"><font face=\"arial\">Notes</font></td>";
	echo "<td align=\"left\"><textarea wrap=\"physical\" name=\"notes\" rows=\"7\" cols=\"35\">".$data1['notes']."</textarea></td>";
	echo "</tr>";	
	echo "</table>";

echo "<input type=\"hidden\" name=\"vendorID\" value=\"".$data1['vendorID']."\">";
echo "<table width=\"80%\">";
echo "<tr>";
echo "<td colspan=\"5\" align=\"center\"><br><br><input type=\"Submit\" name=\"EditVendors\" value=\"    Edit Vendors   \"></td>";
echo "</tr>";
echo "</table>";
echo "</form>";
require('../trailer.php');
?>
