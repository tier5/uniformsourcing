<?php
require('Application.php');
if($_POST['clientid'])
{
	if($_POST['vendorid'])
		{
			$innerJOIN="left join tbl_prjvendor pv on pv.pid=p.pid left join vendor v on v.\"vendorID\"=pv.vid";
			$joinLINK="and pv.vid=".$_POST['vendorid'];
		}
	$clientid=$_POST['clientid'];
	$sql="select distinct(p.projectname),p.pid from tbl_newproject p inner join \"clientDB\" c
	on p.client=c.\"ID\" ".$innerJOIN." where p.client='$clientid' and p.status ='1' ".$joinLINK."";
//echo $sql;
/*$file=fopen('model.txt','w');
fwrite($file,$sql);
fclose($file);*/
if(!($result=pg_query($connection,$sql))){
	print("Failed query: " . pg_last_error($connection));
	exit;
}
	echo '<option value="0">-----Select-----</option>';
	while($row=pg_fetch_array($result))
	{
		$id=$row['pid'];
		$data=$row['projectname'];
		echo '<option value="'.$id.'">'.$data.'</option>';
	}
}

?>