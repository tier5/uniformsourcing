<?php
require('Application.php');
require('../../header.php');
$id=$_GET['ID'];
if($debug == "on"){
	echo "id IS $id<br>";
}
$query1=("SELECT \"ID\", \"firstname\", \"lastname\", \"workday\", \"clockin\", \"out\", \"status\", \"total\" ".
		 "FROM \"timeclock\" ".
		 "WHERE \"ID\" = '$id'");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
echo "<center>";
echo "<h4>You sure you want to delete this record?</h4>";
echo "<table>";
echo "<tr>";
echo "<td><b>ID</b></td>";
echo "<td><b>firstname</b></td>";
echo "<td><b>lastname</b></td>";
echo "<td><b>workday</b></td>";
echo "<td><b>clockin</b></td>";
echo "<td><b>out</b></td>";
echo "<td><b>status</b></td>";
echo "<td><b>total</b></td>";
echo "</tr>";
echo "<tr bgcolor=bbbbbb>";
echo "<td>".$data1[0]['ID']."</td>";
echo "<td>".$data1[0]['firstname']."</td>";
echo "<td>".$data1[0]['lastname']."</td>";
echo "<td>".date("m/d/Y", $data1[0]['workday'])."</td>";
echo "<td>".date("m/d/Y H:i:s", $data1[0]['clockin'])."</td>";
echo "<td>".date("m/d/Y H:i:s", $data1[0]['out'])."</td>";
echo "<td>".$data1[0]['status']."</td>";
echo "<td>".$data1[0]['total']."</td>";
echo "</tr>";
echo "<tr>";
echo "<form method=\"post\" action=\"delete1.php\">";
echo "<input type=\"hidden\" name=\"id\" value=\"$id\">";
echo "<td colspan=\"8\"><div align=\"center\"><input type=\"submit\" value=\"    Really Delete    \"></div></td>";
echo "</form>";
echo "</tr>";
echo "</table>";
require('../../trailer.php');
?>
