<?php
require('Application.php');
require('../../header.php');
$ID=$_GET['ID'];
$query1=("SELECT w.\"ID\" as \"wID\", ".
		 "w.\"clientID\" as \"wclientID\", ".
		 "w.\"employeeID\" as \"wemployeeID\", ".
		 "w.\"itemid\" as \"witemid\", ".
		 "w.\"hours\" as \"whours\", ".
		 "w.\"total\" as \"wtotal\", ".
		 "w.\"dateassigned\" as \"wdateassigned\", ".
		 "w.\"completiondate\" as \"wcompletiondate\", ".
		 "w.\"jobnotes\" as \"wjobnotes\", ".
		 "w.\"open\" as \"wopen\", ".
		 "w.\"whoassigned\" as \"whoassigned\", ".
		 "c.\"ID\" as \"cID\", ".
		 "c.\"client\" as \"cclient\", ".
		 "e.\"employeeID\" as \"eemployeeID\", ".
		 "e.\"firstname\" as \"efirstname\", ".
		 "e.\"lastname\" as \"elastname\", ".
		 "b.\"ID\" as \"bitemid\", ".
		 "b.\"description\" as \"bdescription\", ".
		 "b.\"price\" as \"bprice\" ".
		 "FROM \"work1\" w, \"clientDB\" c, \"employeeDB\" e, \"billingcodes\" b ".
		 "WHERE w.\"ID\" = '$ID' AND w.\"employeeID\" = e.\"employeeID\" AND w.\"clientID\" = c.\"ID\" AND w.\"itemid\" = b.\"ID\"");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
echo "<font face=\"arial\">";
echo "<blockquote>";
echo "<font face=\"arial\" size=\"+2\"><b><center>Workorder for ".$data1[0]['cclient']."</center></b></font>";
echo "<p>";
echo "<form action=\"work.edit.php\" method=\"post\">";
echo "<table align=\"center\">";
echo "<tr>";
echo "<td>Client:</td>";
echo "<td><b>".$data1[0]['cclient']."</b></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Person Assigned to:</td>";
echo "<td><b>".$data1[0]['efirstname']." ".$data1[0]['elastname']."</b></td>";
echo "</tr>";
echo "<tr>";
echo "<td>".$data1[0]['efirstname']." has closed this ticket:</td>";
echo "<td><b>".$data1[0]['wopen']."</b></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Type of Work to be done:</td>";
echo "<td><b>".$data1[0]['bdescription']." - $".$data1[0]['bprice']."</b></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Estimated Number of Hours:</td>";
echo "<td><b>".$data1[0]['whours']."</b></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Date Assigned:</td>";
echo "<td><b>".$data1[0]['wdateassigned']."</b></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Completion Date:</td>";
echo "<td><b>".$data1[0]['wcompletiondate']."</b></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Job Notes:</td>";
echo "<td><textarea wrap=\"physical\" name=\"jobnotes\" rows=\"7\" cols=\"35\">".$data1[0]['wjobnotes']."</textarea></td>";
echo "</tr>";
echo "<tr>";
echo "<td><input type=\"hidden\" name=\"code\" value=\"".$data1[0]['wID']."\"></td>";
echo "<td><input type=\"submit\" name=\"submit\" value=\" Edit Workorder \"></td>";
echo "</tr>";
echo "</table>";
echo "</form>";
echo "<form action=\"work.bill.php\" method=\"post\">";
echo "<table align=\"center\">";
echo "<tr>";
echo "<td><input type=\"hidden\" name=\"code\" value=\"".$data1[0]['wID']."\"></td>";
echo "<td><input type=\"submit\" name=\"submit\" value=\" Bill & Close Workorder \"></td>";
echo "</tr>";
echo "</table>";
echo "</form>";
echo "<br><br>";
require('../../trailer.php');
?>
