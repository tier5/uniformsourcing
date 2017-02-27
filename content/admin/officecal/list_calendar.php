<?php
	require('Application.php');
	require('../../header.php');
//	for reference
	$today==date("M/d/Y", mktime(0, 0, 0, date("m") , date("d") , date("Y")));
	// prints todays date IE Aug/24/2005
	$tomorrow=date("m/d/Y", mktime(0, 0, 0, date("m") , date("d")+1 , date("Y")));
	// prints 08/25/2005
	$yesterday=date("m/d/Y", mktime(0, 0, 0, date("m") , date("d")-1 , date("Y")));
	// prints 08/23/2005
	$nextmonthmk=date("M/d/Y", mktime(0, 0, 0, date("m")+1 , 1, date("Y")));
	// prints Sep/01/2005
	$lastmonthmk=date("M/d/Y", mktime(0, 0, 0, date("m")-1 , 1, date("Y")));
	//prints Jul/01/2005
//	STARTING REAL VARIBLE DECLERATIONS
// If statment to see if we are comming back to this page with a month and year defined. The reason for the $_POST and non is because
// of the form tag for "Show Month" and the a href tags for Previous and Next.
if((isset($_POST['month'])) && (isset($_POST['year']))) {
	$month=date("m", mktime(0, 0, 0, $_POST[month], 1, $_POST[year]));
        $year=date("Y", mktime(0, 0, 0, $_POST[month], 1, $_POST[year]));
	} elseif((isset($month)) && (isset($year))) {
	$month=date("m", mktime(0, 0, 0, $month, 1, $year));
        $year=date("Y", mktime(0, 0, 0, $month, 1, $year));
	} else {
	$month=date("n", mktime(0, 0, 0, date("m"), 1, date("Y")));
	$year=date("Y", mktime(0, 0, 0, date("m"), 1, date("Y")));
}
	$startdate=mktime(0, 0, 0, $month, 1, $year);// Get the month and year set day to 01
	$enddate=mktime(0, 0, 0, $month+1, 0, $year);// get last day of month by current month day set to 0
	$datestring=date("F", mktime(0, 0, 0, $month, 1, $year));// get month complete name
	$monthstring=date("F", mktime(0, 0, 0, $month, 1, $year));
    $yearstring=date("Y", mktime(0, 0, 0, $month, 1, $year));// get 4 digit year
    $nextmonth=date("m", mktime(0, 0, 0, $month+1, 1, $year));// first day of next month
    $nextmm=date("m", mktime(0, 0, 0, $month+1, 1, $year));// first day of next month
    $nextyy=date("Y", mktime(0, 0, 0, $month+1, 1, $year));
    $lastmonth=date("m", mktime(0, 0, 0, $month, 0, $year));
    $lastmm=date("m", mktime(0, 0, 0, $month, 0, $year));
	$lastyy=date("Y", mktime(0, 0, 0, $month, 0, $year));
	$dayinmonth=date("t", mktime(0, 0, 0, $month, 1, $year));
