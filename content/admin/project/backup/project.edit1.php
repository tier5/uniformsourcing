<?php
require('Application.php');
//print '<pre>';print_r($_POST);print '</pre>';
if($debug == "on"){
	require('../../header.php');
	foreach($_POST as $key=>$value) {
		if($key!="submit") { echo "$key = $value<br/>"; }
	}
}
$session_id=$_SESSION['pid'];
extract($_POST);
$arrErr = array();
if($projectName =="") {
	$arrErr[]="Enter a Project Name";
}
if($purchaseOrder =="") {
	$arrErr[]="Enter Purchase Order";
} //else {
//	//if( !($purchaseOrder*1) )
//	if($purchaseOrder=="") {
//		$arrErr[]="Enter a Purchase Order ";
//	}
//}
if($projectQuote =="") {
	$arrErr[]="Enter Project Quote";
} else {
	if( !($projectQuote*1) ) {
		$arrErr[]="Enter Project Quote in digits";
	}
}
if($quanPeople !="") {
	if($quanPeople!=0 && !($quanPeople*1) ) {
		$arrErr[]="Enter Quantity of People in digits";
	}	
}
if($styleNumber ==""){
		$arrErr[]="Enter Style ";
	}	
if($targetPriceunit !="") {
	if($targetPriceunit!=0 && !($targetPriceunit*1) ) {
		$arrErr[]="Enter Target Price per unit in digits";
	}	
}
if($targetRetailPrice !="") {
	if($targetRetailPrice!=0 && !($targetRetailPrice*1) ) {
		$arrErr[]="Enter Target Retail Price in digits";
	}	
}
if($pcost =="") {
	$arrErr[]="Enter Project Cost";
} else {
	if( !($pcost*1) ) {
		$arrErr[]="Enter Project Cost in digits";
	}
}
/*if($pestimate =="") {
	$arrErr[]="Enter Project Estimated Unit cost";
} else {
	if( !($pestimate*1) ) {
		$arrErr[]="Enter Project Estimated Unit cost in digits";
	}
}
if($pcompcost =="") {
	$arrErr[]="Enter Project Completion cost";
} else {
	if( !($pcompcost*1) ) {
		$arrErr[]="Enter Project Completion cost in digits";
	}
}*/

if(count($arrErr)){
	$_SESSION['edit_err'][]=$arrErr;
	$_SESSION['edit_err'][]=$_POST;
	header('Location:project.edit.php?ID='.$pid);
	require('../../header.php');
	echo "You have to enter Project name, Purchase order and Project quote data<br>";
	echo "Please go back and put in a reasonable data for the Project to be submitted.<br />";
	echo '<a href="project.edit.php?ID='.$pid.'">Go Back to Add Project Page</a>';
	require('../../trailer.php');
	exit;
}

