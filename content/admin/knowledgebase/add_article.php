<?php
require('Application.php');
require('../../header.php');
$query1=("SELECT * ".
		 "FROM \"employeeDB\" ".
		 "WHERE \"employeeID\" != '1'");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[] = $row1;
}
echo "<p>";
echo "<FONT FACE=\"ARIAL\">";
echo "<center>";
echo "<font face=\"arial\" size=\"+1\"><b>Knowledge Base Article Submition</b></font>";
echo "<p>";
echo "<form action=\"input.php\" method=\"POST\">";
echo "<table>";
echo "<tr>";
echo "<td>Author:</td>";
echo "<td><select name=\"author\">";
for($i=0; $i < count($data1); $i++){
	echo "<option value=\"".$data1[$i]['firstname']." ".$data1[$i]['lastname']."\">".$data1[$i]['firstname']." ".$data1[$i]['lastname']."</option>";
}
echo "</select></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Title:</td>";
echo "<td><input type=\"Text\" name=\"title\" size=\"35\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Keyword:</td>";
echo "<td><input type=\"Text\" name=\"keyword\" size=\"35\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Subject:</td>";
echo "<td><input type=\"Text\" name=\"subject\" size=\"35\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Article:</td>";
echo "<td><textarea name=\"article\" cols=\"35\" rows=\"20\"></textarea></td>";
echo "</tr>";
echo "<tr>";
echo "<td colspan=\"2\" align=\"center\"><hr width=\"100%\" noshade><input type=\"Submit\" name=\"\" value=\"Submit\"><input type=\"reset\"></td>";
echo "</tr>";
echo "</table>";
echo "</form>";
require('../../trailer.php');
?>
