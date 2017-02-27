<?php
	require('Application.php');
	$date=explode('/',$_POST['Evt_Date']);
	$day1=$date[1];
	$month1=$date[0];
	$year1=$date[2];
	$date1=mktime(0, 0, 0, $month1, $day1, $year1);
	$query=("UPDATE \"tblCalendar\" ".
			"SET \"Evt_Name\" = '".$_POST['Evt_Name']."', ".
			"\"Evt_Date\" = '$date1', ".
			"\"Evt_Time\" = '".$_POST['Evt_Time']."', ".
			"\"Evt_Link\" = '".$_POST['Evt_Link']."', ".
			"\"Evt_Brief\" = '".$_POST['Evt_Brief']."', ".
			"\"Evt_Approved\" = '1' ".
			"WHERE \"Evt_ID\" = '".$_POST['Evt_ID']."'");
	if(!($result=pg_query($connection,$query))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
header("location: admin_menu.php");
?>
