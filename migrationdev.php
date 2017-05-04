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
$cSession = curl_init(); 
//step2
curl_setopt($cSession,CURLOPT_URL,"http://internal.uniformsourcing.com/test.php");
curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
curl_setopt($cSession,CURLOPT_HEADER, false); 
//step3
$result=curl_exec($cSession);
//step4
curl_close($cSession);
//step5
echo $result;
?>