//	Begin Query to get EVENTS for displayed month
	$query= ("SELECT \"Evt_ID\", \"Evt_Name\", \"Evt_Date\", \"Evt_Time\", \"Evt_Approved\", \"Evt_Link\", \"Evt_Brief\" " .
		"FROM \"tblCalendar\" " .
		"WHERE \"Evt_Date\" >= '$startdate' AND \"Evt_Date\" <= '$enddate' AND \"Evt_Approved\" = '1' " .
		"ORDER BY \"Evt_Date\", \"Evt_Time\"");
	if(!($result = pg_query($connection,$query))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
while($row = pg_fetch_array($result)) {
	$data1[]=$row;
}
	print("<link rel=stylesheet type=\"text/css\" href=\"calendar.css\">");
//	Begin making calendar Top portion
	print("<table width=\"99%\">" .
		"<tr>" .
			"<td valign=\"top\">" .
				"<a href=\"list_calendar.php?month=$lastmm&year=$lastyy\" class=link>&lt;-- Previous</a>" .
			"</td>" .
			"<td align=\"center\" valign=\"top\">" .
				"<form action=\"list_calendar.php\" method=\"POST\">" .
				"<select name=\"month\">" .
					"<option value=\"$month\">$monthstring</option>" .
					"<option value=\"01\">January</option>" .
					"<option value=\"02\">Febuary</option>" .
					"<option value=\"03\">March</option>" .
					"<option value=\"04\">April</option>" .
					"<option value=\"05\">May</option>" .
					"<option value=\"06\">June</option>" .
					"<option value=\"07\">July</option>" .
					"<option value=\"08\">August</option>" .
					"<option value=\"09\">September</option>" .
					"<option value=\"10\">October</option>" .
					"<option value=\"11\">November</option>" .
					"<option value=\"12\">December</option>" .
				"</select>" .
				"&nbsp;&nbsp;" .
				"<select name=\"year\">" .
					"<option value=\"$yearstring\">$yearstring</option>");
	for($idx=date("Y", mktime(0, 0, 0, $month, 1, $year-1)); $idx <= date("Y-m-d", mktime(0, 0, 0, $month, 1, $year+2)); $idx++) {
					print("<option value=\"$idx\">$idx</option>");
	}
				print("</select>" .
				"&nbsp;&nbsp;" .
				"<input type=\"SUBMIT\" value=\"Show Month\">" .
				"</form>" .
			"</td>" .
			"<td align=\"right\" valign=\"top\">" .
				"<a href=\"list_calendar.php?month=$nextmm&year=$nextyy\" class=link>Next --&gt;</a>" .
			"</td>" .
		"</tr>" .
	"</table>");
//	Making body of the calendar
	print("<table border=\"1\" cellspacing=\"0\" cellpadding=\"2\" bordercolor=\"$brdrcolor\" width=\"99%\">" .
		"<tr bgcolor=\"$hdrcolor\">" .
			"<td width=8% valign=middle align=center><b><font color=\"$thdrcolor\">Sun</font></b></td>" .
			"<td width=16% valign=middle align=center><b><font color=\"$thdrcolor\">Mon</fonr></b></td>" .
			"<td width=16% valign=middle align=center><b><font color=\"$thdrcolor\">Tue</font></b></td>" .
			"<td width=16% valign=middle align=center><b><font color=\"$thdrcolor\">Wed</font></b></td>" .
			"<td width=16% valign=middle align=center><b><font color=\"$thdrcolor\">Thu</font></b></td>" .
			"<td width=16% valign=middle align=center><b><font color=\"$thdrcolor\">Fri</font></b></td>" .
			"<td width=8% valign=middle align=center><b><font color=\"$thdrcolor\">Sat</font></b></td>" .
		"</tr>");
	print("<tr bgcolor=\"$extcolor\">");
	$cday=0;
	$spacer=date("w", mktime(0, 0, 0, $month, 1, $year));
for($iindex=1; $iindex <= $spacer; $iindex++) {
		print("<td bgcolor=\"$extcolor\">&nbsp;" .
			"</td>");
			}
// Data for each date cell
for($iindex=1; $iindex <= date("t", mktime(0, 0, 0, $month, 1, $year)); $iindex++) {
	$odate=date("Y-m-d", mktime(0, 0, 0, $month, $iindex, $year));
	$odate1=date("w", mktime(0, 0, 0, $month, $iindex, $year))+1;
	print("<td align=\"left\" valign=\"top\" bgcolor=\"$calcolor\">" .
		"<b><font color=\"$tcalcolor\">$iindex</b><p>");
	$count1=0;
	for($i=0; $i < count($data1); $i++) {
		$date=date("d",$data1[$i]['Evt_Date']);
		if($iindex != $date) { continue; }
		if($data1[$i]['Evt_Link'] != "") {
			if($data1[$i]['Evt_Time'] != "") {
				print($data1[$i]['Evt_Time'] . " ");
			}
			print('<a href="'.$data1[$i]['Evt_Link'].'">'.$data1[$i]['Evt_Name'].'</a><br>');
			$count1++;
		} elseif($data1[$i]['Evt_Brief'] != "") {
			if($data1[$i]['Evt_Time'] != "") {
				print($data1[$i]['Evt_Time'] . " ");
			}
			print("<a href=\"list_brief.php?id=".$data1[$i]['Evt_ID']."\">".$data1[$i]['Evt_Name']."</a><br>");
			$count1++;
		} else {
			if($data1[$i]['Evt_Time'] != "") {
				print($data1[$i]['Evt_Time'] . " ");
			}
			print($data1[$i]['Evt_Name'] . "<br>");
			$count1++;
		}
	}
	for($ii=$count1; $ii < 4; $ii++){
		print("$ii<br>");
	}
	print("</font>" .
	  "</td>");
	if($odate1 == 7 AND $iindex != $dayinmonth) {
		$cday=0;
		print("</tr>" .
			  "<tr bgcolor=\"$extcolor\">");
	} else {
		$cday=$cday + 1;
	}
}
while($cday <= 6) {
	print("<td bgcolor=\"$extcolor\">&nbsp;" .
		  "</td>");
	$cday++;
}
print("</tr>" .
	  "</table>");
//	Bottom portion of calendar
	print("<div align=\"center\">" .
		"<img src=\"../../images/i2net.gif\" border=0><br>" .
		"</div>" .
		"<p>" .
		"</center>");
	echo "<center>";
	echo "<a href=\"admin_menu.php\">Back to Admin Menu</a></div>";
	echo "</center>";
	require('../../trailer.php');
?>
