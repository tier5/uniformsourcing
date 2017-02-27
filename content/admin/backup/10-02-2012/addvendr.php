<?php
require('Application.php');
require('../header.php');



$sql='select (Max("vendorID")+1) as "vendorID" from vendor ';
if(!($result_cnt=pg_query($connection,$sql))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row_cnt = pg_fetch_array($result_cnt)){
	$data_cnt=$row_cnt;
}
if(! $data_cnt['vendorID']) { $data_cnt['vendorID']=1; }

echo "<font face=\"arial\">";
echo "<blockquote>";
echo "<center><font size=\"5\">$compname Directory Administration</font></center>";
echo "<p>";
echo "</blockquote>";
echo "<center>";
echo "<table>";
echo "<tr>";
echo "<td colspan=2><font face=\"arial\"><b>Enter New Vendor</b></font></td>";
echo "</tr>";
echo "<form action=\"newVendr.php\" method=POST>";
echo "<tr>";
echo "<td align=\"right\"><font face=\"arial\" color=\"red\">*(r)</font><font face=\"arial\"><b>Vendor Name</b></font></td>";
echo "<td align=\"left\"><input type=\"text\" name=\"vendorName\" size=\"20\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td align=\"right\"><font face=\"arial\" color=\"red\">*(r)</font><font face=\"arial\"><b>Address</b></font></td>";
echo "<td align=\"left\"><input type=\"text\" name=\"addressnew\" size=\"30\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td align=\"right\"><font face=\"arial\" color=\"red\">*(r)</font><font face=\"arial\"><b>City</b></font></td>";
echo "<td align=\"left\"><input type=\"text\" name=\"citynew\" size=\"30\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td align=\"right\"><font face=\"arial\" color=\"red\">*(r)</font><font face=\"arial\"><b>State</b><font></td>";
echo "<td align=\"left\"><input type=\"text\" name=\"statenew\" size=\"3\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td align=\"right\"><font face=\"arial\" color=\"red\">*(r)</font><font face=\"arial\"><b>Zip</b></font></td>";
echo "<td align=\"left\"><input type=\"text\" name=\"zipnew\" size=\"10\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td align=\"right\"><font face=\"arial\" color=\"red\">*(r)</font><font face=\"arial\"><b>Country</b></font></td>";
echo "<td align=\"left\"><input type=\"text\" name=\"country\" size=\"10\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td align=\"right\"><font face=\"arial\" color=\"red\">*(r)</font><font face=\"arial\"><b>Account Number</b></font></td>";
echo "<td align=\"left\"><input type=\"text\" name=\"account\" size=\"20\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td align=\"right\"><font face=\"arial\" color=\"red\">*(r)</font><font face=\"arial\"><b>Phone</b></font></td>";
echo "<td align=\"left\"><input type=\"text\" name=\"phonenew\" size=\"20\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td align=\"right\"><font face=\"arial\"><b>Fax</b></font></td>";
echo "<td align=\"left\"><input type=\"text\" name=\"pagernew\" size=\"20\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td align=\"right\"><font face=\"arial\"><b>Skype Account</b></font></td>";
echo "<td align=\"left\"><input type=\"text\" name=\"alphapagernew\" size=\"20\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td align=\"right\"><font face=\"arial\" color=\"red\">*(r)</font><font face=\"arial\"><b>Cellular</b></font></td>";
echo "<td align=\"left\"><input type=\"text\" name=\"cellnew\" size=\"20\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td align=\"right\"><font face=\"arial\" color=\"red\">*(r)</font><font face=\"arial\"><b>Email</b></font></td>";
echo "<td align=\"left\"><input type=\"text\" name=\"emailnew\" size=\"20\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td align=\"right\"><font face=\"arial\" color=\"red\">*(r)</font><font face=\"arial\"><b>Username</b></font></td>";
echo "<td align=\"left\"><input type=\"text\" name=\"newusername\" size=\"20\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td align=\"right\"><font face=\"arial\" color=\"red\">*(r)</font><font face=\"arial\"><b>Password</b></font></td>";
echo "<td align=\"left\"><input type=\"text\" name=\"newpassword\" size=\"20\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td align=\"right\"><b>Url:</b></td>";
echo "<td align=\"left\"><input type=\"text\" name=\"www\" value=\"http://\" size=\"30\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td align=\"right\"><b>Notes:</b></td>";
echo "<td align=\"left\"><textarea wrap=\"physical\" name=\"notes\" rows=\"7\" cols=\"35\"></textarea></td>";
echo "</tr>";

echo "</table>";
echo "<table width=\"80%\">";
echo "<tr>";
echo "<td colspan=5 align=\"center\">";
echo "<br><br>";
echo "<input type=\"Submit\" value=\" Enter New Vendor  \"></td>";
echo "</tr>";
echo "</table>";
echo "</form>";
require('../trailer.php');
?>