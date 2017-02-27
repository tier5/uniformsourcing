<?php
require('Application.php');
require('../../header.php');
$client=$_POST['client'];
if($debug == 'on'){
	echo "client IS $client<br>";
}
$query1=("SELECT * ".
		 "FROM \"invoices\" ".
		 "WHERE \"client\" = '$client'");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
$query2=("SELECT \"client\" ".
		 "FROM \"clientDB\" ".
		 "WHERE \"ID\" = '$client'");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2)){
	$data2[]=$row2;
}
$color1="ffffff";
$color2="cccccc";
echo "<center>";
echo "<font face=\"verdana\" size=\"+2\" color=\"d6a500\"><b>".$data2[0]['client']."</b></font>";
echo "<p>";
echo "<table width=\"50%\">";
echo "<tr bgcolor=\"4a526b\">";
echo "<td><b><font face=\"arial\" size=\"-1\" color=\"ffffff\">Invoice Number</font></b></td>";
echo "<td><b><font face=\"arial\" size=\"-1\" color=\"ffffff\">PO</font></b></td>";
echo "<td><b><font face=\"arial\" size=\"-1\" color=\"ffffff\">Invoice Date</font></b></td>";
echo "</tr>";
echo "<form action=\"archived.invoice.php\" method=\"post\">";
for($i=0; $i < count($data1); $i++){
	echo "<input type=\"hidden\" name=\"invoice\" value=\"".$data1[$i]['invoice']."\">";
	if(bcmod("$i", "2") == 1){
		$color1="ffffff";
	}else{
		$color1="cccccc";
	}
	echo "<tr bgcolor=\"$color1\">";
	echo "<td><font face=\"arial\" size=\"-1\"><a href=\"archived.invoice.php?invoice=".$data1[$i]['invoice']."\">".$data1[$i]['invoice']."</a></font></td>";
	echo "<td><font face=\"arial\" size=\"-1\">".$data1[$i]['customerPO']."</font></td>";
	echo "<td><font face=\"arial\" size=\"-1\">".date("m/d/Y", $data1[$i]['invoicedate'])."</font></td>";
	echo "</tr>";
}
echo "</table>";
echo "</center>";
require('../../trailer.php');
?>
