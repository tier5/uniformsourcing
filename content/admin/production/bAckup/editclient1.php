<?php
require('Application.php');
require('../../header.php');
$query1=("SELECT * ".
		 "FROM \"clientDB\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER BY \"client\" ASC");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
$query2=("SELECT * ".
		 "FROM \"clientDB\" ".
		 "WHERE \"active\" = 'no' ".
		 "ORDER BY \"client\" ASC");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2)){
	$data2[]=$row2;
}
echo "<font face=\"arial\">";
echo "<center>";
echo "<p>";
echo "<form action=\"editclient2.php\" method=\"post\">";
echo "<b><font face=\"arial\">Choose Active Client to Edit:</font></b><br>";
echo "<select name=\"id\">";
for($i=0; $i < count($data1); $i++){
	echo "<option value=\"".$data1[$i]['ID']."\">".$data1[$i]['client']."</option>";
}
echo "</select>";
echo "<br>";
echo "<input type=\"submit\" name=\"\" value=\"Submit\">";
echo "</form>";
echo "<p>";
echo "<form action=\"editclient2.php\" method=\"post\">";
echo "<b><font face=\"arial\">Choose DeActivated Client to Edit:</font></b><br>";
echo "<select name=\"id\">";
for($i=0; $i < count($data2); $i++){
	echo "<option value=\"".$data2[$i]['ID']."\">".$data2[$i]['client']."</option>";
}
echo "</select>";
echo "<br>";
echo "<input type=\"submit\" name=\"\" value=\"Submit\">";
echo "</form>";
echo "</center>";
require('../../trailer.php');
?>
