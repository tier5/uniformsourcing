<?php
require('Application.php');
require('../header.php');
$query1=("SELECT * ".
		 "FROM \"clientDB\" ".
		 "ORDER BY \"client\" ASC ");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
echo "<font face=\"arial\">";
echo "<blockquote>";
echo "<center><font size=\"5\">PRODUCTION</font>";
echo "<p>";
echo "Choose Client:";
echo "<form action=\"clients/clienthomepage.php\" method=\"post\">";
echo "<select name=\"ID\">";
for($i=0; $i < count($data1); $i++){
	echo "<option value=\"".$data1[$i]['ID']."\">".$data1[$i]['client']."</option>";
}
echo "</select>";
echo "<input type=\"submit\" name=\"submit\" value=\"Submit\">";
echo "</form>";
require('../trailer.php');
?>
