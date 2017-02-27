<?php
require('Application.php');
require('../../header.php');
$cID=$_POST['cID'];
for($i=0; $i < count($_POST['bill']); $i++){
	$bill[$i]=$_POST['bill'][$i];
}
if($debug == "on"){
	echo "cID IS $cID<br>";
	echo "count bill IS ".count($bill)."<br>";
	for($i=0; $i < count($bill); $i++){
		echo "bill $i IS $bill[$i]<br>";
	}
}
echo "<form action=\"bill.php\" method=\"post\">";
for($i=0; $i < count($bill); $i++){
	echo "<input type=\"hidden\" name=\"bill[$i]\" value=\"$bill[$i]\">";
}
echo "<input type=\"hidden\" name=\"cID\" value=\"$cID\">";
echo "<table>";
echo "<tr>";
echo "<td>Customer PO:</td>";
echo "<td><input type=\"text\" name=\"po\" size=\"20\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td colspan=\"2\"><input type=\"submit\" name=\"submit\" value=\"Next\"></td>";
echo "</tr>";
echo "</table>";
echo "</form>";
require('../../trailer.php');
?>
