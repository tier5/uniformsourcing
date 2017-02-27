<?php
require('Application.php');
if($_POST['vendorid'])
{
$projet_vid=$_POST['vendorid'];


$sql="select DISTINCT(c.\"client\"),c.\"ID\" from \"clientDB\" c inner join \"tbl_projects\" p on p.\"cid\"=c.\"ID\"
      inner join  vendor v on v.\"vendorID\"=p.\"vid\" where p.\"vid\"='$projet_vid'";
	  
	 /* $sql="select DISTINCT(v.\"vendorName\"),v.\"vendorID\" from vendor v inner join \"tbl_projects\" p on p.\"vid\"=v.\"vendorID\"
      inner join \"clientDB\" c on c.\"ID\"=p.\"cid\" where p.\"cid\"='$projet_cid'";*/
/*$file=fopen('model.txt','w');
fwrite($file,$sql);
fclose($file);
*/if(!($result=pg_query($connection,$sql))){
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