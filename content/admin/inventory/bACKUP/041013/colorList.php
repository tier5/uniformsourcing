<?php
require('Application.php');
require('../../jsonwrapper/jsonwrapper.php');
if($debug == "on"){
	require('../../header.php');
	foreach($_POST as $key=>$value) {
		if($key!="submit") { echo "$key = $value<br/>"; }
	}
}
$type=$_GET['type'];
if(isset($_POST['styleNo']))
{
$styleNo=$_POST['styleNo'];
	$sql='select "colorId","name" from "tbl_invColor" where "styleId"='.$styleNo;
	/*$file=fopen('model.txt','w');
		fwrite($file,$type);*/
	if(!($result=pg_query($connection,$sql))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
	echo '<option value="">-----Select------</option>';
	while($row=pg_fetch_array($result))
	{
		$id=$row['colorId'];
		$data=$row['name'];
		echo '<option value="'.$id.'">'.$data.'</option>';
	}
	pg_free_result($result);
}
?>