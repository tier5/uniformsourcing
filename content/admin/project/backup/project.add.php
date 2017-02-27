<?php
require('Application.php');
require('../../header.php');

function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return substr($pageURL, 0, strrpos($pageURL, "/"));
}

$query1=("SELECT \"ID\", \"clientID\", \"client\", \"active\" ".
		 "FROM \"clientDB\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER BY \"client\" ASC");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
pg_free_result($result1);
$sql='select (Max("pid")+1) as "pid" from tbl_projects ';
if(!($result_cnt=pg_query($connection,$sql))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row_cnt = pg_fetch_array($result_cnt)){
	$data_cnt=$row_cnt;
}
pg_free_result($result_cnt);

$sql = 'select id, "srID" from "tbl_sampleRequest" where status=1';
if(!($result1=pg_query($connection,$sql))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data_sample[]=$row1;
}
pg_free_result($result1);
if(! $data_cnt['pid']) { $data_cnt['pid']=1; }
$_SESSION['pid']=$data_cnt['pid'];
?>
<table width="90%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left"><input type="button" value="Back" onclick="location.href='project.list.php';" /></td>
    <td>&nbsp;</td>
  </tr>
</table>
<?php
//print '<pre>';print_r($_SESSION['add_err']);print '</pre>';
echo '<font face="arial">';
echo "<blockquote>";
echo '<font face="arial" size="+2"><b><center>Project Add</center></b></font>';
echo "<p>";
$adminURL = curPageURL();
$adminURL = substr($adminURL, 0 , strrpos($adminURL, "/")+1);
if(count($_SESSION['add_err'])){
	extract($_SESSION['add_err'][1]);
	if(count($_SESSION['add_err'][0])) {
		echo '<ul style="color:Red;margin-left:100px;">';
		echo "<li>Please Correct Following Fields</li>";
		foreach($_SESSION['add_err'][0] as $error) {
			echo "<li>".$error."</li>";
		}
		echo '</ul>';
	}
	unset($_SESSION['add_err']);
	//print '<pre>';print_r($_SESSION['add_err']);print '</pre>';
} else {
	$clientID='';
	$projectName ="";
	$purchaseOrder ="";
	$quanPeople ="";
	$garDescription ="";
	$sizeNeeded ="";
	$samplesProvided = 1;
	$styleNumber ="";
	$color ="";
	$typeMaterial ="";
	$embroidery = 1;
	$silkScreening = 1;
	$targetPriceunit ="";
	$targetRetailPrice = "";
	$projectComments ="";
	$poDueDate ="";
	$prdctnSample ="";
	$lpDip ="";
	$etaPrdctn ="";
	$projectQuote ="";
	$pcost="";
	$pestimate="";
	$pcompcost="";
	$hdnimage1 ="";
	$hdnimage2 ="";
	$hdnimage3 ="";
	$hdnimage4 ="";
	$hdnimage5 ="";
	$hdnimage6 ="";
	$hdnimage7 ="";
	$hdnimage16 ="";
	$hdnimage17 ="";
	$hdnimage18 ="";
	$hdnimage19 ="";
	$hdnimage20 ="";
	$vendorID ="";
	$production =1;
	$prdctDate ="";
	$sampleNmbr ="";
}
	$queryVendor="SELECT \"vendorID\", \"vendorName\", \"active\" ".
		 "FROM \"vendor\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER BY \"vendorName\" ASC ";
		 //echo $queryVendor;
	if(!($result=pg_query($connection,$queryVendor))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_Vendr[]=$row;
}
echo '<script type="text/javascript" src="'.$mydirectory.'/js/jquery.min.js"></script>';
echo '<script type="text/javascript" src="'.$mydirectory.'/js/jquery-ui.min.js"></script>';
echo '<script type="text/javascript" src="'.$mydirectory.'/js/ajaxfileupload.js"></script>';
echo '<script type="text/javascript" src="'.$mydirectory.'/js/projectadd.js"></script>';
?>
<script src="../../js/PopupBox.js" type="text/javascript"></script>
<script type="text/javascript">
popBoxWaitImage.src = "../../images/spinner40.gif";
popBoxRevertImage = "../../images/magminus.gif";
popBoxPopImage = "../../images/magplus.gif";
</script> 
<script type="text/javascript">
	function setVisibility(id,visible) 
	{//alert('Hello');
		if(visible==1)
		document.getElementById(id).style.display="";
		else
		document.getElementById(id).style.display="none";
	}
	function Prdtnvisible()
	{
		if(document.getElementById('production').value==1)
		{
			setVisibility('prodId',1);
		}
		else
			setVisibility('prodId',0);
	}
