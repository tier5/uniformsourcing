<?php
require('Application.php');
if(isset($_GET['Id']) && $_GET['Id'] != "")
{
	$sql='Select sample_id_val, detail_description from tbl_sample_database where sample_id=\''.$_GET['Id'].'\'';
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
<h3><?php echo $data_list['sample_id_val'];?></h3>
      <p>
	 <?php echo $data_list['detail_description'];?>
	  </p>
      
</div>