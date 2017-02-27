<?
	require('Application.php');
	require('../../header.php');
	echo "<link rel=stylesheet type=\"text/css\" href=\"calendar.css\">";
if($_POST['linktype'] == "brief" AND ! isset($_POST['brief'])) {
	echo "<div align=\"center\">";
	echo "<form action=\"upd_calendar.php\" method=\"POST\">";
	echo "<b>Linked Information About Event</b><p>";
	echo "<table width=\"50%\">";
	echo "<tr>";
	echo "<td align=\"center\">$_POST[name]</td>";
	echo "</tr>";
	echo "</table><p>";
	echo "<textarea cols=\"60\" rows=\"8\" name=\"brief\"></textarea><br>";
	echo "<i>Enter or paste full text information about this event.<br>";
	echo "Any Word formatting will be ignored.</i>";
	echo "<input type=\"hidden\" name=\"name\" value=\"$_POST[name]\">";
	echo "<input type=\"hidden\" name=\"month\" value=\"$_POST[month]\">";
	echo "<input type=\"hidden\" name=\"day\" value=\"$_POST[day]\">";
	echo "<input type=\"hidden\" name=\"year\" value=\"$_POST[year]\">";
	echo "<input type=\"hidden\" name=\"linktype\" value=\"$_POST[linktype]\">";
	echo "<input type=\"hidden\" name=\"ampm\" value=\"$_POST[ampm]\">";
	echo "<input type=\"hidden\" name=\"hour\" value=\"$_POST[hour]\">";
	echo "<input type=\"hidden\" name=\"minute\" value=\"$_POST[minute]\">";
	echo "<p>";
	echo "<input type=\"Submit\" value=\"Add Brief Info\">";
	echo "</form>";
	echo "</div>";
	echo "<a href=\"javascript:history.go(-1)\">Back to Add Events Page</a>";
	echo "</body>";
	echo "</html>";
exit;
}
if($_POST['hour'] == "") {
	$hour="";
}else{
	$hour="$_POST[hour]:$_POST[minute] $_POST[ampm]";
}
if($autoapprove == "Y") {
	$approved=1;
}else{
	$approved=0;
}
if($_POST['linktype'] == "page") {
	$linktype1=$_POST['linkpage'];
}else{
	$linktype1="";
}
if($_POST['linktype'] == "brief") {
	$linktype2=$_POST['brief'];
}else{
	$linktype2="";
}
	$date1=mktime(0, 0, 0, $_POST['month'], $_POST['day'], $_POST['year']);
	$query=("INSERT INTO \"tblCalendar\" ".
			"(\"Evt_Name\", \"Evt_Date\", \"Evt_Time\", \"Evt_Approved\", \"Evt_Link\", \"Evt_Brief\") " .
			"VALUES ('".$_POST['name']."', '$date1', '$hour', '$approved', '$linktype1', '$linktype2')");
	if(!($result= pg_query($connection,$query))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
if($sendmail == "Y") {
	echo "Put sendmail code here";
}
	echo "<h2>Event Submitted</h2>";
	echo "The event you entered was successfully submitted.";
	echo "<form>";
if($_POST['linktype'] == "brief") {
	echo "<input type=\"BUTTON\" value=\"Add More Events\" onClick=\"history.go(-2);\">";
}else{
	echo "<input type=\"BUTTON\" value=\"Add More Events\" onClick=\"history.go(-1);\">";
}
	echo "</form><br>";
	echo "<a href=\"admin_menu.php\">Back to Admin Menu</a></div>";
	require('../../trailer.php');
?>