</script>
<center>
<div style="width:50%" id="message"></div></center>
<form action="project.add1.php" method="post" enctype="multipart/form-data" name="frmprjctAdd">
	<table border="0" width="40%" align="center">
				<tr>
					<td><input name="prjctestBtn" type="submit" value="Project Esimated Unit Cost" onmouseover="this.style.cursor = 'pointer';" onclick="javascript:return fnvalidate();" style="cursor: pointer;"></td>
					<td><input name="prdctMileBtn" type="submit" value="Production Milestones" onmouseover="this.style.cursor = 'pointer';" onclick="javascript:return fnvalidate();" style="cursor: pointer;"></td>
				</tr>
			</table>
<table align="center" width="90%">
<tr>
<td align='right'>Choose Client:</td>
<td align="left"><select name="clientID">
<?php for($i=0; $i < count($data1); $i++){
	if($clientID==$data1[$i]['ID'])
		echo '<option value="'.$data1[$i]['ID'].'" selected="selected">'.$data1[$i]['client'].'</option>';
	else 
		echo '<option value="'.$data1[$i]['ID'].'">'.$data1[$i]['client'].'</option>';
}
?> </select></td>
</tr>
<tr>
<td align='right'>Project Name:</td>
<td align="left"><input type="text" id="projectName" name="projectName" value="<?php echo $projectName;?>"/></td>
</tr>
<tr>
<td align='right'><font face="arial" color="red">*(r)</font>Purchase Order:</td>
<td align="left"><input type="text" name="purchaseOrder" id="purchaseOrder" value="<?php echo $purchaseOrder;?>"/></td>
</tr>

<tr>
<td align='right'>Purchase Order Due Date:</td>
<td align="left"><input type="text" name="poDueDate" id="poDueDate" value="<?php echo $poDueDate;?>"/></td>
</tr>

<tr>
<td align='right'><font face="arial" color="red">*(r)</font>Quantity of People:</td>
<td align="left"><input type="text" name="quanPeople" id="quanPeople" value="<?php echo $quanPeople;?>"/></td>
</tr>
<tr>
<td align='right'>Garment Description:</td>
<td align="left"><textarea wrap="physical" name="garDescription" rows="7" cols="35"><?php echo $garDescription;?></textarea></td>
</tr>
<tr>
<td align='right'>Sizes Needed:</td>
<td align="left"><input type="text" name="sizeNeeded" value="<?php echo $sizeNeeded;?>" /></td>
</tr>
<tr><td align="right" width="106">Vendor:</td> 
    <td width="544" align="left"><select name="vendorID">
      <?php
	for($i=0; $i <count($data_Vendr); $i++){
		if($vendorID==$data_Vendr[$i]['vendorID'])
			echo '<option value="'.$data_Vendr[$i]['vendorID'].'" selected="selected">'.$data_Vendr[$i]['vendorName'].'</option>';
		else 
			echo '<option value="'.$data_Vendr[$i]['vendorID'].'">'.$data_Vendr[$i]['vendorName'].'</option>';
}
?>
    </select></td>
  </tr>
