<?php
require('Application.php');
require('../../header.php');
if($_GET['ID'] != ""){
	$ID=$_GET['ID'];
}elseif($_POST['ID'] != ""){
	$ID=$_POST['ID'];
}else{
	echo "There was an error setting the ID variable.";
	exit;
}
if($debug == 'on'){
	echo "ID is $ID<br>";
}
$query1=("SELECT * ".
		 "FROM \"clientDB\" ".
		 "WHERE \"ID\" = '$ID' ");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
$query2=("SELECT * ".
		 "FROM \"employeeDB\" ".
		 "WHERE \"employeeID\" = '".$data1[0]['accountmanager']."' ");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2)){
	$data2[]=$row2;
}
echo "<font face=\"arial\">";
echo "<center>";
echo "<table cellpadding=\"8\" border=\"0\" width=\"80%\">";
echo "<tr>";
echo "<td colspan=\"4\" align=\"center\">";
echo "<b><font size=\"+2\" face=\"arial\">".$data1[0]['client']."</font></b>";
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Client ID:</b></td>";
echo "<td>".$data1[0]['clientID']."</td>";
echo "<td><b>Account Manager:</b></td>";
echo "<td>".$data2[0]['firstname']." ".$data2[0]['lastname']."</td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Address:</b></td>";
echo "<td>".$data1[0]['address']."</td>";
echo "<td><b>City:</b></td>";
echo "<td>".$data1[0]['city']."</td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>State:</b></td>";
echo "<td>".$data1[0]['state']."</td>";
echo "<td><b>Zip:</b></td>";
echo "<td>".$data1[0]['zip']."</td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Contact:</b></td>";
echo "<td>".$data1[0]['contact']."</td>";
echo "<td><b>Phone:</b></td>";
echo "<td>".$data1[0]['phone']."</td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Fax:</b></td>";
echo "<td>".$data1[0]['fax']."</td>";
echo "<td><b>Email:</b></td>";
echo "<td><a href=\"mailto:".$data1[0]['email']."\">".$data1[0]['email']."</a></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>URL:</b></td>";
echo "<td><a href=\"".$data1[0]['www']."\">".$data1[0]['www']."</a></td>";
echo "<td><b>Class:</b></td>";
echo "<td>".$data1[0]['class']."</td>";
echo "</tr>";
echo "<tr>";
echo "<td colspan=\"4\"><b>Notes:</b></td>";
echo "</tr>";
echo "<tr>";
echo "<td colspan=\"4\">".$data1[0]['notes']."</td>";
echo "</tr>";
echo "<tr>";
echo "<td colspan=\"4\" align=\"center\" bgcolor=\"c0c0c0\">";
echo "<a href=\"work/work.add.php?whoop=".$data1[0]['ID']."\">Enter A New Workorder</a> | ";
echo "<a href=\"work/work.view.php?whoop=".$data1[0]['ID']."\">View Current Workorders</a>";
echo "</td>";
echo "</tr>";
echo "</table>";
echo "</center>";
require('../../trailer.php');
?>
