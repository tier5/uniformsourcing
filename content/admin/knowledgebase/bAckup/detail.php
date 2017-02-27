<?php
require('Application.php');
require('../../header.php');
$articleID=$_GET['articleID'];
$query1=("SELECT * ".
		 "FROM \"knowledgebase\" ".
		 "WHERE \"articleID\" = '$articleID'");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
$query2=("SELECT \"firstname\", \"lastname\" ".
		 "FROM \"employeeDB\"");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2)){
	$data2[]=$row2;
}
echo "<center>";
echo "<p>";
echo "<font face=\"arial\">";
echo "<font face=\"arial\" size=\"+1\"><b>Knowledge Base Article Form</b></font>";
echo "<p>";
echo "<form action=\"update.php\" method=\"post\">";
echo "<table>";
echo "<tr>";
echo "<td>Author:</td>";
echo "<td>";
echo "<select name=\"author\">";
echo "<option value=\"".$data1[0]['author']."\">".$data1[0]['author']."</option>";
for($i=0; $i < count($data2); $i++){
	echo "<option value=\"".$data2[$i]['firstname']." ".$data2[$i]['lastname']."\">".$data2[$i]['firstname']." ".$data2[$i]['lastname']."</option>";
}
echo "</select></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Title:</td>";
echo "<td><input type=\"text\" name=\"title\" value=\"".$data1[0]['title']."\" size=\"35\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Keyword:</td>";
echo "<td><input type=\"text\" name=\"keyword\" value=\"".$data1[0]['keyword']."\" size=\"35\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Subject:</td>";
echo "<td><input type=\"text\" name=\"subject\" value=\"".$data1[0]['subject']."\" size=\"35\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Article:</td>";
echo "<td><textarea name=\"article\" cols=\"50\" rows=\"30\">".$data1[0]['article']."</textarea></td>";
echo "</tr>";
echo "<tr>";
echo "<input type=\"hidden\" name=\"articleID\" value=\"".$data1[0]['articleID']."\">";
echo "<td colspan=\"2\" align=\"center\"><hr width=\"80%\" noshade><input type=\"submit\" name=\"\" value=\"submit\"><input type=\"reset\"></td>";
echo "</tr>";
echo "</table>";
echo "</form>";
echo "</center>";
echo "<p>";
echo "<center>";
echo "<spacer type=verticle size=\"20\">";
echo "<hr>";
require('../../trailer.php');
?>
