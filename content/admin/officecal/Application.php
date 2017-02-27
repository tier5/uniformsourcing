<?php
$appdir="../../";
require('../../Application.php');
$mydirectory="../..";
$autoapprove="Y";           // automatically approve new entries Y/N
$sendmail="N";              // send e-mail when new entry posted Y/N
$emailaddr="martin.kenney@i2net.com";   // address for e-mail
$brdrcolor="#737373";           // border color of calendar
$hdrcolor="#c0c0c0";            // background color of day header row
$extcolor="#e0e0e0";            // background color for previous and next month cells
$calcolor="#ffffff";            // background color for current month cells
$thdrcolor="#000000";           // text color of day header row
$tcalcolor="#000000";           // text color for current month cells
$postnew="Y";               // allow user to attach new document for linking Y/N
$linkto="Y";                // allow user to link event to web page Y/N
$brief="Y";             // allow user to post additional breif information about event Y/N
if(isset($_SESSION['perm_admin']) AND $_SESSION['perm_admin'] == "on"){
}else{
	require('../../header.php');
	echo "<body bgcolor=\"#FFFFFF\">";
	echo "<br><br>";
	echo "<center>";
	echo "<font face=\"arial\">";
	echo "<b>The User ".$_SESSION['firstname']." ".$_SESSION['lastname']." does not have access to this area.</b>";
	echo "</font>";
	echo "</center>";
	echo "</body>";
	require('../../trailer.php');
	exit;
}
?>
