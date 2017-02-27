<?php
require('Application.php');
require('../../header.php');
$target_dir = "../../uploadFiles/client/";

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
echo "<font face=\"arial\">";
echo "<center>";
echo "<p>";
echo "<form action=\"printlabel.php\" method=\"post\">";
echo "<b><font face=\"arial\">Client : </font></b>";
echo "<select name=\"id\">";
for($i=0; $i < count($data1); $i++){
	echo "<option value=\"".$data1[$i]['ID']."\">".$data1[$i]['client']."</option>";
}
echo "</select>";
echo "<br><br>";
echo "&nbsp; &nbsp;<input type=\"submit\" name=\"\" value=\"submit\">";

 
echo "</form>";
require('../../trailer.php');

?>