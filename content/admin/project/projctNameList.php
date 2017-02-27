<?php
require('Application.php');
if($_POST['clientid'])
{
$clientid=$_POST['clientid'];
$sql="select p.*,c.* from \"tbl_projects\" p inner join \"clientDB\" c on 	       p.\"cid\"=c.\"ID\" where p.\"cid\"='$clientid' ";
//echo $sql;
if(!($result=pg_query($connection,$sql))){
	print("Failed query: " . pg_last_error($connection));
	exit;
}
	echo '<option value="">Select</option>';
	while($row=pg_fetch_array($result))
	{
		$id=$row['pid'];
		$data=$row['pname'];
		echo '<option value="'.$id.'">'.$data.'</option>';
	}
}

?>