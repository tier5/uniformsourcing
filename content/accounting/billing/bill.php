<?php
require('Application.php');
require('../../header.php');
for($i=0; $i < count($_POST['bill']); $i++){
	$bill[$i]=$_POST['bill'][$i];
	if($i < (count($_POST['bill']) - 1)){
		$prices.="".$bill[$i]." ";
	}elseif($i == (count($_POST['bill']) - 1)){
		$prices.="".$bill[$i]."";
	}
}
$cID=$_POST['cID'];
$po=$_POST['po'];
if($debug == "on"){
	echo "count bill IS ".count($bill)."<br>";
	echo "cID IS $cID<br>";
	echo "po IS $po<br>";
	for($i=0; $i < count($bill); $i++){
		echo "bill $i IS ".$bill[$i]."<br>";
	}
}
$query1=("SELECT * ".
		 "FROM \"clientDB\" ".
		 "WHERE \"ID\" = '$cID'");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
$query2=("SELECT * ".
		 "FROM \"clientDB\" ".
		 "WHERE \"ID\" = '1'");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2)){
	$data2[]=$row2;
}
$query3=("SELECT MAX(\"ID\") AS \"maxid\" ".
		 "FROM \"invoices\"");
if(!($result3=pg_query($connection,$query3))){
	print("Failed query3: " . pg_last_error($connection));
	exit;
}
while($row3 = pg_fetch_array($result3)){
	$data3[]=$row3;
}
$color1="ffffff";
$color2="e7e7e7";
$invnum=$data3[0]['maxid'] + 10000;
echo "<center>";
echo "<table width=\"100%\">";
echo "<tr>";
echo "<td align=\"left\">";
echo "<br>";
echo "<font face=\"arial\" size=\"-1\">".$data2[0]['client']."<br>";
echo $data2[0]['address']."<br>";
echo $data2[0]['city'].",".$data2[0]['state']." ".$data2[0]['zip'];
echo "<p>";
echo "<b>Voice:</b> &nbsp;&nbsp;&nbsp;".$data2[0]['phone']."<br>";
echo "<b>Fax:</b>     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$data2[0]['fax']."";
echo "</font>";
echo "<p>";
echo "<font face=\"arial\" size=\"-1\">";
echo "<b>Sold To:</b>";
echo "<blockquote>";
echo $data1[0]['client']."<br>";
echo $data1[0]['address']."<br>";
echo $data1[0]['city'].",".$data1[0]['state']." ".$data1[0]['zip']."";
echo "</blockquote></font>";
echo "</td>";
echo "<td align=\"right\" valign=\"top\">";
echo "<font face=\"verdana\" size=\"+2\"><b>INVOICE</b></font><br>";
echo "<font face=\"arial\" size=\"-1\">";
echo "<b>Invoice Number:</b><br>";
echo $invnum."<br>";
echo "<b>Invoice Date:</b><br>";
echo date("M d, Y");
echo "</font>";
echo "</td>";
echo "</tr>";
echo "</table>";
echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\">";
echo "<tr align=\"center\" bgcolor=\"c0c0c0\">";
echo "<td><font face=\"arial\" size=\"-1\"><b>Customer ID</b></font></td>";
echo "<td><font face=\"arial\" size=\"-1\"><b>Customer PO</b></font></td>";
echo "<td><font face=\"arial\" size=\"-1\"><b>Payment Terms</b></font></td>";
echo "<td><font face=\"arial\" size=\"-1\"><b>Due Date</b></font></td>";
echo "</tr>";
echo "<tr align=\"center\">";
echo "<td><font face=\"arial\" size=\"-1\">".$data1[0]['clientID']."</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">".$po."</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">Next 7 Days</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">".date("m-d-Y", mktime(1, 1, 1, date("m"), date("d")+7, date("Y")))."</font></td>";
echo "</tr>";
echo "<table>";
$counter=1;
echo "<table width=\"100%\" border=\"0\">";
echo "<tr bgcolor=\"c0c0c0\" align=\"center\">";
echo "<td><font face=\"arial\" size=\"-1\"><b>Hours</b></font></td>";
echo "<td><font face=\"arial\" size=\"-1\"><b>Item</b></font></td>";
echo "<td><font face=\"arial\" size=\"-1\"><b>Description</b></font></td>";
echo "<td><font face=\"arial\" size=\"-1\"><b>Unit Price</b></font></td>";
echo "<td><font face=\"arial\" size=\"-1\"><b>Cost</b></font></td>";
echo "</tr>";
for($i=0; $i < count($bill); $i++){
	$query4=("SELECT DISTINCT \"timelog\".*, \"billingcodes\".\"price\", \"billingcodes\".\"itemid\" as \"bitemid\" ".
			 "FROM \"timelog\", \"billingcodes\" ".
			 "WHERE \"timelog\".\"ID\" = '".$bill[$i]."' AND \"billingcodes\".\"ID\" = \"timelog\".\"workid\"");
	if(!($result4=pg_query($connection,$query4))){
		print("Failed query4: " . pg_last_error($connection));
		exit;
	}
	while($row4 = pg_fetch_array($result4)){
		$data4[]=$row4;
	}
	$whooper2=$data4[0]['total'] / 60;
	echo "<tr bgcolor=\"$color1\">";
	echo "<td align=\"center\"><font face=\"arial\" size=\"-1\">$whooper2</font></td>";
	echo "<td align=\"center\"><font face=\"arial\" size=\"-1\">".$data4[0]['bitemid']."</font></td>";
	echo "<td><font face=\"arial\" size=\"-1\">".$data4[0]['description']."</font></td>";
	echo "<td align=\"right\"><font face=\"arial\" size=\"-1\">$".$data4[0]['price']." /hour</font></td>";
	echo "<td align=\"right\"><font face=\"arial\" size=\"-1\">$".$data4[0]['cost']."</font></td>";
	echo "</tr>";
	$totalcost= bcadd($data4[0]['cost'] , $totalcost, 2);
	unset($data4);
}
echo "</table>";
echo "<p>";
//$query5 HERE martin
echo "<table width=\"100%\">";
echo "<tr>";
echo "<td align=\"right\">";
echo "<table width=\"300\" border=\"0\">";
echo "<tr>";
echo "<td align=\"right\"><b><font face=\"arial\" size=\"-1\">Subtotal</font></b></td>";
echo "<td align=\"right\"><font face=\"arial\" size=\"-1\">$".$totalcost."</font></td>";
echo "</tr>";
echo "<tr>";
echo "<td align=\"right\"><b><font face=\"arial\" size=\"-1\">Sales Tax</font></b></td>";
echo "<td align=\"right\"</td>";
echo "</tr>";
echo "<tr>";
echo "<td align=\"right\"><b><font face=\"arial\" size=\"-1\">Total Invoice Ammount</font></b></td>";
echo "<td align=\"right\"><font face=\"arial\" size=\"-1\">$".$totalcost."</font></td>";
echo "</tr>";
echo "<tr>";
echo "<td align=\"right\"><b><font face=\"arial\" size=\"-1\">Payment Recieved</font></b></td>";
echo "<td align=\"right\"><font face=\"arial\" size=\"-1\">0.00</font></td>";
echo "</tr>";
echo "<tr>";
echo "<td align=\"right\"><b><font face=\"arial\" size=\"-1\">TOTAL</font></b></td>";
echo "<td align=\"right\"><font face=\"arial\" size=\"-1\">$".$totalcost."</font></td>";
echo "</tr>";
echo "</table>";
echo "</td>";
echo "</tr>";
echo "</table>";
echo "<p>";
echo "<font face=\"arial\"><i>All past due accounts subject to finance charges.</i></font>";
for($i=0; $i < count($bill); $i++){
	$query9=("SELECT \"ID\", \"billed\" ".
			 "FROM \"timelog\" ".
			 "WHERE \"ID\" = '".$bill[$i]."'");
	if(!($result9=pg_query($connection,$query9))){
		print("Failed query9: " . pg_last_error($connection));
		exit;
	}
	while($row9 = pg_fetch_array($result9)){
		$data9[]=$row9;
	}
	if($data9[0]['billed'] == 'yes'){
		$checker=1;
	}elseif($data9[0]['billed'] = 'no'){
		$query8=("UPDATE \"timelog\" ".
				 "SET ".
				 "\"billed\" = 'yes' ".
				 "WHERE \"ID\" = '".$bill[$i]."'");
		if(!($result8=pg_query($connection,$query8))){
			print("Failed query8: " . pg_last_error($connection));
			exit;
		}
		$checker=0;
	}
}
if($checker < 1){
	$query6=("SELECT MAX(\"ID\") AS \"max\" ".
			 "FROM \"invoices\" ");
	if(!($result6=pg_query($connection,$query6))){
		print("Failed query6: " . pg_last_error($connection));
		exit;
	}
	while($row6 = pg_fetch_array($result6)){
		$data6[]=$row6;
	}
	$max2=$data6[0]['max'] + 10000;
	$query7=("INSERT INTO \"invoices\" ".
			 "(\"invoice\", \"client\", \"invoicedate\", \"customerPO\", \"timelogs\", \"total\") ".
			 "VALUES ('$max2', '".$data1[0]['ID']."', '".mktime(0, 0, 0, date("m"), date("d"), date("Y"))."', '".$po."', '$prices', '$totalcost')");
	if(!($result7=pg_query($connection,$query7))){
		print("Failed query7: " . pg_last_error($connection));
		exit;
	}
}elseif($checker > 0){
	echo "<p>";
	echo "<font face=\"arial\" size=\"+1\" color=\"FF0000\">";
	echo "THIS INVOICE HAS ALREADY BEEN CREATED";
	echo "</font>";
	echo "</BODY>";
}
require('../../trailer.php');
?>
