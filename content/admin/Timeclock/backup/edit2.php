<?php
require('Application.php');
require('../../header.php');
$employees=$_POST['employees'];
$editmonth=$_POST['editmonth'];
$editday=$_POST['editday'];
$edityear=$_POST['edityear'];
$editdate=date("m/d/Y", mktime(0, 0, 0, $editmonth, $editday, $edityear));
$editdate1=mktime(0, 0, 0, $editmonth, $editday, $edityear);
if($debug == "on"){
	echo "employees IS $employees<br>";
	echo "editmonth IS $editmonth<br>";
	echo "editday IS $editday<br>";
	echo "edityear IS $edityear<br>";
	echo "editdate IS $editdate<br>";
	echo "editdate1 IS $editdate1<br>";
}
if($editmonth == "month"){
	header("location: edit1.php?error=nomonth");
	exit;
}
if($editday == "day"){
	header("location: edit1.php?error=noday");
	exit;
}
if($edityear == "year"){
	header("location: edit1.php?error=noyear");
	exit;
}
$query1=("SELECT e.\"employeeID\" as employeeID, ".
		 "e.\"firstname\" as firstname, ".
		 "e.\"lastname\" as lastname, ".
		 "t.\"firstname\" as tfirstname, ".
		 "t.\"lastname\" as tlastname, ".
		 "t.\"workday\" as tworkday, ".
		 "t.\"ID\" as tID, ".
		 "t.\"clockin\" as tclockin, ".
		 "t.\"out\" as tout ".
		 "FROM \"employeeDB\" e, \"timeclock\" t ".
		 "WHERE e.\"employeeID\" = '$employees' AND e.\"firstname\" = t.\"firstname\" AND e.\"lastname\" = t.\"lastname\" AND t.\"workday\" = '$editdate1'");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
echo "<center>";
echo "<table>";
echo "<tr>";
echo "<td><b><font face=\"arial\" size=\"-1\"><div align=\"center\">EDIT</div></font></b></td>";
echo "<td><b><font face=\"arial\" size=\"-1\"><div align=\"center\">IN</div></font></b></td>";
echo "<td><b><font face=\"arial\" size=\"-1\"><div align=\"center\">OUT</div></font></b></td>";
echo "</tr>";
for($i = 0; $i < count($data1); $i++){
	$clockin1=$data1[$i]['tclockin'];
	$clockin3=date("H:i:s", $clockin1);
	$clockout1=$data1[$i]['tout'];
	$clockout3=date("H:i:s", $clockout1);
	echo "<tr bgcolor=999999>";
	echo "<td><font face=\"arial\" size=\"-2\"><a href=\"edit3.php?ID=".$data1[$i]['tID']."\">Edit</a><br><a href=\"delete.php?ID=".$data1[$i]['tID']."\">Delete</a></font></td>";
	echo "<td><font face=\"arial\" size=\"-1\">".$clockin3."</font></td>";
	echo "<td><font face=\"arial\" size=\"-1\">".$clockout3."</font></td>";
	echo "</tr>";
}
echo "</table>";
echo "</center>";
require('../../trailer.php');
?>
