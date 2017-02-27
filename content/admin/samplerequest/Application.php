<?php
$appdir="../../";
require('../../Application.php');
$mydirectory="../..";
$upload_dir			= "../../projectimages/";
if(isset($_SESSION['perm_admin']) AND $_SESSION['perm_admin'] == "on"){
}
else if(isset($_SESSION['employeeType']) AND $_SESSION['employeeType'] >0)
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
