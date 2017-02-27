<?php
	require('Application.php');
if(! isset($id)) {
	header("location: list_calendar.php");
}
	$query=("SELECT \"Evt_ID\", \"Evt_Name\", \"Evt_Date\", \"Evt_Time\", \"Evt_Approved\", \"Evt_Link\", \"Evt_Brief\" ".
			"FROM \"tblCalendar\" ".
			"WHERE \"Evt_ID\" = '$id'");
	if(!($result=pg_query($connection,$query))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
if(count($data1) > 1) {
	echo "ERROR you got more than 1 record from the database";
	exit;
}
while($row = pg_fetch_array($result)) {
	$data1[]=$row;
}
for($i=0; $i < count($data1); $i++) {
	$date=explode('-',$data1[$i]['Evt_Date']);
	$day1=$date[2];
	$month1=$date[1];
	$year1=$date[0];
	$time=$data1[$i]['Evt_Time'];
	$name=$data1[$i]['Evt_Name'];
	$brief=$data1[$i]['Evt_Brief'];
}
$thisdate=date("F dS, Y", mktime(0, 0, 0, $month1, $day1, $year1));
$thistime=$time;
$thisname=$name;
$thisbrief=$brief;
	echo "<link rel=stylesheet type=\"text/css\" href=\"calendar.css\">";
	echo "<div align=\"center\">";
	echo "<h2>Event Calendar Information</h2>";
	echo "<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\" bordercolor=\"$brdrcolor\" width=\"50%\">";
	echo "<tr bgcolor=\"$hdrcolor\">";
	echo "<td align=\"center\"><b><font color=\"$thdrcolor\">$thisdate - $thistime</font></b></td>";
	echo "</tr>";
	echo "<tr bgcolor=\"$hdrcolor\">";
	echo "<td align=\"center\"><font color=\"$thdrcolor\"><b>$thisname</b></font></td>";
	echo "</tr>";
	echo "<tr bgcolor=\"$calcolor\">";
	echo "<td><font color=\"$tcalcolor\">$thisbrief</font></td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "<p><a href=\"javascript:history.go(-1)\">Back to Events Calendar</a>";
	echo "<a href=\"admin_menu.php\">Back to Admin Menu</a></div>";
?>
