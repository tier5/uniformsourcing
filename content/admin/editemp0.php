<?php
require('Application.php');
$querya=("SELECT * ".
		 "FROM \"employeeDB\" ".
		 "WHERE \"active\" = 'yes'");
if(!($resulta=pg_query($connection,$querya))){
	print("Failed querya: " . pg_last_error($connection));
	exit;
}
while($rowa = pg_fetch_array($resulta)){
	$dataa[]=$rowa;
}
require('../header.php');
echo "<font face=\"arial\">";
echo "<blockquote>";
echo "<center><font size=\"5\">INTERNAL DIRECTORY ADMINISTRATION</font></center>";
echo "<p>";
echo "<center>";
echo "<table>";
echo "<tr>";
echo "<td align=\"center\"><b><font face=\"arial\" size=\"-1\">FUNCTION</font></b></td>";
echo "<td align=\"center\"><b><font face=\"arial\" size=\"-1\">NAME</font></b></td>";
echo "<td align=\"center\"><b><font face=\"arial\" size=\"-1\">TITLE</font></b></td>";
echo "</tr>";
for($i=0; $i < count($dataa); $i++){
	echo "<tr>";
	echo "<td bgcolor=C0C0C0>";
	echo "<a href=\"edit.php?employeeID=".$dataa[$i]['employeeID']."\">";
	echo "<font face=\"arial\" size=\"-2\">EDIT</font></a><br>";
	echo "<a href=\"delete.php?employeeID=".$dataa[$i]['employeeID']."\">";
	echo "<font face=\"arial\" size=\"-2\">DEACTIVATE</font></a>";
	echo "</td>";
	echo "<td bgcolor=C0C0C0><font face=\"arial\" size=\"-1\">".$dataa[$i]['firstname']." ".$dataa[$i]['lastname']."</font></td>";
	echo "<td bgcolor=C0C0C0><font face=\"arial\" size=\"-1\">".$dataa[$i]['title']."</font></td>";
	echo "</tr>";
}
echo "</table>";
echo "<form action=\"editemp2.php\" method=\"post\">";
echo "<input type=\"submit\" name=\"submit\" value=\" View DeActivated Employees \">";
echo "</form>";
require('../trailer.php');
?>