if($projectName && $clientID && $purchaseOrder && $projectQuote) {
	$sql="select count(*) as n from \"tbl_projects\" where \"pname\"= '$projectName' and \"cid\"='$clientID' and \"pid\" !='$pid' ";
	if(!($result_cnt=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row_cnt = pg_fetch_array($result_cnt)){
		$data_cnt=$row_cnt;
	}
	if($data_cnt['n']) {
		require('../../header.php');
		echo "You have entered same Project name,Client name data before.<br>";
		echo "Please go back and put in a different data for the Project to be submitted.<br>";
		echo '<a href="project.edit.php?ID='.$pid.'">Go Back to Edit Project Page</a>';
		require('../../trailer.php');
		exit;
	}
}
$garDescription=pg_escape_string($garDescription);
$sizeNeeded=pg_escape_string($sizeNeeded);
$typeMaterial=pg_escape_string($typeMaterial);
$styleNumber=pg_escape_string($styleNumber);
$projectComments=pg_escape_string($projectComments);
$sampleNmbr=pg_escape_string($sampleNmbr);
$color=pg_escape_string($color);
$query4="UPDATE \"tbl_projects\" "."set \"pname\" = '$projectName', ".
		 "\"eid\" = '".$_SESSION['employeeID']."', ".
		 "\"cid\" = '$clientID', ".
		 "\"pquote\" = '$projectQuote', ".
		 "\"purchaseOrder\" = '$purchaseOrder', ".
		 "\"samplesProvided\" = '$samplesProvided', ".
		 "\"embroidery\" = '$embroidery', ".
		 "\"silkScreening\" = '$silkScreening', ";		 
if($garDescription) $query4.="\"pdescription\" = '$garDescription', ";
else  $query4.="\"pdescription\" = '', ";
if($quanPeople) $query4.=" \"quanPeople\" = '$quanPeople', ";
else  $query4.="\"quanPeople\" = 0, ";
if($sizeNeeded) $query4.="\"sizeNeeded\" = '$sizeNeeded', ";
else  $query4.="\"sizeNeeded\" = 0, ";
if($color) $query4.="\"color\" = '$color', ";
else  $query4.="\"color\" = '', ";
if($typeMaterial) $query4.="\"typeMaterial\" = '$typeMaterial', ";
else  $query4.="\"typeMaterial\" = '', ";
if($styleNumber) $query4.="\"styleNumber\" = '$styleNumber', ";
else  $query4.="\"styleNumber\" = '', ";
if($targetPriceunit) $query4.="\"targetPriceunit\" = '$targetPriceunit', ";
else  $query4.="\"targetPriceunit\" = 0, ";
if($hdnimage1) $query4.="\"image1\" = '$hdnimage1', ";
if($hdnimage2) $query4.="\"image2\" = '$hdnimage2', ";
if($hdnimage3) $query4.="\"image3\" = '$hdnimage3', ";
if($hdnimage4) $query4.="\"image4\" = '$hdnimage4', ";
if($hdnimage5) $query4.="\"image5\" = '$hdnimage5', ";
if($projectComments) $query4.="\"projectComments\" = '$projectComments', ";
  else  $query4.="\"projectComments\" = '', ";
  if($pcost) $query4.="\"pcost\" = '$pcost', ";
else  $query4.="\"pcost\" = 0, ";
if($pestimate)  $query4.="\"pestimate\" = '$pestimate', ";
else  $query4.="\"pestimate\" = 0, ";
if($pcompcost)  $query4.="\"pcompcost\" = '$pcompcost', ";
else  $query4.="\"pcompcost\" = 0, ";
$query4.= " \"modifiedDate\" = '".date('U')."', ";
if($poDueDate)  $query4.="\"poDueDate\" = '$poDueDate', ";
else  $query4.="\"poDueDate\" = '', ";
if($prdctnSample)  $query4.="\"prdctnSample\" = '$prdctnSample', ";
else  $query4.="\"prdctnSample\" = '', ";
if($lpDip)  $query4.="\"lapDip\" = '$lpDip', ";
else  $query4.="\"lapDip\" = '', ";
if($etaPrdctn)  $query4.="\"etaProduction\" = '$etaPrdctn', ";
else  $query4.="\"etaProduction\" = '', ";
if($hdnimage6)  $query4.="\"pattern\" = '$hdnimage6', ";
if($hdnimage7)  $query4.="\"grading\" = '$hdnimage7', ";
if($hdnimage16)  $query4.="\"fileupld1\" = '$hdnimage16', ";
if($hdnimage17)  $query4.="\"fileupld2\" = '$hdnimage17', ";
if($hdnimage18)  $query4.="\"fileupld3\" = '$hdnimage18', ";
if($hdnimage19)  $query4.="\"fileupld4\" = '$hdnimage19', ";
if($hdnimage20)  $query4.="\"fileupld5\" = '$hdnimage20', ";
if($production)  $query4.="\"production\" = '$production', ";
else  $query4.="\"production\" = 0, ";
if($vendorID)  $query4.="\"vid\" = '$vendorID', ";
else  $query4.="\"vid\" = '', ";
if($prdctDate)  $query4.="\"prdctionDate\" = '$prdctDate', ";
else  $query4.="\"prdctionDate\" = '' ,";
if($sampleNmbr)  $query4.="\"sampleId\" = '$sampleNmbr', ";
else  $query4.="\"sampleNmbr\" = '', ";
if($targetRetailPrice) $query4.="\"targetRetailPrice\" = '$targetRetailPrice' ";
else  $query4.="\"targetRetailPrice\" = 0 ";
$query4.= " WHERE \"pid\" = '".$pid."'";
if(!($result4=pg_query($connection,$query4))){
	print("Failed query4: " . pg_last_error($connection));
	exit;
}
$sql = 'select * from "tbl_prjNotes" where pid='.$pid;
if(!($result=pg_query($connection,$sql))){
	print("Failed sql: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result))
{
		$data_notes[]=$row;
}
pg_free_result($result);
$sql ="";
for($i=0; $i< count($textAreaName); $i++)
{
	if($hdnNotesName[$i] == 0 && $textAreaName[$i] !="")
	{
		$sql="Insert into \"tbl_prjNotes\" (";
		$sql.="notes ,";
		$sql.=" pid" ;
		$sql .=", \"createdDate\"";
		$sql .=", \"createdTime\"";
		$sql .=", \"createdBy\"";
		$sql .=" )Values(";
		$sql .=" '".pg_escape_string($textAreaName[$i])."',";
		$sql .=" '".$pid."'";
		$sql .=", ".date("U");
		$sql .=", ".date("U");
		$sql .=", ".$_SESSION["employeeID"]."";
		$sql .=" )";
		if(!($result=pg_query($connection,$sql)))
		{
			print("Failed sql: " . pg_last_error($connection));
			exit;
		}
	}
}

header("Location: project.edit.php?ID=".$_SESSION['pid']);
?>