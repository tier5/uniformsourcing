<?php
	require('Application.php');
	require('../../header.php');
	$EVT_DATE = mktime();
	$sql1 = ("SELECT \"Evt_ID\", \"Evt_Name\", \"Evt_Date\", \"Evt_Time\", \"Evt_Approved\", \"Evt_Link\", \"Evt_Brief\" " .
		"FROM \"tblCalendar\" " .
		"WHERE \"Evt_Date\" >= '$EVT_DATE' ");
	if(!($result1 = pg_query($connection,$sql1))){
		print("Failed sql1: " . pg_last_error($connection));
		exit;
	}
	$sql2 = "SELECT \"Evt_ID\", \"Evt_Name\", \"Evt_Date\", \"Evt_Time\", \"Evt_Approved\", \"Evt_Link\", \"Evt_Brief\" " .
		"FROM \"tblCalendar\" " .
		"WHERE \"Evt_Date\" < '$EVT_DATE' ";
	if(!($result2 = pg_query($connection,$sql2))){
		print("Failed sql2: " . pg_last_error($connection));
		exit;
	}
	print("<link rel=stylesheet type=\"text/css\" href=\"calendar.css\">");
?>
<html>
<head>
<title>
Interactive Ideas Internal Intranet Calendar - ADMIN
</title>
</head>

<div align="center">
<h2>Calendar Admin Menu</h2>
<form action="edit.php" method="post">
<select name="Evt_ID">
	<? while($row1 = pg_fetch_array($result1)) {
		//print("<option value={$row['Evt_ID']}>{$row['Evt_Date']} - {$row['Evt_Name']}</option>");
		$data1[]=$row1;
		}
	for($i=0; $i < count($data1); $i++){
		echo "<option value=\"".$data1[$i]['Evt_ID']."\">".date("m/d/Y", $data1[$i]['Evt_Date'])." - ".$data1[$i]['Evt_Name']."</option>";
	}
?>
</select>
<input type="submit" value="Edit Future Event" class="button">
</form><p>
<form action="edit.php" method="post">
<select name="Evt_ID">
	<? while($row2 = pg_fetch_array($result2)) {
		//print("<option value={$row['Evt_ID']}>{$row['Evt_Date']} - {$row['Evt_Name']}</option>");
		$data2[]=$row2;
	}
	for($i=0; $i < count($data2); $i++){
		echo "<option value=\"".$data2[$i]['Evt_ID']."\">".date("m/d/Y", $data2[$i]['Evt_Date'])." - ".$data2[$i]['Evt_Name']."</option>";
	}
?>
</select>
<input type="submit" value="Edit Past Event" class="button">
</form><p>
<a href="add_calendar.php">Add To Calendar</a><p>
<a href="list_calendar.php">List Calendar</a><p>
</div>
</html>
<? require('../../trailer.php'); ?>
