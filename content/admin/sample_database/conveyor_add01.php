<?php
require('Application.php');

if($_POST['submit'] == "Add Conveyor"){

	$conv_name = strtoupper(trim($_POST['conv_name']));

	$query1 = ("SELECT id, name ".
		"FROM sample_conveyor ".
		"WHERE name = '$conv_name' ");
	if(!($result1 = pg_query($connection,$query1))){
		print("Failed query1:<br> $query1 <br><br> " . pg_last_error($connection));
		exit;
	}
	while($row1 = pg_fetch_array($result1)){
		$data1[] = $row1;
	}

	if(count($data1) > 0 AND $conv_name != ""){
		$conv_error = "That name is already in use";
	}else{
		$query2 = ("INSERT INTO sample_conveyor ".
			"(name, active, addedby, addeddate) ".
			"VALUES('".pg_escape_string($conv_name)."', '1', '".$_SESSION['employeeID']."', '".mktime()."') ");
		if(!($result2 = pg_query($connection,$query2))){
			print("Failed query2:<br> $query2 <br><br> " . pg_last_error($connection));
			exit;
		}
	}
}

require('../../header.php');
echo "<font face=\"arial\">";
echo "<center>";
echo "<font class=\"headline\">Administration - Sample Database</font>";
echo "<br>";

if(isset($conv_error) AND $conv_error != ""){
	echo "<font color=\"red\">".$conv_error."</font>";
}

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

require('../../trailer.php');
?>
