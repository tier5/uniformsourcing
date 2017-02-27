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
echo "<table>";
echo "<tr>";
echo "<td><b>Author:</b></td>";
echo "<td>";
echo "<option value=\"".$data1[0]['author']."\">".$data1[0]['author']."</option>";
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Title:</b></td>";
echo "<td>".$data1[0]['title']."</td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Keyword:</b></td>";
echo "<td>".$data1[0]['keyword']."</td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Subject:</b></td>";
echo "<td>".$data1[0]['subject']."</td>";
echo "</tr>";
echo "</table>";
echo "<br>";
echo "<b>Article:</b>";
echo "<div align=\"left\" style=\"width:100px:\">".nl2br($data1[0]['article'])."</div>";
echo "</center>";
require('../../trailer.php');
?>
