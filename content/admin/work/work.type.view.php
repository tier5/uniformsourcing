<?php
require('Application.php');
require('../../header.php');
$query1=("SELECT * ".
		 "FROM \"billingcodes\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER BY \"description\" ASC");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
echo "<font face=\"arial\">";
echo "<blockquote>";
echo "<center><font size=\"5\">Type and Global Pricing</font></center>";
echo "<p>";
echo "<center>";
echo "<table>";
echo "<tr>";
echo "<td align=\"center\"><b><font face=\"arial\" size=\"-1\">FUNCTION</font></b></td>";
echo "<td align=\"center\"><b><font face=\"arial\" size=\"-1\">ITEMID</font></b></td>";
echo "<td align=\"center\"><b><font face=\"arial\" size=\"-1\">DESCRIPTION</font></b></td>";
echo "<td align=\"center\"><b><font face=\"arial\" size=\"-1\">PRICE</font></b></td>";
echo "</tr>";
for($i=0; $i < count($data1); $i++){
	echo "<tr>";
	echo "<td bgcolor=c0c0c0>";
	echo "<a href=\"work.type.edit0.php?ID=".$data1[$i]['ID']."\">";
	echo "<font face=\"arial\" size=\"-2\">EDIT</font></a><br>";
	echo "<a href=\"work.type.delete.php?ID=".$data1[$i]['ID']."\">";
	echo "<font face=\"arial\" size=\"-2\">DEACTIVATE</font></a>";
	echo "</td>";
	echo "<td bgcolor=c0c0c0><font face=\"arial\" size=\"-1\">".$data1[$i]['itemid']."</font></td>";
	echo "<td bgcolor=c0c0c0><font face=\"arial\" size=\"-1\">".$data1[$i]['description']."</font></td>";
	echo "<td bgcolor=c0c0c0><font face=\"arial\" size=\"-1\">".$data1[$i]['price']."</font></td>";
	echo "</tr>";
}
echo "</table>";
echo "<form action=\"work.type.edit2.php\" method=\"post\">";
echo "<input type=\"submit\" name=\"submit\" value=\" View DeActivated Pricing \">";
echo "</form>";
require('../../trailer.php');
?>
