<?php
require('Application.php');
require('../../header.php');
$query1=("SELECT DISTINCT \"clientDB\".\"client\", \"clientDB\".\"clientID\", \"clientDB\".\"ID\" ".
		 "FROM \"clientDB\", \"timelog\" ".
		 "WHERE \"timelog\".\"client\" = \"clientDB\".\"ID\" AND \"timelog\".\"billed\" = 'no' ".
		 "ORDER BY \"clientDB\".\"client\" ASC ");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
echo "<center>";
echo "<table>";
echo "<tr>";
echo "<td><font face=\"arial\" size=\"+1\"><b>Choose Client</b></font></td>";
echo "</tr>";
for($i=0;$i < count($data1); $i++){
	echo "<tr>";
	echo "<td><font face=\"arial\" size=\"-1\"><a href=\"workorderlist.php?cID=".$data1[$i]['ID']."\">".$data1[$i]['client']."</a></font></td>";
	echo "</tr>";
}
echo "</table>";
echo "</center>";
require('../../trailer.php');
?>