<tr>
<td align='right'>Samples Provided:</td>
<td align="left"><input type="radio" value="1" id="samplesProvided" name="samplesProvided" checked="checked"   onclick="setVisibility('rowId','1');Prdtnvisible();" />&nbsp;Yes &nbsp;<input type="radio" id="samplesProvided"  name="samplesProvided" onclick="setVisibility('rowId','0');setVisibility('prodId','0');" value="0"/>&nbsp;No </td></tr>
<tr id="rowId" style="display:none; color:#FF00FF"><td>&nbsp;</td><td align="left"><input name="production" id="production" type="radio" value="1" checked="checked" onclick="setVisibility('prodId',1);document.getElementById('production').value=1;"/>Production
<input name="production" id="production" type="radio" value="0" onclick="setVisibility('prodId',0);document.getElementById('production').value=0;" />Client</td></tr>
<tr id="prodId" style="display:none; color:#FF00FF"><td>&nbsp;</td><td align="right"><table width="53%" align="left" class="prjctVenorTable">
  <tr><td align="left">Date:</td>
  <td align="left"><input type="text" name="prdctDate" id="prdctDate" 
  value="<?php echo $prdctnDate;?>"/></td>
</tr>
<tr>
	<td height="30" align="left">Sample Number: </td>
  <td align="left">
  <select name="sampleNmbr" id="sampleNmbr" style="width:50;" onchange="sampleChange();" ><option value="">---Select---</option>
<?php
$sampleIndex = "";
for($i=0; $i < count($data_sample); $i++){
	echo '<option value="'.$data_sample[$i]['id'].'">'.$data_sample[$i]['srID'].'</option>';
}
?> </select><a id="sample_a" style="display:none;" href="#" onclick="popupWindow('<?php echo $adminURL;?>samplerequest/samplerequest.edit.php?id=60');"><img width="20px" height="25px" src="<?php echo $mydirectory;?>/images/reportviewEdit.png" border="0"></a>
<?php
if($sampleIndex != "")
	echo '<input type="hidden" id="hdn_sampleNum" value="'.$data_sample[$sampleIndex]['srID'].'"/> ';
else
	echo '<input type="hidden" id="hdn_sampleNum" value="0"/> ';
?>
  </td>
</tr>
</table></td>
</tr>

<?php echo "<tr>";
echo "<td align='right'><font face=\"arial\" color=\"red\">*(r)</font>Style:</td>";
echo '<td align="left"><input type="text" name="styleNumber" id="styleNumber" value="'.$styleNumber.'"/></td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Color:</td>";
echo '<td align="left"><input type="text" name="color" value="'.$color.'" /></td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Type of Material:</td>";
echo '<td align="left"><input type="text" name="typeMaterial" value="'.$typeMaterial.'"/></td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Embroidery:</td>";
if($embroidery) 
	echo '<td align="left"><input type="radio" value="1" name="embroidery" checked="checked" />&nbsp;Yes &nbsp;<input type="radio" value="0" name="embroidery" />&nbsp;No </td>';
else
	echo '<td align="left"><input type="radio" value="1" name="embroidery" />&nbsp;Yes &nbsp;<input type="radio" value="0" name="embroidery" checked="checked" />&nbsp;No </td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Silk Screening:</td>";
if($silkScreening) 
	echo '<td align="left"><input type="radio" value="1" name="silkScreening" checked="checked" />&nbsp;Yes &nbsp;<input type="radio" value="0" name="silkScreening" />&nbsp;No </td>';
else
	echo '<td align="left"><input type="radio" value="1" name="silkScreening" />&nbsp;Yes &nbsp;<input type="radio" value="0" name="silkScreening" checked="checked" />&nbsp;No </td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Target Price per unit:</td>";
echo '<td align="left"><input type="text" name="targetPriceunit" id="targetPriceunit" value="'.$targetPriceunit.'" /></td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Target Retail Price:</td>";
echo '<td align="left"><input type="text" name="targetRetailPrice" id="targetRetailPrice" value="'.$targetRetailPrice.'" /></td>';
echo "</tr>";
echo '<tr id="loading" style="display:none;">';
echo '<td colspan="2"><img src="'.$mydirectory.'/images/loading.gif"/></td>';
echo "</tr>";
echo "<tr id=\"patternRow\">";
echo "<td align='right'>Pattern:</td>";
echo '<td align="left">';
if($hdnimage6) 
	echo '<img style="width: 129px; height: 96px;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image6" src="../../projectimages/'.$hdnimage6.'"  />';
