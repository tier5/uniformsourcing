<?php
require('Application.php');
require('../../header.php');
$startmonth=$_POST['startmonth'];
$startday=$_POST['startday'];
$startyear=$_POST['startyear'];
$endmonth=$_POST['endmonth'];
$endday=$_POST['endday'];
$endyear=$_POST['endyear'];
if($debug == 'on'){
	echo "startmonth IS $startmonth<br>";
	echo "startday IS $startday<br>";
	echo "startyear IS $startyear<br>";
	echo "endmonth IS $endmonth<br>";
	echo "endday IS $endday<br>";
	echo "endyear IS $endyear<br>";
}
if($startmonth == 'month'){
	echo "You forgot to enter a start month";
	require('../../trailer.php');
	exit;
}
if($startday == 'day'){
	echo "You forgot to enter a start day";
	require('../../trailer.php');
	exit;
}
if($startyear == 'year'){
	echo "You forgot to enter a start year";
	require('../../trailer.php');
	exit;
}
if($endmonth == 'month'){
	echo "You forgot to enter a end month";
	require('../../trailer.php');
	exit;
}
if($endday == 'day'){
	echo "You forgot to enter a end day";
	require('../../trailer.php');
	exit;
}
if($endyear == 'year'){
	echo "You forgot to enter a end year";
	require('../../trailer.php');
	exit;
}
$startdate="$startyear-$startmonth-$startday";
$enddate="$endyear-$endmonth-$endday";
$startdate1=strtotime($startdate);
$startdate2=date("Y-m-d", $startdate1);
$enddate1=strtotime($enddate);
$enddate2=date("Y-m-d", $enddate1);
if($startdate1 >= $enddate1){
	echo "The entered startdate is greaterthan or equal to the enddate, You cant to that";
	require('../../trailer.php');
	exit;
}
$query1=("SELECT \"invoices\".\"invoice\", \"invoices\".\"invoicedate\", \"invoices\".\"client\", \"invoices\".\"total\", \"clientDB\".\"client\", \"clientDB\".\"clientID\", \"clientDB\".\"ID\" ".
		 "FROM \"invoices\", \"clientDB\" ".
		 "WHERE \"invoices\".\"client\" = \"clientDB\".\"ID\" AND \"invoices\".\"invoicedate\" >= '$startdate1' AND \"invoices\".\"invoicedate\" <= '$enddate1' ".
		 "ORDER BY \"invoices\".\"invoice\" ASC ");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pf_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
if($debug == 'on'){
	echo "count data1 IS ".count($data1)."<br>";
}
echo "<center>";
echo "<font face=\"verdana\" size=\"+2\" color=\"4a526b\"><b>Search Results</b></font>";
echo "<p>";
echo "<table>";
echo "<tr bgcolor=\"4a526b\">";
echo "<td><b><font face=\"arial\" size=\"-1\" color=\"ffffff\">Invoice Number</font></b></td>";
echo "<td><b><font face=\"arial\" size=\"-1\" color=\"ffffff\">Client</font></b></td>";
echo "<td><b><font face=\"arial\" size=\"-1\" color=\"ffffff\">Date</font></b></td>";
echo "<td><b><font face=\"arial\" size=\"-1\" color=\"ffffff\">Total</font></b></td>";
echo "</tr>";
for($i=0; $i < count($data1); $i++){
	if(bcmod("$i", "2") == 1){
		$color1="ffffff";
	}else{
		$color1="cccccc";
	}
	echo "<tr bgcolor=\"$color1\">";
	echo "<td><font face=\"arial\" size=\"-1\"><a href=\"archived.invoice.php?invoice=".$data1[$i]['invoice']."\">".$data1[$i]['invoice']."</a></font></td>";
	echo "<td><font face=\"arial\" size=\"-1\">".$data1[$i]['client']."</font></td>";
	echo "<td><font face=\"arial\" size=\"-1\">".date("m/d/Y", $data1[$i]['invoicedate'])."</font></td>";
	echo "<td><font face=\"arial\" size=\"-1\">$".$data1[$i]['total']."</font></td>";
	echo "</tr>";
}
echo "</table>";
echo "</center>";
require('../../trailer.php');
?>
