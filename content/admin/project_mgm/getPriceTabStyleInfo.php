<?php

require('Application.php');

header('content-type:application/json;');
 //-------work done on 16052018 for making alike sequent for size parameters ordered-------//
 $sql2 = "select * from tbl_prj_style$tx  where status =1 and pid =". $_POST['pid'] . " order by prj_style_id";
	if(!($result=pg_query($connection,$sql2))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prj_style[]=$row;
	}
        
  echo json_encode($data_prj_style);
?>