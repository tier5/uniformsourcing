<?php
require('Application.php');
require('../../jsonwrapper/jsonwrapper.php');
if($debug == "on"){
	require('../../header.php');
	foreach($_POST as $key=>$value) {
		if($key!="submit") { echo "$key = $value<br/>"; }
	}
}
$search="";
$location=$_POST['location'];
$scale=$_POST['scale'];
$sex=$_POST['sex'];
$garment=$_POST['garment'];
$fabric=$_POST['fabric'];
$scale=$_POST['scale'];
$client=$_POST['client'];
if($location>0)
{
	$search.=' and "locationIds" ILIKE \'%'.$location.'%\'';
}
if($scale>0)
{
	$search.=' and "scaleNameId"='.$scale;
}
if($sex!="")
{
	$search.=' and sex=\''.$sex.'\'';
}
if($garment>0)
{
	$search.=' and "garmentId"='.$garment;
}
if($fabric>0)
{
	$search.=' and "fabricId"='.$fabric;
}
if($client>0)
{
	$search.=' and "clientId"='.$client;
}
	$sql='select "styleId","styleNumber" from "tbl_invStyle" where "isActive"=1'.$search;
	if(!($result=pg_query($connection,$sql))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
	echo '<option value="">-----Select------</option>';
	while($row=pg_fetch_array($result))
	{
		$id=$row['styleId'];
		$data=$row['styleNumber'];
		echo '<option value="'.$id.'">'.$data.'</option>';
	}
	pg_free_result($result);
?>