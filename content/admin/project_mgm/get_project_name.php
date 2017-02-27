<?php
require('Application.php');

extract($_POST);
$ret=array();
	$ret['project']='';
	$sql="select * from tbl_quote  where qid =".$project_name;
	//$sql="select reg.region,reg.rid from \"employeeDB\" as emp left join tbl_region as reg on reg.rid=emp.region where \"employeeID\" =".$merch_1;
//echo $sql;
if(!($result=pg_query($connection,$sql))){
	print("Failed query: " . pg_last_error($connection));
	exit;
}

	$row=pg_fetch_array($result);
	
	$ret['project']=$row['project_name'];
	$ret['qid']=$row['qid'];
	
	pg_free_result($result);


header('Content-type: application/json'); 
echo json_encode($ret);
?>