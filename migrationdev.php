<?php

$server_URL = "http://127.0.0.1:4569";  //Server address needed for sending sam$
$db_server = "localhost";
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
curl_setopt($cSession, CURLOPT_HTTPGET, true);
curl_setopt($cSession, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Accept: application/json'
)); 
//step3
$result=curl_exec($cSession);
//step4
curl_close($cSession);
//step5
$liveset= json_decode($result);

$sql="select table_name from information_schema.tables";

$tbl_container_exists;
if(!($result=pg_query($connection,$sql)))
{
    print("Failed StyleQuery: " . pg_last_error($connection));
    exit;
}
while($row = pg_fetch_array($result))
{
	
    $tbl_container_exists[]=$row['table_name'];
}
pg_free_result($row);
echo "<pre>";
print_r($tbl_container_exists);
echo "<pre>";
print_r($liveset);
?>