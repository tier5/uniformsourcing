<?php
$appdir="../../";
require('../../Application.php');
$mydirectory="../..";
if(isset($_SESSION['perm_admin']) AND $_SESSION['perm_admin'] == "on"){
}else{
	echo "<body bgcolor=\"#FFFFFF\">";
	echo "<br><br>";
	echo "<center>";
	echo "<font face=\"arial\">";
	echo "<b>The User ".$_SESSION['firstname']." ".$_SESSION['lastname']." does not have access to this area.</b>";
	echo "</font>";
	echo "</center>";
	echo "</body>";
	exit;
}
?>
