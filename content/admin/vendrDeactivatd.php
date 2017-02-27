<?php
require('Application.php');
$query=("SELECT * ".
		 "FROM \"vendor\" ".
		 "WHERE \"active\" = 'no'");
if(!($result=pg_query($connection,$query))){
	print("Failed querya: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data[]=$row;
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
echo "</tr>";
for($i=0; $i < count($data); $i++){
	echo "<tr>";
	echo "<td bgcolor=C0C0C0>";
	echo "<a href=\"Vendredit.php?vendorID=".$data[$i]['vendorID']."\">";
	echo "<font face=\"arial\" size=\"-2\">EDIT</font></a><br>";
	echo "</td>";
	echo "<td bgcolor=C0C0C0><font face=\"arial\" size=\"-1\">".$data[$i]['vendorName']."</font></td>";
	echo "</tr>";
}
echo "</table>";
require('../trailer.php');
?>
