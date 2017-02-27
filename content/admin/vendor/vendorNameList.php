<?php
require('Application.php');
if($_POST['clientid'])
{
	$projet_cid=$_POST['clientid'];
	
	$sql="select DISTINCT(v.\"vendorName\"),v.\"vendorID\" from vendor v inner join \"tbl_projects\" p on p.\"vid\"=v.\"vendorID\"
		  inner join \"clientDB\" c on c.\"ID\"=p.\"cid\" where p.\"cid\"='$projet_cid'";
/*$file=fopen('model.txt','w');
fwrite($file,$sql);
fclose($file);*/
	if(!($result=pg_query($connection,$sql))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
	echo '<option value="">-----Select------</option>';
	while($row=pg_fetch_array($result))
	{
		$id=$row['vendorID'];
		$data=$row['vendorName'];
		echo '<option value="'.$id.'">'.$data.'</option>';
	}
}
if($_POST['vendorid'])
{
		$ID=$_POST['vendorid'];
		$clientID=$_POST['client'];
		if($_POST['client'])
		{
			
			$innerJOIN="inner join \"clientDB\" c on c.\"ID\"=p.\"cid\"";
			$joinLINK="and p.\"cid\"=".$_POST['client'];
		}
		$sql="select p.\"pid\",p.pname from \"tbl_projects\" p ".$innerJOIN." left join vendor v on 
		v.\"vendorID\"=p.\"vid\" where p.\"vid\"='$ID' ".$joinLINK." ";
		  
		if(!($result=pg_query($connection,$sql))){
			print("Failed query: " . pg_last_error($connection));
			exit;
		}
		echo '<option value="">-----Select------</option>';
		while($row=pg_fetch_array($result))
		{
			$id=$row['pid'];
			$data=$row['pname'];
			echo '<option value="'.$id.'">'.$data.'</option>';
		}
}
?>