<?php
require('Application.php');
echo "<html>";
echo "<head>";
require('../header.php');
$queryd=("SELECT * ".
		 "FROM \"employeeDB\" ".
		 "WHERE active = 'yes' ");
if(!($resultd=pg_query($connection,$queryd))){
	print("Failed queryd: " . pg_last_error($connection));
	exit;
}
while($rowd = pg_fetch_array($resultd)){
	$datad[]=$rowd;
}
echo "<font face=\"arial\">";
echo "<blockquote>";
echo "<center><font size=\"5\">Internal Directory</font>";
echo "<p>";
echo "<table border=\"0\" width=\"40%\">";
for($i=0; $i <= count($datad); $i++){
	echo "<tr>";
	echo "<td align=\"left\"><font face=\"arial\"><a href=record.php?employeeID=".$datad[$i]['employeeID'].">".$datad[$i]['firstname']." ".$datad[$i]['lastname']."</a></font></td>";
	echo "<td align=\"left\"><font face=\"arial\"><b>".$datad[$i]['title']."</b></font></td>";
	echo "</tr>";
}
echo "</table>";
require('../trailer.php');
?>
