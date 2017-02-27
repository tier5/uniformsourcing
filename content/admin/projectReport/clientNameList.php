<?php
require('Application.php');
if($_POST['vendorid'])
{
$projet_vid=$_POST['vendorid'];


$sql="select DISTINCT(c.\"client\"),c.\"ID\" from \"clientDB\" c inner join tbl_newproject as p on p.client=c.\"ID\"
      inner join tbl_prjvendor as pv on pv.pid=p.pid where pv.vid='$projet_vid'";	  
if(!($result=pg_query($connection,$sql))){
	print("Failed query: " . pg_last_error($connection));
	exit;
}
	echo '<option value="">-----Select------</option>';
	while($row=pg_fetch_array($result))
	{
		$id=$row['ID'];
		$data=$row['client'];
		echo '<option value="'.$id.'">'.$data.'</option>';
	}
}

?>