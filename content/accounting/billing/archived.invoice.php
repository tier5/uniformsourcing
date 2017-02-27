<?php
require('Application.php');
require('../../header.php');
$invoice=$_GET['invoice'];
if($debug == 'on'){
	echo "invoice IS $invoice<br>";
}
$query1=("SELECT * ".
		 "FROM \"invoices\" ".
		 "WHERE \"invoice\" = '$invoice'");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
if($debug == 'on'){
	echo "data1 0 client IS ".$data1[0]['client']."<br>";
}
$query2=("SELECT * ".
		 "FROM \"clientDB\" ".
		 "WHERE \"ID\" = '".$data1[0]['client']."'");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2)){
	$data2[]=$row2;
}
$query3=("SELECT * ".
		 "FROM \"clientDB\" ".
		 "WHERE \"ID\" = '1' ");
if(!($result3=pg_query($connection,$query3))){
	print("Failed query3: " . pg_last_error($connection));
	exit;
}
while($row3 = pg_fetch_array($result3)){
	$data3[]=$row3;
}
$bills=explode(" ", $data1[0]['timelogs']);
if($debug == 'on'){
	echo "\"".$data1[0]['timelogs']."\"<br>";
	for($i=0; $i < count($bills); $i++){
		echo "bills $i IS $bills[$i] <br>";
	}
}
echo "<center>";
echo "<table width=\"100%\">";
echo "<tr>";
echo "<td align=\"left\">";
echo "<br>";
echo "<font face=\"arial\" size=\"-1\">";
echo $data3[0]['client']."<br>";
echo $data3[0]['address']."<br>";
echo $data3[0]['city'].", ".$data3[0]['state']." ".$data3[0]['zip']."";
echo "<p>";
echo "<b>Voice:</b> &nbsp;&nbsp;&nbsp;".$data3[0]['phone']."<br>";
echo "<b>Fax:</b>     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$data[0]['fax']."";
echo "</font>";
echo "<p>";
echo "<font face=\"arial\" size=\"-1\">";
echo "<b>Sold To:</b>";
echo "<blockquote>";
echo $data2[0]['client']."<br>";
echo $data2[0]['address']."<br>";
echo $data2[0]['city'].", ".$data2[0]['state']." ".$data2[0]['zip']."";
echo "</blockquote></font>";
echo "</td>";
echo "<td align=\"right\" valign=\"top\">";
echo "<font face=\"verdana\" size=\"+2\"><b>INVOICE</b></font><br>";
echo "<font face=\"arial\" size=\"-1\">";
echo "<b>Invoice Number:</b><br>";
echo $data1[0]['invoice']."<br>";
echo "<b>Invoice Date:</b><br>";
$invoicedate=date("M d, Y", strtotime($date1[0]['invoicedate']));
echo $invoicedate;
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
echo "<td><font face=\"arial\" size=\"-1\">".$data2[0]['clientID']."</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">".$data1[0]['customerPO']."</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">Net 7 Days</font></td>";
echo "<td><font face=\"arial\" size=\"-1\">".date("m/d/Y", $data1[0]['invoicedate'])."</font></td>";
echo "</tr>";
echo "</table>";
echo "<table width=\"100%\" border=\"0\">";
echo "<tr bgcolor=\"c0c0c0\" align=\"center\">";
echo "<td><font face=\"arial\" size=\"-1\"><b>Hours</b></font></td>";
echo "<td><font face=\"arial\" size=\"-1\"><b>Item</b></font></td>";
echo "<td><font face=\"arial\" size=\"-1\"><b>Description</b></font></td>";
echo "<td><font face=\"arial\" size=\"-1\"><b>Unit Price</b></font></td>";
echo "<td><font face=\"arial\" size=\"-1\"><b>Extention</b></font></td>";
echo "</tr>";
for($i=0; $i < count($bills); $i++){
	if(bcmod("$i", "2") == 1){
		$color1="ffffff";
	}else{
		$color1="e7e7e7";
	}
	if($bills[$i] == ""){
		$bills[$i]=0;
	}
	$query4=("SELECT \"cost\", \"ID\", \"billed\", \"total\", \"workid\", \"rate\", \"description\" ".
			 "FROM \"timelog\" ".
			 "WHERE \"ID\" = '".$bills[$i]."'");
	if(!($result4=pg_query($connection,$query4))){
		print("Failed query4: " . pg_last_error($connection));
		exit;
	}
	while($row4 = pg_fetch_array($result4)){
		$data4[]=$row4;
	}
	$query5=("SELECT \"ID\", \"description\", \"itemid\" ".
			 "FROM \"billingcodes\" ".
			 "WHERE \"ID\" = '".$data4[0]['workid']."' ");
	if(!($result5=pg_query($connection,$query5))){
		print("Failed query5: " . pg_last_error($connection));
		exit;
	}
	while($row5 = pg_fetch_array($result5)){
		$data5[]=$row5;
	}
	$cost=$data4[0]['cost'];
	$totalcost=bcadd("$cost", "$totalcost", 2);
	$total1=bcdiv("".$data4[0]['total']."", "60", "2");
	echo "<tr bgcolor=\"$color1\">";
	echo "<td align=\"center\"><font face=\"arial\" size=\"-1\">$total1</font></td>";
	echo "<td align=\"center\"><font face=\"arial\" size=\"-1\">".$data5[0]['itemid']."</font></td>";
	echo "<td><font face=\"arial\" size=\"-1\">".$data4[0]['description']."</font></td>";
	echo "<td align=\"right\"><font face=\"arial\" size=\"-1\">$".$data4[0]['rate']."</font></td>";
	echo "<td align=\"right\"><font face=\"arial\" size=\"-1\">$".$data4[0]['cost']."</font></td>";
	echo "</tr>";
	unset($data4);
	unset($data5);
}
echo "</table>";
echo "<p>";
echo "<table width=\"100%\">";
echo "<tr>";
echo "<td align=\"right\">";
echo "<table width=\"300\" border-\"0\">";
echo "<tr>";
echo "<td align=\"right\"><b><font face=\"arial\" size=\"-1\">Subtotal</font></b></td>";
echo "<td align=\"right\"><font face=\"arial\" size=\"-1\">$totalcost</font></td>";
echo "</tr>";
echo "<tr>";
echo "<td align=\"right\"><b><font face=\"arial\" size=\"-1\">Sales Tax</font></b></td>";
echo "<td align=\"right\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td align=\"right\"><b><font face=\"arial\" size=\"-1\">Total Invoice Amount</font></b></td>";
echo "<td align=\"right\"><font face=\"arial\" size=\"-1\">$totalcost</font></td>";
echo "</tr>";
echo "<tr>";
echo "<td align=\"right\"><b><font face=\"arial\" size=\"-1\">Payment Received</font></b></td>";
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
echo "<p>";
echo "<p>";
echo "<font face=\"arial\" size=\"+1\" color=\"ff0000\">";
echo "THIS IS AN ARCHIVED COPY";
require('../../trailer.php');
?>
