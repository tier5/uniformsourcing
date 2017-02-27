<?php
require('Application.php');
//print '<pre>';print_r($_POST);print '</pre>';
if($debug == "on"){
	require('../../header.php');
	foreach($_POST as $key=>$value) {
		if($key!="submit") { echo "$key = $value<br/>"; }
	}
	require('../../trailer.php');
}
extract($_POST);
$_SESSION['pid']=$pid;
$styleNumber=pg_escape_string($styleNumber);
$typeMaterial=pg_escape_string($typeMaterial);
$projectComments=pg_escape_string($projectComments);
$query4="UPDATE \"tbl_projects\" "."set \"modifiedDate\" = '".date('U')."' ";
if($typeMaterial) $query4.=",\"typeMaterial\" = '$typeMaterial' ";
else  $query4.=",\"typeMaterial\" = '' ";
if($styleNumber) $query4.=",\"styleNumber\" = '$styleNumber' ";
else  $query4.=",\"styleNumber\" = '' ";
if($hdnimage1) $query4.=",\"image1\" = '$hdnimage1' ";
if($hdnimage2) $query4.=",\"image2\" = '$hdnimage2' ";
if($hdnimage3) $query4.=",\"image3\" = '$hdnimage3' ";
if($hdnimage4) $query4.=",\"image4\" = '$hdnimage4' ";
if($hdnimage5) $query4.=",\"image5\" = '$hdnimage5' ";
if($projectComments) $query4.=",\"projectComments\" = '$projectComments' ";
  else  $query4.=",\"projectComments\" = '' ";
if($lpDip)  $query4.=",\"lapDip\" = '$lpDip' ";
else  $query4.=",\"lapDip\" = '' ";
if($hdnimage6)  $query4.=",\"pattern\" = '$hdnimage6' ";
if($hdnimage7)  $query4.=",\"grading\" = '$hdnimage7' ";
if($hdnimage16)  $query4.=",\"fileupld1\" = '$hdnimage16' ";
if($hdnimage17)  $query4.=",\"fileupld2\" = '$hdnimage17' ";
if($hdnimage18)  $query4.=",\"fileupld3\" = '$hdnimage18' ";
if($hdnimage19)  $query4.=",\"fileupld4\" = '$hdnimage19' ";
if($hdnimage20)  $query4.=",\"fileupld5\" = '$hdnimage20' ";
$query4.= " WHERE \"pid\" = '".$pid."'";
//echo $typeMaterial;
//echo $styleNumber;
if(!($result4=pg_query($connection,$query4))){
	print("Failed query4: " . pg_last_error($connection));
	exit;
}
header("Location: editProject.php?ID=".$pid);
?>