<?php
require('Application.php');

extract($_POST);


$sql="select * from tbl_quote  where client_id =". $client;
//echo $sql;
if(!($result=pg_query($connection,$sql))){
	print("Failed query: " . pg_last_error($connection));
	exit;
}
	echo '<option value="">------------SELECT-------------</option>';
	while($row=pg_fetch_array($result))
	{
		
		echo '<option value="'.$row['qid'].'">'.$row['po_number'].' - '.$row['project_name'].'</option>';
	}
	pg_free_result($result);


?>
