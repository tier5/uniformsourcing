<?php
	require('Application.php');
	require('../../header.php');
	echo "<link rel=stylesheet type=\"text/css\" href=\"calendar.css\">";
	echo "<script language=\"JavaScript\">";
	echo "function isFilled(elm) {";
	echo "if (elm.value == \"\" ||";
	echo "elm.value == null)";
	echo "return false;";
	echo "else return true;";
	echo "}";
	echo "function isReady(form) {";
	echo "if (isFilled(form.name) == false) {";
	echo "alert(\"Please enter your name.\");";
	echo "return false;";
if($linkto == "Y") {
	echo "if (form.linktype[1].checked) {";
	echo "if (isFilled(form.linkpage) == false) {";
	echo "alert(\"Please enter the web page address.\");";
	echo "return false;";
	echo "}";
	echo "}";
}
if($postnew == "Y") {
	echo "if (form.linktype[2].checked) {";
	echo "if (isFilled(form.filename) == false) {";
	echo "alert(\"Please enter the file name to post.\");";
	echo "return false;";
	echo "}";
	echo "}";
}
	echo "return true;";
	echo "}";
	echo "</script>";
	echo "<div align=\"center\">";
	echo "<h2>Add to Events Calendar</h2>";
	echo "<form action=\"upd_calendar.php\" enctype=\"multipart/form-data\" method=\"POST\" onSubmit=\"return isReady(this)\" name=\"entryform\">";
	echo "<table>";
	echo "<tr>";
	echo "<td align=\"right\"><b>Name:</b></td>";
	echo "<td><input type=\"text\" name=\"name\" size=\"30\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><b>Date of Event:</b></td>";
	echo "<td><select name=\"month\"> ".
			"<option value=\"1\">January</option> ".
			"<option value=\"2\">Febuary</option> ".
			"<option value=\"3\">March</option> ".
			"<option value=\"4\">April</option> ".
			"<option value=\"5\">May</option> ".
			"<option value=\"6\">June</option> ".
			"<option value=\"7\">July</option> ".
			"<option value=\"8\">August</option> ".
			"<option value=\"9\">September</option> ".
			"<option value=\"10\">October</option> ".
			"<option value=\"11\">November</option> ".
			"<option value=\"12\">December</option> ".
			"</select>";
	echo "<select name=\"day\">";
for($i=1; $i <= 31; $i++) {
	echo "<option value=\"$i\">$i</option>";
}
	echo "</select>";
	echo "<select name=\"year\">";
for($i=date("Y", mktime(0, 0, 0, 1, 1, date("Y"))); $i < date("Y", mktime(0, 0, 0, 1, 1, date("Y")+3)); $i++) {
	echo "<option value=\"$i\">$i</option>";
	}
	echo "</select>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\"><b>Time:</b></td>";
	echo "<td><select name=\"hour\">";
	echo "<option value=\"\" selected>No Time</option>";
for($i=1; $i <= 12; $i++) {
	echo "<option value=\"$i\">$i</option>";
}
	echo "</select>";
	echo "<b>:</b>";
	echo "<select name=\"minute\">";
	echo "<option value=\"00\">00</option>";
	echo "<option value=\"15\">15</option>";
	echo "<option value=\"30\">30</option>";
	echo "<option value=\"45\">45</option>";
	echo "</select>";
	echo "<select name=\"ampm\">";
	echo "<option value=\"AM\">AM</option>";
	echo "<option value=\"PM\">PM</option>";
	echo "</select>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"right\" valign=\"top\"><input type=\"radio\" name=\"linktype\" value=\"none\" checked></td>";
	echo "<td><b>No link for this Event</b></td>";
	echo "</tr>";
if($linkto == "Y") {
	echo "<tr>";
	echo "<td align=\"right\" valign=\"top\"><input type=\"radio\" name=\"linktype\" value=\"page\"></td>";
	echo "<td><b>Link event to existing web page</b> (<i>type or paste web address</i>)<br>";
	echo "<input type=\"text\" name=\"linkpage\" size=\"50\" value=\"http://\"></td>";
	echo "</tr>";
}
if($brief == "Y") {
	echo "<tr>";
	echo "<td align=\"right\" valign=\"top\"><input type=\"radio\" name=\"linktype\" value=\"brief\"></td>";
	echo "<td><b>Link Event to further information</b> (<i>enter more than 256 character details in next screen</i>)</td>";
	echo "</tr>";
}
	echo "<tr>";
	echo "<td>&nbsp;</td>";
	echo "<td><input type=\"Submit\" value=\"Add Event\"></td>";
	echo "</tr>";
	echo "</table>";
	echo "<input type=\"hidden\" name=\"name_required\" value=\"You must enter your name\">";
	echo "</form>";
	echo "</div>";
	echo "<center>";
	echo "<a href=\"list_calendar.php\">List Event Calendar</a> | ";
	echo "<a href=\"admin_menu.php\">Back to Admin Menu</a></div>";
	echo "</center>";
	require('../../trailer.php');
?>
