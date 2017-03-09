<?php
//error_reporting(E_ALL); ini_set('display_errors', '1'); 
$server_URL = "http://127.0.0.1:4569";  //Server address needed for sending sample request email 
$db_server = "localhost";
$db_name = "php_intranet_uniformsourcing";                          // database name
$db_uname= "globaluniformuser";                              // username to connect to database
$db_pass= "globaluniformpassword";                                // password of username to connecto to database
$debug="off";                                   // set to on for the little debug code i have set on/off
$PHPLIBDIR="/var/www/html/phplib/";               // base dir for getting libs
$PHPLIB=$PHPLIBDIR;
$JSONLIB=$PHPLIBDIR."jsonwrapper/";     
// DB connection stuff
//$connection = mysql_connect($db_server, $db_uname, $db_pass)
//or die(mysql_error());
//$db = mysql_select_db($db_name, $connection)
//or die(mysql_error());
$isMailServer ="false";
$mailServerAddress = "colomx.i2net.com";// if isMailServer is false please specify the mail server address (ex. mail.i2net.com)
$account_emailid="accounting@uniforms.net";
try{
	$connection = pg_connect("host = $db_server ".
						 "dbname = $db_name ".
						 "user = $db_uname ".
						 "password = $db_pass");

}
catch(\Exception $e)
{
	var_dump($e->getMessage());
}

// Central variables for entire module
$compquery=("SELECT \"ID\", \"client\" ".
			"FROM \"clientDB\" ".
			"WHERE \"ID\" = '1' ");
if(!($resultcomp=pg_query($connection,$compquery))){
	print("Failed compquery: " . pg_last_error($connection));
	exit;
}
while($rowcomp = pg_fetch_array($resultcomp)){
	$datacomp[]=$rowcomp;
}
	$compname="".$datacomp[0]['client']."";			// company name
?>