else 
	echo '<img style="display:none;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image6" />';
echo '<input type="file" name="pattern" id="pattern" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';" name="button666" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'pattern\','.$data_cnt['pid'].',6);" />&nbsp;&nbsp;&nbsp;<a id="alink6" style="display:none;align:left" onclick="javascript:return DeleteImageRow(\'hdnimage6\',\'alink6\',\'thumb_image6\');">Delete</a></td>';
echo "</tr>";

echo "<tr>";
echo "<td align='right'>Grading:</td>";
echo '<td align="left">';
if($hdnimage7) 
	echo '<img style="width: 129px; height: 96px;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image7" src="../../projectimages/'.$hdnimage7.'"  />';
else 
	echo '<img style="display:none;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image7" />';
echo '<input type="file" name="grading" id="grading" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';" name="button777" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'grading\','.$data_cnt['pid'].',7);" />&nbsp;&nbsp;&nbsp;<a id="alink7" style="display:none;align:left" onclick="javascript:return DeleteImageRow(\'hdnimage7\',\'alink7\',\'thumb_image7\');">Delete</a></td>';
echo "</tr>";

echo "<tr>";
echo "<td align='right'>Image Upload 1:</td>";
echo '<td align="left">';
if($hdnimage1) 
	echo '<img style="width: 129px; height: 96px;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image1" src="../../projectimages/'.$hdnimage1.'"  />';
else 
	echo '<img style="display:none;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image1"/>';
echo '<input type="file" name="image1" id="image1" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';" name="button111" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'image1\','.$data_cnt['pid'].',1);" />&nbsp;&nbsp;&nbsp;<a id="alink1" style="display:none;align:left" onclick="javascript:return DeleteImageRow(\'hdnimage1\',\'alink1\',\'thumb_image1\');">Delete</a></td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Image Upload 2:</td>";
echo '<td align="left">';
if($hdnimage2) 
	echo '<img style="width: 129px; height: 96px;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image2" src="../../projectimages/'.$hdnimage2.'" />';
else 
	echo '<img style="display:none;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image2" />';
echo '<input type="file" name="image2" id="image2" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';" name="button222" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'image2\','.$data_cnt['pid'].',2);" />&nbsp;&nbsp;&nbsp;<a id="alink2" style="display:none;align:left" onclick="javascript:return DeleteImageRow(\'hdnimage2\',\'alink2\',\'thumb_image2\');">Delete</a></td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Image Upload 3:</td>";
echo '<td align="left">';
if($hdnimage3) 
	echo '<img style="width: 129px; height: 96px;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image3" src="../../projectimages/'.$hdnimage3.'" />';
else
	echo '<img style="display:none;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image3" />';
echo '<input type="file" name="image3" id="image3" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';" name="button333" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'image3\','.$data_cnt['pid'].',3);" />&nbsp;&nbsp;&nbsp;<a id="alink3" style="display:none;align:left" onclick="javascript:return DeleteImageRow(\'hdnimage3\',\'alink3\',\'thumb_image3\');">Delete</a></td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Image Upload 4:</td>";
echo '<td align="left">';
if($hdnimage4)
	echo '<img style="width: 129px; height: 96px;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image4" src="../../projectimages/'.$hdnimage4.'" />';
else 
	echo '<img style="display:none;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image4" />';
echo '<input type="file" name="image4" id="image4" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';" name="button444" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'image4\','.$data_cnt['pid'].',4);" />&nbsp;&nbsp;&nbsp;<a id="alink4" style="display:none;align:left" onclick="javascript:return DeleteImageRow(\'hdnimage4\',\'alink4\',\'thumb_image4\');">Delete</a></td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Image Upload 5:</td>";
echo '<td align="left">';
if($hdnimage5)
	echo '<img style="width: 129px; height: 96px;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image5" src="../../projectimages/'.$hdnimage5.'" />';
