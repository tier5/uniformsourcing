<?php
require('Application.php');
echo "<html>";
echo "<head>";
require('../header.php');
$empid=$_GET['employeeID'];
$queryd=("SELECT * ".
		 "FROM \"employeeDB\" ".
		 "WHERE \"employeeID\" = '$empid'");
if(!($resultd=pg_query($connection,$queryd))){
	print("Failed queryd: " . pg_last_error($connection));
	exit;
}
while($rowd = pg_fetch_array($resultd)){
	    $datad[]=$rowd;
}
echo "<center>";
echo "<p>";
if(count($datad) != 1){
	echo "There is a problem. There was more than 1 record returned for the employeeID $empid";
	exit;
}
echo "<b><font face=\"arial\">$compname Internam Directory - <font size=\"+1\">".$datad[0]['firstname']." ".$datad[0]['lastname']."</b></font></font>";
echo "<p>";
echo "<table border=\"0\">";
echo "<tr>";
echo "<td width=\"200\" valign=\"top\"><font face=\"arial\"><b>TITLE:</b></font></td>";
echo "<td><font face=\"arial\">".$datad[0]['title']."</font></td>";
echo "</tr>";
echo "<tr>";
echo "<td width=\"200\" valign=\"top\"><font face=\"arial\"><b>ADDRESS:</b></font></td>";
echo "<td><font face=\"arial\">".$datad[0]['address']."</font></td>";
echo "</tr>";
echo "<tr>";
echo "<td width=\"200\" valign=\"top\"><font face=\"arial\"><b>CITY:</b></font></td>";
echo "<td><font face=\"arial\">".$datad[0]['city']."</font></td>";
echo "</tr>";
echo "<tr>";
echo "<td width=\"200\" valign=\"top\"><font face=\"arial\"><b>STATE:</b></font></td>";
echo "<td><font face=\"arial\">".$datad[0]['state']."</font></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font face=\"arial\"><b>ZIP:</b></font></td>";
echo "<td><font face=\"arial\">".$datad[0]['zip']."</font></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font face=\"arial\"><b>PHONE:</b></font></td>";
echo "<td><font face=\"arial\">".$datad[0]['phone']."</font></td>";
echo "</tr>";
if($datad[0]['pager'] != "") {
	echo "<tr>";
	echo "<td><font face=\"arial\"><b>PAGER:</b></font></td>";
	echo "<td><font face=\"arial\">".$datad[0]['pager']."</font></td>";
	echo "</tr>";
}
if($datad[0]['alphapager'] != "") {
	echo "<tr>";
	echo "<td><font face=\"arial\"><b>ALPHA PAGER:</b><font></td>";
	// should be echo "<td><font face=\"arial\"><a href=\"mailto:".$datad[0]['AlphaPager']."@alphapage.airtouch.com\">".$datad[0]['AlphaPager']."</a></font></td>";
	echo "<td><font face=\"arial\">".$datad[0]['alphapager']."</font></td>";
	echo "</tr>";
}
if($datad[0]['cell'] != "") {
	echo "<tr>";
	echo "<td><font face=\"arial\"><b>CELL PHONE:</b><font></td>";
	echo "<td><font face=\"arial\">".$datad[0]['cell']."</font></td>";
	echo "</tr>";
}
echo "<tr>";
echo "<td><font face=\"arial\"><b>EMAIL:</b></font></td>";
echo "<td><font face=\"arial\"><a href=mailto:".$datad[0]['email'].">".$datad[0]['email']."</a></font></td>";
echo "</tr>";
echo "</table>";
require('../trailer.php');
?>
