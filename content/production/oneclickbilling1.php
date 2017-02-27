<?php
require('Application.php');
require('../header.php');
$query1=("SELECT w.\"ID\" as \"wID\", ".
		 "w.\"clientID\" as \"wclientID\", ".
		 "w.\"employeeID\" as \"eemployeeID\", ".
		 "w.\"open\" as \"wopen\", ".
		 "w.\"completiondate\" as \"completiondate\", ".
		 "c.\"ID\" as \"cclientID\", ".
		 "c.\"client\" as \"client\", ".
		 "c.\"class\" as \"class\", ".
		 "e.\"employeeID\" as \"eemployeeID\", ".
		 "e.\"firstname\" as \"efirstname\", ".
		 "e.\"lastname\" as \"elastname\" ".
		 "FROM \"work1\" w, \"clientDB\" c, \"employeeDB\" e ".
		 "WHERE w.\"clientID\" = c.\"ID\" AND w.\"employeeID\" = e.\"employeeID\" AND w.\"open\" = 'no' AND w.\"employeeID\" = '".$_SESSION['employeeID']."'".
		 "ORDER BY \"completiondate\", \"class\" ASC");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
echo "<font face=\"arial\" size=\"+2\"><b><center>Open Workorders</center></b></font>";
echo "<p>";
$late=0;
$current=0;
for($i=0; $i < count($data1); $i++){
	if(date("m/d/Y", $data1[$i]['completiondate']) < date("m/d/Y")){
		$late=1;
	}
	if(date("m/d/Y", $data1[$i]['completiondate']) >= date("m/d/Y")){
		$current=1;
	}
}
if($late > 0){
	echo "<div align=\"center\"><font size=\"+2\"> Late Workorders </font></div>";
}
for($i=0; $i < count($data1); $i++){
	if(date("m/d/Y", $data1[$i]['completiondate']) < date("m/d/Y")){
		$wID=$data1[$i]['client'];
		echo "<div align=\"center\"><a href=\"work.view1.php?ID=".$data1[$i]['wID']."\">".$data1[$i]['client']." - (".$data1[$i]['efirstname']." ".$data1[$i]['elastname'].")</a></div>";
	}
}
if($current > 0){
	echo "<br><div align=\"center\"><font size=\"+2\"> Current Ones </font></div>";
}
for($i=0; $i < count($data1); $i++){
	if(date("m/d/Y", $data1[$i]['completiondate']) >= date("m/d/Y")){
		echo "<div align=\"center\"><a href=\"work.view1.php?ID=".$data1[$i]['wID']."\">".$data1[$i]['client']." - (".$data1[$i]['efirstname']." ".$data1[$i]['elastname'].")</a></div>";
	}
}
if($current <= 0 AND $late <= 0){
	echo "<br><div align=\"center\"><font size=\"+2\"> No Workorders Assigned </font></div>";
}
echo "<br><br>";
require('../trailer.php');
?>
