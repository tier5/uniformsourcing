<?php
require('Application.php');
require('../../header.php');
$cID=$_GET['cID'];
$query1=("SELECT * ".
		 "FROM \"timelog\" ".
		 "WHERE \"client\" = '$cID' AND \"billed\" = 'no'");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
$query2=("SELECT \"ID\", \"client\" ".
		 "FROM \"clientDB\" ".
		 "WHERE \"ID\" = '$cID'");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2)){
	$data2[]=$row2;
}
echo "<center>";
echo "<font face=\"verdana\" size=\"+2\" color=\"d6a500\"><b>".$data2[0]['client']."</b></font>";
echo "<p>";
echo "<table width=\"100%\">";
echo "<tr>";
echo "<td><b><font face=\"arial\" size=\"-1\">Date Billed</font></b></td>";
echo "<td><b><font face=\"arial\" size=\"-1\">Date Assigned</font></b></td>";
echo "<td><b><font face=\"arial\" size=\"-1\">Date to be Completed</font></b></td>";
echo "<td><b><font face=\"arial\" size=\"-1\">Work ID</font></b></td>";
echo "<td><b><font face=\"arial\" size=\"-1\">Description</font></b></td>";
echo "<td><b><font face=\"arial\" size=\"-1\">Add To Bill</font></b></td>";
echo "</tr>";
echo "<form action=\"invoice.php\" method=\"post\">";
echo "<input type=\"hidden\" name=\"cID\" value=\"".$data2[0]['ID']."\">";
for($i=0; $i < count($data1); $i++){
	$query3=("SELECT \"ID\", \"itemid\", \"description\" ".
			 "FROM \"billingcodes\" ".
			 "WHERE \"ID\" = '".$data1[$i]['workid']."' ");
	if(!($result3=pg_query($connection,$query3))){
		print("Failed query3: " . pg_last_error($connection));
		exit;
	}
	while($row3 = pg_fetch_array($result3)){
		$data3[]=$row3;
	}
	echo "<tr bgcolor=\"dddddd\">";
	echo "<td><font face=\"arial\" size=\"-1\">".date("m/d/Y", $data1[$i]['wodate'])."</font></td>";
	echo "<td><font face=\"arial\" size=\"-1\">".date("m/d/Y", $data1[$i]['start'])."</font></td>";
	echo "<td><font face=\"arial\" size=\"-1\">".date("m/d/Y", $data1[$i]['end'])."</font></td>";
	echo "<td><font face=\"arial\" size=\"-1\">".$data3[0]['itemid']." - ".$data3[0]['description']."</font></td>";
	echo "<td><font face=\"arial\" size=\"-1\">".$data1[$i]['description']."</font></td>";
	echo "<td align=\"center\"><input type=\"checkbox\" name=\"bill[]\" value=\"".$data1[$i]['ID']."\"></td>";
	echo "</tr>";
	unset($data3);
}
echo "<input type=\"submit\" value=\"Next\">";
echo "</form>";
echo "</center>";
require('../../trailer.php');
echo "</table>";
?>
