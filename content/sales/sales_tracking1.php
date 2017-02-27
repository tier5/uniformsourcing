<?php
require('Application.php');
require('../header.php');
$code=$_POST['code'];
if($debug == 'on'){
	echo "code IS $code<br>";
}
$query1=("SELECT * ".
		 "FROM \"sales\" ".
		 "WHERE \"ID\" = '$code'");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
$query2=("SELECT * ".
		 "FROM \"clientDB\" ".
		 "WHERE \"ID\" = '".$data1[0]['cid']."'");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2)){
	$data2[]=$row2;
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
echo "</tr>";
echo "<tr>";
echo "<td><div align=\"center\"><a href=\"../production/clients/clienthomepage.php?ID=".$data2[0]['ID']."\">".$data2[0]['client']."</a></div></td>";
echo "<td><div align=\"center\">".$data1[0]['po']."</div></td>";
echo "<td><div align=\"center\">".date("m/d/Y", $data1[0]['order_date'])."</div></td>";
echo "<td><div align=\"center\">".date("m/d/Y", $data1[0]['due_date'])."</div></td>";
echo "<td><div align=\"center\">".$data1[0]['sales_id']."</div></td>";
echo "<td><div align=\"center\">".$data1[0]['units']."</div></td>";
echo "<td><div align=\"center\">".$data1[0]['delivery']."</div></td>";
echo "<td><div align=\"center\">".$data1[0]['billed']."</div></td>";
echo "<td><div align=\"center\">".$data1[0]['paid']."</div></td>";
echo "</tr>";
echo "</table>";
echo "<hr>";
echo "<div align=\"center\"><h3>Notes for this order</h3></div>";
echo $data1[0]['notes'];
require('../trailer.php');
?>