else
	echo '<img style="display:none;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image5" />';
echo '<input type="file" name="image5" id="image5" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';" name="button555" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'image5\','.$data_cnt['pid'].',5);" />&nbsp;&nbsp;&nbsp;<a id="alink5" style="display:none;align:left" onclick="javascript:return DeleteImageRow(\'hdnimage5\',\'alink5\',\'thumb_image5\');">Delete</a></td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'>File Upload 1:</td>";
echo '<td align="left">';
if($hdnimage16) 
	echo '<a href="../../projectimages/'.$hdnimage16.'" id="thumb_image16"/>'.$hdnimage16.'</a>';
else 
{
	?><div id="thumb_image16" style="display:none;"></div>
	
	<?php 
}
echo '<input type="file" name="image16" id="image16" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';" name="fileUpload16" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'image16\','.$data_cnt['pid'].',16);" />&nbsp;&nbsp;&nbsp;<a id="alink16" style="display:none;align:left" onclick="javascript:return DeleteImageRow(\'hdnimage16\',\'alink16\',\'thumb_image16\');">Delete</a></td>';
echo "</tr>";

echo "<tr>";
echo "<td align='right'>File Upload 2:</td>";
echo '<td align="left">';
if($hdnimage17) 
	echo '<a href="../../projectimages/'.$hdnimage17.'" id="thumb_image17"/>'.$hdnimage17.'</a>';
else 
{
	?><div id="thumb_image17" style="display:none;"></div>
	
	<?php 
}
echo '<input type="file" name="image17" id="image17" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';" name="fileUpload17" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'image17\','.$data_cnt['pid'].',17);" />&nbsp;&nbsp;&nbsp;<a id="alink17" style="display:none;align:left" onclick="javascript:return DeleteImageRow(\'hdnimage17\',\'alink17\',\'thumb_image17\');">Delete</a></td>';
echo "</tr>";

echo "<tr>";
echo "<td align='right'>File Upload 3:</td>";
echo '<td align="left">';
if($hdnimage18) 
	echo '<a href="../../projectimages/'.$hdnimage18.'" id="thumb_image17"/>'.$hdnimage18.'</a>';
else 
{
	?><div id="thumb_image18" style="display:none;"></div>
	
	<?php 
}
echo '<input type="file" name="image18" id="image18" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';" name="fileUpload18" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'image18\','.$data_cnt['pid'].',18);" />&nbsp;&nbsp;&nbsp;<a id="alink18" style="display:none;align:left" onclick="javascript:return DeleteImageRow(\'hdnimage18\',\'alink18\',\'thumb_image18\');">Delete</a></td>';
echo "</tr>";

echo "<tr>";
echo "<td align='right'>File Upload 4:</td>";
echo '<td align="left">';
if($hdnimage19) 
	echo '<a href="../../projectimages/'.$hdnimage19.'" id="thumb_image17"/>'.$hdnimage19.'</a>';
else 
{
	?><div id="thumb_image19" style="display:none;"></div>
	
	<?php 
}
echo '<input type="file" name="image19" id="image19" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';" name="fileUpload19" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'image19\','.$data_cnt['pid'].',19);" />&nbsp;&nbsp;&nbsp;<a id="alink19" style="display:none;align:left" onclick="javascript:return DeleteImageRow(\'hdnimage19\',\'alink19\',\'thumb_image19\');">Delete</a></td>';
echo "</tr>";

echo "<tr>";
echo "<td align='right'>File Upload 5:</td>";
echo '<td align="left">';
if($hdnimage20) 
	echo '<a href="../../projectimages/'.$hdnimage20.'" id="thumb_image17"/>'.$hdnimage20.'</a>';
else 
{
	?><div id="thumb_image20" style="display:none;"></div>
	
	<?php 
}

