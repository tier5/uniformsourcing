<?php
echo $_GET['table'];
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
$columset=array();
$query="insert into ".$_GET['table']." (";
	foreach ($liveset as $key => $value) {
		if($key==0){
			$columset[]=$value->column_name;
		$query.=$value->column_name;	
		}else{
			$columset[]=$value->column_name;
		$query.=",".$value->column_name;		
		}
	}
	$query.=")";
echo "<pre>";
echo $query;
$cSessionone = curl_init(); 
$urlone="http://internal.uniformsourcing.com/gettable_data.php?table=".$_GET['table'];
curl_setopt($cSessionone,CURLOPT_URL,$urlone);
curl_setopt($cSessionone,CURLOPT_RETURNTRANSFER,true);
curl_setopt($cSessionone, CURLOPT_HTTPGET, true);
curl_setopt($cSessionone, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Accept: application/json'
)); 
//step3
$resultone=curl_exec($cSessionone);
//step4
curl_close($cSessionone);
//step5
$livesetone= json_decode($resultone);
print_r($livesetone);
$vinsertedvalue="VALUES ";
foreach ($livesetone as $livekey => $livevalue) {
	foreach ($columset as $columkey => $columvalue) {
		$execol=count($columset)-1;
		if($columkey==0){
			$vinsertedvalue.="('".$livevalue->$columvalue."'";
		}elseif ($execol==$columkey) {
			$vinsertedvalue.=",'".$livevalue->$columvalue."'), ";
		}
		else{
			$vinsertedvalue.=",'".$livevalue->$columvalue."'";
		}
	}
}
$vinsertedvalue=rtrim($vinsertedvalue,', ');

print_r($query.$vinsertedvalue);
$newQuery=$query.$vinsertedvalue;
if(!($result=pg_query($connection,$newQuery))){
			$return_arr['error'] = pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		}
		pg_free_result($result);
?>