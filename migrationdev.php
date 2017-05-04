<?php

$server_URL = "http://internal.uniformsourcing.com";  //Server address needed f$
//$db_server = "localhost";
$db_server = "74.80.222.58";
$db_name = "php_intranet_uniformsourcing";                          // database$
$db_uname= "globaluniformuser";                              // username to con$
$db_pass= "globaluniformpassword";   
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
?>