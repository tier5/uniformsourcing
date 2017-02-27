<?php
	require('Application.php');
	require('../../header.php');
	$query=("SELECT \"Evt_ID\", \"Evt_Name\", \"Evt_Date\", \"Evt_Time\", \"Evt_Approved\", \"Evt_Link\", \"Evt_Brief\" " .
			"FROM \"tblCalendar\" ".
			"WHERE \"Evt_ID\" = '".$_POST['Evt_ID']."'");
	if(!($result = pg_query($connection,$query))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
	print("<link rel=stylesheet type=\"text/css\" href=\"calendar.css\">");
	print("<script language=\"JavaScript\"> ".
		  "function isFilled(elm) { ".
		  "if (elm.value == \"\" || ".
		  "elm.value == null) ".
		  "return false; ".
		  "else return true; ".
		  "}");
	print("function isReady(form) { ".
		  "if (isFilled(form.evt_date) == false) { ".
		  "alert(\"Please enter the date of the event.\"); ".
		  "return false; ".
		  "}".
		  "if (isFilled(form.evt_name) == false) {".
		  "alert(\"Please enter the event.\");".
		  "return false;".
		  "}".
		  "if(form.evt_name.value.length > 255) {".
		  "alert(\"Event description must be less than 256 characters.\");".
		  "return false;".
		  "}".
		  "return true;".
		  "}");
	print("</script>");
	echo "<div align=\"center\">";
	echo "<h2>Edit Calendar Item<h2>";
	while($row = pg_fetch_array($result)) {
		$data1[]=$row;
	}
		for($i=0; $i < count($data1); $i++) {
			echo "<form action=\"update.php\" method=\"POST\" onSubmit=\"return isReady(this)\">";
			echo "<table>";
			echo "<tr>";
			echo "<td align=\"right\"><b>Name:</b></td>";
			echo "<td><input type=\"text\" name=\"Evt_Name\" value=\"".$data1[$i]['Evt_Name']."\" size=\"30\"></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td align=\"right\"><b>Date of Event:</b></td>";
			$date1=date("m/d/Y", $data1[$i]['Evt_Date']);
			$date2=explode("/", $date1);
			$month1=$date2[0];
			$month2=date("F", mktime(0, 0, 0, $month1, 1, 2005));
			$day1=$date2[1];
			$year1=$date2[2];
			echo "<td><select name=\"month\">";
			echo "<option value=\"$month1\">$month2</option>";
			for($ii=1; $ii <= 12; $ii++){
				$monthnam=date("F", mktime(0, 0, 0, $ii, 1, 2005));
				$monthnum=date("m", mktime(0, 0, 0, $ii, 1, 2005));
				echo "<option value=\"$monthnum\">$monthnam</option>";
			}
			echo "</select>";
			echo "<select name=\"day\">";
			echo "<option value=\"$day1\">$day1</option>";
			for($ii=1; $ii <= 31; $ii++){
				echo "<option value=\"$ii\">$ii</option>";
			}
			echo "</select>";
			echo "<select name=\"year\">";
			echo "<option value=\"$year1\">$year1</option>";
			for($ii=0; $ii <= 3; $ii++){
				$yearnum=date("Y", mktime(0, 0, 0, 1, 1, date("Y")+$ii));
				echo "<option value=\"$yearnum\">$yearnum</option>";
			}
			echo "</select></td>";
			echo "</tr>";
			if($data1[$i]['Evt_Time'] != ""){
				echo "<tr>";
				echo "<td align=\"right\"><b>Time:</b></td>";
				echo "<td><select name=\"hour\">";
				$time1=explode(" ", $data1[$i]['Evt_Time']);
				$time2=explode(":", $time1[0]);
				$time3=$time1[1]; // AM PM
				$time4=$time2[0]; // hour
				$time5=$time2[1]; //min
				echo "<option value=\"$time4\">$time4</option>";
				for($ii=1; $ii <= 12; $ii++){
					echo "<option value=\"$ii\">$ii</option>";
				}
				echo "</select>";
				echo "<b>:</b>";
				echo "<select name=\"minute\">";
				echo "<option value=\"$time5\">$time5</option>";
				echo "<option value=\"00\">00</option>";
				echo "<option value=\"15\">15</option>";
				echo "<option value=\"30\">30</option>";
				echo "<option value=\"45\">45</option>";
				echo "</select>";
				echo "<select name=\"ampm\">";
				echo "<option value=\"$time3\">$time3</option>";
				echo "<option value=\"AM\">AM</option>";
				echo "<option value=\"PM\">PM</option>";
				echo "</select>";
				echo "</td>";
				echo "</tr>";
			}
			echo "</table>";
			echo "<table>";
			if($data1[$i]['Evt_Link'] != ""){
				echo "<tr align=\"center\">";
				echo "<td colspan=\"2\"><b>Link event to existing web page</b> (<i>type or paste web address</i>)<br>";
				echo "<input type=\"text\" name=\"evt_link\" size=\"50\" value=\"".$data1[$i]['Evt_Link']."\"></td>";
				echo "</tr>";
			}
			if($data1[$i]['Evt_Brief'] != ""){
				echo "<tr>";
				echo "<td colspan=\"2\"><b>Link Event to further information</b> (<i>enter less than 256 characters</i>)<br>";
				echo "<textarea name=\"Evt_Brief\" cols=60 rows=6 wrap=\"VIRTUAL\">".$data1[$i]['Evt_Brief']."</textarea></td>";
				echo "</tr>";
			}
			echo "<tr>";
			echo "<td>&nbsp;</td>";
			echo "<td><input type=\"Submit\" value=\"Update Event\" class=button></td>";
			echo "</tr>";
			echo "</table>";
			echo "<input type=\"hidden\" name=\"Evt_ID\" value=\"".$data1[$i]['Evt_ID']."\">";
			echo "</form>";
			echo "<a href=\"admin_menu.php\">Back to Admin Menu</a></div>";
		}
require('../../trailer.php');
?>
