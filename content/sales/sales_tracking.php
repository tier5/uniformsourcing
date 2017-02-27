<?php
require('Application.php');
require('../header.php');
$query1=("SELECT \"clientDB\".\"clientID\" as \"cclientID\", ".
		 "\"clientDB\".\"ID\" as \"cID\", ".
		 "\"clientDB\".\"client\" as \"cclient\", ".
		 "\"sales\".\"id\" as \"sID\", ".
		 "\"sales\".\"cid\" as \"scID\", ".
		 "\"sales\".\"po\" as \"spo\", ".
		 "\"sales\".\"order_date\" as \"sorder_date\", ".
		 "\"sales\".\"due_date\" as \"sdue_date\", ".
		 "\"sales\".\"sales_id\" as \"ssales_id\", ".
		 "\"sales\".\"units\" as \"sunits\", ".
		 "\"sales\".\"delivery\" as \"sdelivery\", ".
		 "\"sales\".\"billed\" as \"sbilled\", ".
		 "\"sales\".\"paid\" as \"spaid\" ".
		 "FROM \"clientDB\", \"sales\" ".
		 "WHERE \"clientDB\".\"ID\" = \"sales\".\"cid\" ".
		 "ORDER BY \"clientDB\".\"client\" ASC ");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
echo "<font face=\"arial\">";
echo "<table border=\"1\" align=\"center\">";
echo "<tr>";
echo "<th>Client</th>";
echo "<th>PO</th>";
echo "<th>Order Date</th>";
echo "<th>Due Date</th>";
echo "<th>Sales ID</th>";
echo "<th>Units</th>";
echo "<th>Delivery</th>";
echo "<th>Billed</th>";
echo "<th>Paid</th>";
echo "<th>Notes</th>";
echo "</tr>";
for($i=0; $i < count($data1); $i++){
	if(bcmod("$i", "2") == 1){
		$color="cccccc";
	}else{
		$color1="ffffff";
	}
	echo "<tr bgcolor=\"$color1\">";
	echo "<td><dov align=\"center\"><a href=\"../production/clients/clienthomepage.php?ID=".$data1[$i]['cID']."\">".$data1[$i]['cclient']."</div></td>";
	echo "<td><div align=\"center\">".$data1[$i]['spo']."</div></td>";
	echo "<td><div align=\"center\">".date("m/d/Y", $data1[$i]['sorder_date'])."</div></td>";
	echo "<td><div align=\"center\">".date("m/d/Y", $data1[$i]['sdue_date'])."</div></td>";
	echo "<td><div align=\"center\">".$data1[$i]['ssales_id']."</div></td>";
	echo "<td><div align-\"center\">".$data1[$i]['sunits']."</div></td>";
	echo "<td><div align=\"center\">".$data1[$i]['sdelivery']."</div></td>";
	echo "<td><div align=\"center\">".$data1[$i]['sbilled']."</div></td>";
	echo "<td><div align=\"center\">".$data1[$i]['spaid']."</div></td>";
	echo "<td><form name-\"ID\" method=\"post\" action=\"sales_tracking1.php\">";
	echo "<input type=\"hidden\" name=\"code\" value=\"".$data1[$i]['sID']."\">";
	echo "<div align=\"center\"><input type=\"submit\" name=\"submit\" value=\"More Info\"></div></form></td>";
	echo "</tr>";
}
echo "</table>";
require('../trailer.php');
?>