echo '<input type="file" name="image20" id="image20" /><input type="button" value="Upload" onmouseover="this.style.cursor = \'pointer\';" name="fileUpload20" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload(\'image20\','.$data_cnt['pid'].',20);" />&nbsp;&nbsp;&nbsp;<a id="alink20" style="display:none;align:left" onclick="javascript:return DeleteImageRow(\'hdnimage20\',\'alink20\',\'thumb_image20\');">Delete</a></td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Production Sample:</td>";
echo '<td align="left"><input type="text" name="prdctnSample" id="prdctnSample" value="'.$prdctnSample.'" /></td>';
echo "</tr>";

echo "<tr>";
echo "<td align='right'>Lap Dip:</td>";
echo '<td align="left"><input type="text" name="lpDip" id="lpDip" value="'.$lpDip.'" /></td>';
echo "</tr>"; 

echo "<tr>";
echo "<td align='right'>ETA Production:</td>";
echo '<td align="left">  <input type="text" name="etaPrdctn" id="etaPrdctn" value="'.$etaPrdctn.'" /></td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'><font face=\"arial\" color=\"red\">*(r)</font>Project Quote:<strong>$</strong></td>";
echo '<td align="left"><input type="text" name="projectQuote" id="projectQuote" value="'.$projectQuote.'" /></td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'><font face=\"arial\" color=\"red\">*(r)</font>Project Cost:<strong>$</strong></td>";
echo '<td align="left"> <input type="text" name="pcost" id="pcost" value="'.$pcost.'" /></td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Project Estimated Unit Cost:<strong>$</strong></td>";
echo '<td align="left"><input type="text" name="pestimate" id="pestimate" value="'.$pestimate.'" /></td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'><font face=\"arial\" color=\"red\">*(r)</font>Project Completion Cost:<strong>$</strong></td>";
echo '<td align="left"> <input type="text" name="pcompcost" id="pcompcost" value="'.$pcompcost.'" /></td>';
echo "</tr>";
echo "<tr>";
echo "<td align=\"right\">Project Notes:</td>";
?>
<td align="left" valign="top">
<table width="100%" border="0" cellspacing="0" cellpadding="0" id="prjNotes">
<tbody>
  <tr>
    <?php 
	echo "<td align=\"left\" valign=\"top\" colspan=\"4\"><a style=\"cursor:hand;cursor:pointer;\" name=\"addNotes\" id=\"addNotes\" onClick=\"javascript:popOpen('');\"><img height=\"25px\" width=\"120px\" src=\"$mydirectory/images/addNotes.gif\" alt=\"notes\"/></a></td>";
	?>
  </tr>
  </tbody>
</table>
<?php 

echo "</td></tr>
<tr><td>&nbsp;</td></tr>";
echo "<tr>";
echo '<td colspan="2"><div align="center"><input type="submit" name="submit" value=" Save Project " onclick="javascript: return fnvalidate();" />
<input type="reset" name="cancel" value=" Cancel Project " /></div></td>';
echo "</tr>";?>

</table>
<?php 

if($hdnimage1)
	echo '<input type="hidden" name="hdnimage1" id="hdnimage1" value="'.$hdnimage1.'" />';
else 
	echo '<input type="hidden" name="hdnimage1" id="hdnimage1" />';
if($hdnimage2)
	echo '<input type="hidden" name="hdnimage2" id="hdnimage2" value="'.$hdnimage2.'" />';
else 
	echo '<input type="hidden" name="hdnimage2" id="hdnimage2" />';
if($hdnimage3)
	echo '<input type="hidden" name="hdnimage3" id="hdnimage3" value="'.$hdnimage3.'" />';
else 
	echo '<input type="hidden" name="hdnimage3" id="hdnimage3" />';
if($hdnimage4)
	echo '<input type="hidden" name="hdnimage4" id="hdnimage4" value="'.$hdnimage4.'" />';
else 
	echo '<input type="hidden" name="hdnimage4" id="hdnimage4" />';
if($hdnimage5)
	echo '<input type="hidden" name="hdnimage5" id="hdnimage5" value="'.$hdnimage5.'" />';
