<?php
require('Application.php');
if(isset($_GET['Id']) && $_GET['Id'] != "")
{
	$sql='Select "srID",detail_description from "tbl_sampleRequest" where id=\''.$_GET['Id'].'\'';
	if(!($result=pg_query($connection,$sql))){
		  print("Failed query1: " . pg_last_error($connection));
		  exit;
	}
	while($row = pg_fetch_array($result))
	{
		$data_list=$row;
	}
	pg_free_result($result);
}
else
{
	return;
}
?>
<div>
<h3><?php echo $data_list['srID'];?></h3>
      <p>
	 <?php echo $data_list['detail_description'];?>
	  </p>
      
</div>