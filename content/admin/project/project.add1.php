<?php
require('Application.php');
//print '<pre>';print_r($_POST);print '</pre>';
if($debug == "on"){
	require('../../header.php');
	foreach($_POST as $key=>$value) {
		if($key!="submit") { echo "$key = $value<br/>"; }
	}
}

extract($_POST);


if($projectName && $clientID && $purchaseOrder && $projectQuote) {
	$sql="select count(*) as n from \"tbl_projects\" where \"pname\"= '$projectName' and \"cid\"='$clientID' and \"pquote\"='$projectQuote' and \"purchaseOrder\"='$purchaseOrder'";
	if(!($result_cnt=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row_cnt = pg_fetch_array($result_cnt)){
		$data_cnt=$row_cnt;
	}
	if($data_cnt['n']) {
		require('../../header.php');
		echo "You have entered same Project name,Client name,Purchase order and Project quote data before.<br>";
		echo "Please go back and put in a different data for the Project to be submitted.<br>";
		echo '<a href="project.add.php">Go Back to Add Project Page</a>';
		require('../../trailer.php');
		exit;
	}
}
$garDescription=pg_escape_string($garDescription);
$sizeNeeded=pg_escape_string($sizeNeeded);
$typeMaterial=pg_escape_string($typeMaterial);
$styleNumber=pg_escape_string($styleNumber);
$projectComments=pg_escape_string($projectComments);
$prdctnSample=pg_escape_string($prdctnSample);
$sampleNmbr=pg_escape_string($sampleNmbr);
$color=pg_escape_string($color);
$query4="INSERT INTO \"tbl_projects\" (\"pname\",\"eid\", \"popen\", \"cid\", \"pquote\",\"purchaseOrder\", \"samplesProvided\", \"embroidery\", \"silkScreening\", \"createdDate\",\"status\" ";
if($garDescription) $query4.=", \"pdescription\" ";
if($quanPeople) $query4.=", \"quanPeople\" ";
if($sizeNeeded) $query4.=", \"sizeNeeded\" ";
if($color) $query4.=", \"color\" ";
if($typeMaterial) $query4.=", \"typeMaterial\" ";
if($styleNumber) $query4.=", \"styleNumber\" ";
if($targetPriceunit) $query4.=", \"targetPriceunit\" ";
if($hdnimage1) $query4.=", \"image1\" ";
if($hdnimage2) $query4.=", \"image2\" ";
if($hdnimage3) $query4.=", \"image3\" ";
if($hdnimage4) $query4.=", \"image4\" ";
if($hdnimage5) $query4.=", \"image5\" ";
if($projectComments) $query4.=", \"projectComments\" ";
if($pcost)  $query4.=", \"pcost\" ";
if($pestimate) $query4.=", \"pestimate\" ";
if($pcompcost) $query4.=", \"pcompcost\" ";
if($poDueDate) $query4.=",\"poDueDate\" ";
if($prdctnSample) $query4.=",\"prdctnSample\" ";
if($lpDip) $query4.=",\"lapDip\" ";
if($etaPrdctn) $query4.=",\"etaProduction\" ";
if($hdnimage6) $query4.=",\"pattern\" ";
if($hdnimage7) $query4.=",\"grading\" ";
if($hdnimage16) $query4.=",\"fileupld1\" ";
if($hdnimage17) $query4.=",\"fileupld2\" ";
if($hdnimage18) $query4.=",\"fileupld3\" ";
if($hdnimage19) $query4.=",\"fileupld4\" ";
if($hdnimage20) $query4.=",\"fileupld5\" ";
if($production) $query4.=",\"production\" ";
if($vendorID) $query4.=",\"vid\" ";
if($prdctDate) $query4.=",\"prdctionDate\" ";
if($sampleNmbr != "") $query4.=",\"sampleId\" ";
if($targetRetailPrice) $query4.=", \"targetRetailPrice\" ";
$query4.=")";
$query4.=" VALUES ('$projectName', '".$_SESSION['employeeID']."', '1', '$clientID', '$projectQuote',  '$purchaseOrder','$samplesProvided', '$embroidery', '$silkScreening','".date('U')."','1'";
if($garDescription) $query4.=" ,'$garDescription' ";
if($quanPeople) $query4.=" ,'$quanPeople' ";
if($sizeNeeded) $query4.=" ,'$sizeNeeded' ";
if($color) $query4.=" ,'$color' ";
if($typeMaterial) $query4.=" ,'$typeMaterial' ";
if($styleNumber) $query4.=" ,'$styleNumber' ";
if($targetPriceunit) $query4.=" ,'$targetPriceunit' ";
if($hdnimage1) $query4.=" ,'$hdnimage1' ";
if($hdnimage2) $query4.=" ,'$hdnimage2' ";
if($hdnimage3) $query4.=" ,'$hdnimage3' ";
if($hdnimage4) $query4.=" ,'$hdnimage4' ";
if($hdnimage5) $query4.=" ,'$hdnimage5' ";
if($projectComments) $query4.=" ,'$projectComments' ";
if($pcost) $query4.=",'$pcost' ";
if($pestimate) $query4.=", '$pestimate' ";
if($pcompcost) $query4.=", '$pcompcost' ";
if($poDueDate) $query4.=",'$poDueDate'";
if($prdctnSample) $query4.=",'$prdctnSample'";
if($lpDip) $query4.=",'$lpDip'";
if($etaPrdctn) $query4.=",'$etaPrdctn'";
if($hdnimage6) $query4.=",'$hdnimage6'"; 
if($hdnimage7) $query4.=",'$hdnimage7'"; 
if($hdnimage16) $query4.=",'$hdnimage16'";
if($hdnimage17) $query4.=",'$hdnimage17'"; 
if($hdnimage18) $query4.=",'$hdnimage18'"; 
if($hdnimage19) $query4.=",'$hdnimage19'"; 
if($hdnimage20) $query4.=",'$hdnimage20'";  
if($production) $query4.=",'$production'" ;
if($vendorID) $query4.=",'$vendorID'";
if($prdctDate) $query4.=",'$prdctDate'";
if($sampleNmbr != "") $query4.=",'$sampleNmbr'";
if($targetRetailPrice) $query4.=" ,'$targetRetailPrice' ";
$query4.=")";

if(!($result4=pg_query($connection,$query4))){
	print("Failed query4: " . pg_last_error($connection));
	exit;
}
pg_free_result($result4);
$sql = 'select pid from tbl_projects where pname =\''.$projectName.'\'';
if(!($result=pg_query($connection,$sql))){
	print("Failed sql: " . pg_last_error($connection));
	exit;
}
$row=pg_fetch_array($result);
$data = $row;
pg_free_result($result);
if($data['pid'] !="")
{
	for($i=0; $i<count($textAreaName); $i++)
	{
		$sql="Insert into \"tbl_prjNotes\" (";
		if($textAreaName[$i]!="") $sql.="notes ,";
		$sql.=" pid" ;
		$sql .=", \"createdDate\"";
		$sql .=", \"createdTime\"";
		$sql .=", \"createdBy\"";
		$sql .=" )Values(";
		if($textAreaName!="") $sql .=" '$textAreaName[$i]',";
		$sql .=" '".$data['pid']."'";
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
if(isset($_POST['prdctMileBtn']))
header("location: ProductionMilestone.php?ID=".$_SESSION['pid']);
else if(isset($_POST['prjctestBtn']))
header("location: projectEstimatedUnitCost.php?ID=".$_SESSION['pid']);
else
header("location: project.list.php");
?>