else 
	echo '<input type="hidden" name="hdnimage5" id="hdnimage5" />';
if($hdnimage6)
	echo '<input type="hidden" name="hdnimage6" id="hdnimage6" value="'.$hdnimage6.'" />';
else 
	echo '<input type="hidden" name="hdnimage6" id="hdnimage6" />';
if($hdnimage7)
	echo '<input type="hidden" name="hdnimage7" id="hdnimage7" value="'.$hdnimage7.'" />';
else 
	echo '<input type="hidden" name="hdnimage7" id="hdnimage7" />';	
if($hdnimage16)
	echo '<input type="hidden" name="hdnimage16" id="hdnimage16" value="'.$hdnimage16.'" />';
else 
	echo '<input type="hidden" name="hdnimage16" id="hdnimage16" />';
if($hdnimage17)
	echo '<input type="hidden" name="hdnimage17" id="hdnimage17" value="'.$hdnimage17.'" />';
else 
	echo '<input type="hidden" name="hdnimage17" id="hdnimage17" />';		
if($hdnimage18)
	echo '<input type="hidden" name="hdnimage18" id="hdnimage18" value="'.$hdnimage18.'" />';
else 
	echo '<input type="hidden" name="hdnimage18" id="hdnimage18" />';	
if($hdnimage19)
	echo '<input type="hidden" name="hdnimage19" id="hdnimage19" value="'.$hdnimage19.'" />';
else 
	echo '<input type="hidden" name="hdnimage19" id="hdnimage19" />';		
if($hdnimage20)
	echo '<input type="hidden" name="hdnimage19" id="hdnimage20" value="'.$hdnimage20.'" />';
else 
	echo '<input type="hidden" name="hdnimage20" id="hdnimage20" />';
	echo '<input type="hidden" name="isEdit" id="isEdit" value="'.$isEdit.'"/>';?>
    <div id="textPop" class="popup_block">

<center><div><strong>Project Note</strong></div></center>
<table width="80%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><textarea id="notes" name="notesId" cols="60" rows="10"></textarea></td>
    </tr>
    <tr>
        <td align="center"><input type="button" name="notesSubmit" id="notesSubmit" value="Submit" onclick="javascript:onNotesSubmit('prjNotes',document.getElementById('notes'));Fade();" />
    <input type="button" id="cancel" value="Cancel" />
    </td>
  </tr>
</table>
</div>
<div id="editPop" class="popup_block">
<center><div ><strong>Project Note</strong></div></center>
<table width="80%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="100px" align="left"><strong>Notes :</strong></td>
<td>&nbsp;</td>
</tr>
  <tr>
    <td width="100"  align="left"><p id="editPopId"></p></td>
    <td width="10">&nbsp;</td>
    
  </tr>
</table>
</div>
   
</form>


