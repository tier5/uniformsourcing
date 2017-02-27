<?php
$appdir="../../";
require('../../Application.php');
$mydirectory="../..";
$upload_dir = "$mydirectory/uploadFiles/project_mgm/";
if($_SESSION['employeeID']== 2|| $_SESSION['employeeID']==34)
{}
else{
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