<?php
echo $_GET['table'];
$cSession = curl_init(); 
//step2
$url="http://internal.uniformsourcing.com/getstructure.php?table=".$_GET['table'];
curl_setopt($cSession,CURLOPT_URL,$url);
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
print_r($liveset);

?>