<script type="text/javascript">
function popOpen(txtId)
{
	if(txtId == null || txtId == "")
	{
		var popID = 'textPop'; //Get Popup Name
	}
	else
	{
		var popID = 'editPop';
		document.getElementById('editPopId').innerHTML = document.getElementById(txtId).value;
	}
	
	popWidth = '500'; $('#' + popID).fadeIn().css({ 'width': Number( popWidth ) }).prepend('<span style="cursor:hand;cursor:pointer;" class="close"><img src="<?php echo $mydirectory;?>/images/close_pop.png" class="btn_close" title="Close Window" alt="Close" /></span>');
			
	//Define margin for center alignment (vertical + horizontal) - we add 80 to the height/width to accomodate for the padding + border width defined in the css
	var popMargTop = ($('#' + popID).height() + 80) / 2;
	var popMargLeft = ($('#' + popID).width() + 80) / 2;
			
	//Apply Margin to Popup
	$('#' + popID).css({ 
	'margin-top' : -popMargTop,
	'margin-left' : -popMargLeft
	});	
	//Fade in Background
	$('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
	$('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn(); //Fade in the fade layer 		
	}
	//Close Popups and Fade Layer
	$('span.close, #fade, #cancel').live('click', function() { //When clicking on the close or fade layer...
	$('#fade , .popup_block').fadeOut(); //fade them both out
	$('#fade').remove();
	return false;
});
	function Fade()
	{
		$('#fade , .popup_block').fadeOut();
		document.frmprjctAdd.notesId.value="";
	}
function onNotesSubmit(tableId,textId)
{
	var table = document.getElementById(tableId);
	var rowCount = table.rows.length;
	var row = table.insertRow(rowCount);
	
	var cell1 = row.insertCell(0);
	cell1.width="50px";		
	cell1.innerHTML = "Notes "+rowCount+":";	
	var cell2 = row.insertCell(1);
	cell2.width="10px";		
	cell2.innerHTML = "&nbsp;";	
	
	var noteslimit=textId.value;
	
	if(noteslimit.length > 10)
	{
	 noteslimit= noteslimit.substr(0,10);
	}
	var cell3 = row.insertCell(2);
	cell3.width="150px";		
	cell3.innerHTML = noteslimit;	
	
	var cell7 = row.insertCell(3);
	cell7.width ="150px";
	var element1 = document.createElement("a");
	element1.style.cursor ="hand";
	element1.style.cursor ="pointer";
	element1.innerHTML = "Read more...";
	element1.onclick = function(){popOpen('txtAreaId'+rowCount+'');};
	cell7.appendChild(element1);
	var cell4 = row.insertCell(4);
	cell4.width="10px";
	cell4.innerHTML = "&nbsp;";
	
	var cell5 = row.insertCell(5);
	var element2 = document.createElement("textarea");
	element2.name = "textAreaName[]";
	element2.id = 'txtAreaId'+rowCount;
	element2.value = textId.value;
	element2.style.display = "none";
	var element3 = document.createElement("input");
	element3.name = "hdnNotesName[]";
	element3.id = 'hdnNotesId'+rowCount;
	element3.value = 0;
	element3.style.display = "none";
	cell5.appendChild(element2);
	cell5.appendChild(element3);
}
if(document.getElementById('samplesProvided').value==1)
{
	setVisibility('rowId',1);
	if(document.getElementById('production').value==1)
		{
			setVisibility('prodId',1);
		}
		else
			setVisibility('prodId',0);
}
</script>
<script type="text/javascript">
<!--
function popupWindow() 
{
	var hdn = document.getElementById('hdn_sampleNum').value;
 if(hdn != "")
  var url = "<?php echo $adminURL;?>samplerequest/samplerequest.edit.php?id="+hdn;
 else
  return;
 params  = 'width='+screen.width;
 params += ', height='+screen.height;
 params += ', top=0, left=0'
 params += ', fullscreen=yes';
 params += ', scrollbars=yes';

 newwin=window.open(url,'windowname4', params);
 if (window.focus) {newwin.focus()}
 return false;
}
function sampleChange()
{
	var sel = document.getElementById('sampleNmbr');
	var hdn = document.getElementById('hdn_sampleNum');
	hdn.value = sel.options[sel.selectedIndex].value;
	if(hdn.value != "")
		document.getElementById('sample_a').style.display="";
	else
		document.getElementById('sample_a').style.display="none";
}
function DeleteImageRow(image,alink,thumb)
{
	hdnImage = document.getElementById(image) ;
	var dataString = "imgName="+hdnImage.value;
	$.ajax({
		   type: "POST",
		   url: "prjImageRemove.php",
		   data: dataString,
		   dataType: "json",
		   success:function(data)
			{
				if(data!=null)
				{
					if(data.name || data.error)
					{
						$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");
					} 
					else
					{	
						$("#message").html("<div class='errorMessage'><strong>Image Removed...</strong></div>");
						document.getElementById(alink).style.display = "none";
						document.getElementById(thumb).style.display = "none";
						hdnImage.value = "";
					}
				}
				else
				{
				$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
				}
				
			}
		});
}


</script>

<?php 
require('../../trailer.php');
?>
