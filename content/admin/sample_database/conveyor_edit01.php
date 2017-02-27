<?php
require('Application.php');

$query1 = ("SELECT sc.\"id\" as \"id\" ".
	", sc.\"name\" as \"name\" ".
	", sc.\"active\" as \"active\" ".
	", sc.\"addedby\" as \"addedby\" ".
	", sc.\"addeddate\" as \"addeddate\" ".
	", sc.\"editedby\" as \"editedby\" ".
	", sc.\"editeddate\" as \"editeddate\" ".
	", ea.\"firstname\" as \"ea_firstname\" ".
	", ea.\"lastname\" as \"ea_lastname\" ".
	", ee.\"firstname\" as \"ee_firstname\" ".
	", ee.\"lastname\" as \"ee_lastname\" ".
	"FROM sample_conveyor AS sc ".
	"LEFT JOIN \"employeeDB\" ea ON sc.\"addedby\" = ea.\"employeeID\" ".
	"LEFT JOIN \"employeeDB\" ee ON sc.\"editedby\" = ee.\"employeeID\" ".
	"ORDER BY sc.\"name\" ");
if(!($result1 = pg_query($connection,$query1))){
	print("Failed query1:<br> $query1 <br><br> " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[] = $row1;
}

require('../../header.php');

echo "<font face=\"arial\">";
echo "<center>";
echo "<font class=\"headline\">Administration - Sample Database</font>";
echo "<br><br>";

echo "<form action=\"conveyor_add01.php\" method=\"POST\">";
echo "<table>";

echo "<tr>";
echo "<td><b>Conveyor Name:</b></td>";
echo "<td><input type=\"text\" name=\"conv_name\"></td>";
echo "</tr>";

echo "<tr>";
echo "<td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"Add Conveyor\"></td>";
echo "</tr>";

echo "</table>";
echo "</form>";

echo "<br><br>";
if(count($data1) >= 1){
	echo "<table border=\"1\">";

	echo "<tr>";
	echo "<th>Name</th>";
	echo "<th>Added By</th>";
	echo "<th>Date Added</th>";
	echo "<th>Edited Last By</th>";
	echo "<th>Date Edited</th>";
	echo "<th>Active</th>";
	echo "</tr>";
	for($i=0, $z=count($data1); $i < $z; $i++){
		echo "<tr>";
		echo "<td>".$data1[$i]['name']."</td>";
		echo "<td>".$data1[$i]['ea_firstname']." ".$data1[$i]['ea_lastname']."</td>";
		if($data1[$i]['addeddate'] > '1'){
			echo "<td>".date("m/d/Y", $data1[$i]['addeddate'])."</td>";
		}else{
			echo "<td>&nbsp;</td>";
		}
		echo "<td>".$data1[$i]['ee_firstname']." ".$data1[$i]['ee_lastname']."</td>";
		if($data1[$i]['editeddate'] > '1'){
			echo "<td>".date("m/d/Y", $data1[$i]['editeddate'])."</td>";
		}else{
			echo "<td>&nbsp;</td>";
		}
		if($data1[$i]['active'] == '1'){
			echo "<td>ACTIVE</td>";
		}else{
			echo "<td>NOT ACTIVE</td>";
		}
		echo "</tr>";
	}

	echo "</table>";
}else{
	echo "<font color=\"red\">There are no Conveyors in the database. Please add one</font>";
}

require('../../trailer.php');
?>
