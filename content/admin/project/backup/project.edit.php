<?php
ob_start();
require('Application.php');
if(!isset($_GET['ID'])) {
	header("location: project.list.php");
}
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
$pid = $_GET['ID'];
$_SESSION['pid']=$_GET['ID'];
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
pg_free_result($result);
$sql ='select "notesId", notes, "createdTime", e.firstname as "firstName", e.lastname as "lastName" from "tbl_prjNotes" as n inner join "employeeDB" as e on e."employeeID" =n."createdBy" where pid='.$pid.' order by "notesId"';
if(!($result=pg_query($connection,$sql))){
	print("Failed sql: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_notes[]=$row;
}
pg_free_result($result);

$sql = 'select id, "srID" from "tbl_sampleRequest" where status=1';
if(!($result1=pg_query($connection,$sql))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data_sample[]=$row1;
}
pg_free_result($result1);
//print '<pre>';print_r($_SESSION);print '</pre>';
/*
$query2=("SELECT \"ID\", \"clientID\", \"client\", \"active\" ".
		 "FROM \"clientDB\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER BY \"client\" ASC");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2)){
	$data2[]=$row2;
}*/
if(! $data_cnt['pid']) { $data_cnt['pid']=$_GET['ID']; }
$query3="SELECT * ".
		 "FROM \"tbl_projects\" ".
		 "WHERE  \"pid\" =".$_GET['ID'];
if(!($result3=pg_query($connection,$query3))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row3 = pg_fetch_array($result3)){
	$data3=$row3;
}
$length=strlen($data_cnt['pid'])+9;
$adminURL = curPageURL();
$adminURL = substr($adminURL, 0 , strrpos($adminURL, "/")+1);
?>
<table width="90%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left"><input type="button" value="Back" onclick="location.href='project.list.php';" /></td>
    <td>&nbsp;</td>
  </tr>
</table>
<font face="arial">
<blockquote>
<font face="arial" size="+2"><b><center>Project Edit</center></b></font>
</blockquote>
</font>

<?php
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
	{
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
<div align="center" style="width:50%" id="message"></div></center>
<table border="0" width=\"40%" align="center">
	<tr><td align="center"><input type="button" value="Project Esimated Unit Cost" onmouseover="this.style.cursor = 'pointer';" onclick="javascript:location.href='projectEstimatedUnitCost.php'" style="cursor: pointer;" /></td>
	<td align="center"><input type="button" value="Production Milestones" onmouseover="this.style.cursor = 'pointer';" onclick="javascript:location.href='ProductionMilestone.php'" /></td>
	</tr>
</table>
<?php 
if(count($_SESSION['edit_err'])){
	//print '<pre>';print_r($_SESSION['edit_err']);print '</pre>';
	@ extract($_SESSION['edit_err'][1]);
	if(count($_SESSION['edit_err'][0])) {
		echo '<ul style="color:Red;margin-left:100px;">';
		echo "<li>Please Correct Following Fields</li>";
		foreach($_SESSION['edit_err'][0] as $error) {
			echo "<li>".$error."</li>";
		}
		echo '</ul>';
	}
	unset($_SESSION['edit_err']);
	//unset($_SESSION['edit_err']);
	//print '<pre>';print_r($_SESSION['add_err']);print '</pre>';
} 
echo '<form action="project.edit1.php" method="post" enctype="multipart/form-data" name="frmprjEdit">';
echo '<table align="center">';
echo "<tr>";
echo "<td align='right'>Choose Client:</td>";
echo '<td align="left"><select name="clientID">';
for($i=0; $i < count($data1); $i++){
	if($data3['cid']==$data1[$i]['ID'])
		echo '<option value="'.$data1[$i]['ID'].'" selected="selected">'.$data1[$i]['client'].'</option>';
	else 
		echo '<option value="'.$data1[$i]['ID'].'">'.$data1[$i]['client'].'</option>';
}
echo "</select></td>";
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Project Name:</td>";
echo '<td align="left"><input type="text" id="projectName" name="projectName" value="'.$data3['pname'].'" /></td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Purchase Order:</td>";
echo '<td align="left"><input type="text" name="purchaseOrder" id="purchaseOrder" value="'.$data3['purchaseOrder'].'" /></td>';
echo "</tr>";

echo "<tr>";
echo "<td align='right'>Purchase Order Due Date:</td>";
echo '<td align="left"><input type="text" name="poDueDate" id="poDueDate" value="'.$data3['poDueDate'].'" /></td>';
echo "</tr>";

echo "<tr>";
echo "<td align='right'>Quantity of People:</td>";
echo '<td align="left"><input type="text" name="quanPeople" id="quanPeople" value="'.$data3['quanPeople'].'" /></td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Garment Description:</td>";
echo '<td align="left"><textarea wrap="physical" name="garDescription" rows="7" cols="35">'.$data3['pdescription'].'</textarea></td>';
echo "</tr>";
echo "<tr>";
$sizeNeeded=str_replace("\"","&quot;",$data3['sizeNeeded']);
echo "<td align='right'>Sizes Needed:</td>";
echo "<td align=\"left\"><input type=\"text\" name=\"sizeNeeded\" value=\"".$sizeNeeded."\" /></td>";
echo "</tr>";
?>
 <tr><td align="right" width="106">Vendor:</td> 
<?php 
   echo '<td width="544" align="left"><select name="vendorID">';     
	for($i=0; $i <count($data_Vendr); $i++)
	{
		if($data3['vid']==$data_Vendr[$i]['vendorID'])
		echo '<option value="'.$data_Vendr[$i]['vendorID'].'"
		 selected="selected">'.$data_Vendr[$i]['vendorName'].'</option>';
		else 
			echo '<option value="'.$data_Vendr[$i]['vendorID'].'">'.$data_Vendr[$i]['vendorName'].'</option>';
    }
?>
    </select></td>
  </tr>
<?php
echo "<tr>";
echo "<td align='right'>Samples Provided:</td>";
if($data3['samplesProvided'])
	echo '<td align="left"><input type="radio" value="1" id="samplesProvided" name="samplesProvided" checked="checked" onclick="setVisibility(\'rowId\',\'1\');Prdtnvisible();" />&nbsp;Yes &nbsp;<input type="radio" value="0" id="samplesProvided" name="samplesProvided" onclick="setVisibility(\'rowId\',\'0\');setVisibility(\'prodId\',\'0\');" />&nbsp;No </td>';
else
	echo '<td align="left"><input type="radio" value="1"  id="samplesProvided" name="samplesProvided" onclick="setVisibility(\'rowId\',\'1\');Prdtnvisible();"/>&nbsp;Yes &nbsp;<input type="radio" value="0" id="samplesProvided" name="samplesProvided" checked="checked"  onclick="setVisibility(\'rowId\',\'0\');setVisibility(\'prodId\',\'0\');"/>&nbsp;No </td>';
echo "</tr>";
echo '<tr id="rowId" style="display:none; color:#FF00FF">';echo '<td>&nbsp;</td>';
if($data3['production'])
echo '<td align="left"><input name="production" id="production" type="radio" value="1" checked="checked" onclick="setVisibility(\'prodId\',\'1\');document.getElementById(\'production\').value=1;"/>Production
<input name="production" id="production" type="radio" value="0" 
onclick="setVisibility(\'prodId\',\'0\');document.getElementById(\'production\').value=0;" />Client</td>';
else
echo '<td align="left"><input name="production" id="production" type="radio" value="1" onclick="setVisibility(\'prodId\',\'1\'); document.getElementById(\'production\').value=1;"/>Production
<input name="production" id="production" type="radio" value="0" checked="checked" 
onclick="setVisibility(\'prodId\',\'0\');document.getElementById(\'production\').value=0;" />Client</td>';
echo '</tr>';
?>
<tr id="prodId" style="display:none; color:#FF00FF"><td>&nbsp;</td><td align="right"><table width="53%" align="left" class="prjctVenorTable">
 <tr><td align="left">Date:</td>
  <td align="left"><input type="text" name="prdctDate" id="prdctDate" 
                   value="<?php echo $data3['prdctionDate'];?>"/></td>
</tr>
<tr>
	<td align="left">Sample Number:  </td><td align="left">
   <select name="sampleNmbr" id="sampleNmbr" style="width:50;" onchange="sampleChange();" ><option value="">---Select---</option>
<?php
$sampleIndex = "";
for($i=0; $i < count($data_sample); $i++){
	if($data3['sampleId']==$data_sample[$i]['id'])
	{
		echo '<option value="'.$data_sample[$i]['id'].'" selected="selected">'.$data_sample[$i]['srID'].'</option>';
		$sampleIndex = $i;
	}
	else 
		echo '<option value="'.$data_sample[$i]['id'].'">'.$data_sample[$i]['srID'].'</option>';
}
?> </select><a id="sample_a" <?php if($sampleIndex == ""){?> style="display:none;" <?php } ?> href="#" onclick="popupWindow('<?php echo $adminURL;?>samplerequest/samplerequest.edit.php?id=60');"><img width="20px" height="25px" src="<?php echo $mydirectory;?>/images/reportviewEdit.png" border="0"></a>
<?php
if($sampleIndex != "")
	echo '<input type="hidden" id="hdn_sampleNum" value="'.$data_sample[$sampleIndex]['id'].'"/> ';
else
	echo '<input type="hidden" id="hdn_sampleNum" value="0"/> ';
?>
</td>
</tr>
</table></td>
</tr>
<?php
echo "<tr>";
echo "<td align='right'>Style:</td>";
$styleNumber=str_replace("\"","&quot;",$data3['styleNumber']);
echo "<td align=\"left\"><input type=\"text\" name=\"styleNumber\" id=\"styleNumber\" value=\"".$styleNumber."\" /></td>";
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Color:</td>";
$color=str_replace("\"","&quot;",$data3['color']);
echo "<td align=\"left\"><input type=\"text\" name=\"color\" value=\"".$color."\" /></td>";
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Type of Material:</td>";
$typeMaterial=str_replace("\"","&quot;",$data3['typeMaterial']);
echo "<td align=\"left\"><input type=\"text\" name=\"typeMaterial\" value=\"".$typeMaterial."\" /></td>";
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Embroidery:</td>";
if($data3['embroidery']) 
	echo '<td align="left"><input type="radio" value="1" name="embroidery" checked="checked" />&nbsp;Yes &nbsp;<input type="radio" value="0" name="embroidery" />&nbsp;No </td>';
else
	echo '<td align="left"><input type="radio" value="1" name="embroidery" />&nbsp;Yes &nbsp;<input type="radio" value="0" name="embroidery" checked="checked" />&nbsp;No </td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Silk Screening:</td>";
if($data3['silkScreening']) 
	echo '<td align="left"><input type="radio" value="1" name="silkScreening" checked="checked" />&nbsp;Yes &nbsp;<input type="radio" value="0" name="silkScreening" />&nbsp;No </td>';
else
	echo '<td align="left"><input type="radio" value="1" name="silkScreening" />&nbsp;Yes &nbsp;<input type="radio" value="0" name="silkScreening" checked="checked" />&nbsp;No </td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Target Price per unit:</td>";
echo '<td align="left"><input type="text" name="targetPriceunit" id="targetPriceunit" value="'.$data3['targetPriceunit'].'" /></td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Target Retail Price:</td>";
echo '<td align="left"><input type="text" name="targetRetailPrice" id="targetRetailPrice" value="'.$data3['targetRetailPrice'].'" /></td>';
echo "</tr>";
echo '<tr id="loading" style="display:none;">';
echo '<td colspan="2"><img src="'.$mydirectory.'/images/loading.gif"/></td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Pattern:</td>";
echo '<td align="left">';

if($data3['pattern']) 
	echo '<img style="width: 129px; height: 96px;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image6" src="../../projectimages/'.$data3['pattern'].'"  />';
else 
	echo '<img style="display:none;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image6" />';
	?>
    
<input type="file" name="pattern" id="pattern" /><input type="button" value="Upload" onmouseover="this.style.cursor = 'pointer';" name="button666" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload('pattern','<?php echo $data_cnt['pid'];?>',6);" />&nbsp;&nbsp;&nbsp;<a id="alink6"<?php if($data3['pattern'] =="") {?> style="display:none;"<?php } ?>onclick="javascript:return DeleteImageRow('hdnimage6','alink6','thumb_image6',<?php echo $data_cnt['pid'];?>,'pattern');">Delete</a></td>
<?php
echo "</tr>";

echo "<tr>";
echo "<td align='right'>Grading:</td>";
echo '<td align="left">';
if($data3['grading']) 
	echo '<img style="width: 129px; height: 96px;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image7" src="../../projectimages/'.$data3['grading'].'"  />';
else 
	echo '<img style="display:none;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image7" />';
?>
<input type="file" name="grading" id="grading" /><input type="button" value="Upload" onmouseover="this.style.cursor = 'pointer';" name="button777" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload('grading','<?php echo $data_cnt['pid'];?>',7);" />&nbsp;&nbsp;&nbsp;<a id="alink7"<?php if($data3['grading'] =="") {?> style="display:none;align:left"<?php } ?>onclick="javascript:return DeleteImageRow('hdnimage7','alink7','thumb_image7',<?php echo $data_cnt['pid'];?>,'grading');">Delete</a></td>
<?php
echo "</tr>";

echo "<tr>";
echo "<td align='right'>Image Upload 1:</td>";
echo '<td align="left">';

if($data3['image1']) 		
	echo '<img id="thumb_image1" style="width: 129px; height: 96px;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" src="../../projectimages/'.$data3['image1'].'"/>';
else 
	echo '<img style="display:none;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image1" />';
?>
<input type="file" name="image1" id="image1" /><input type="button" value="Upload" onmouseover="this.style.cursor = 'pointer';" name="button111" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload('image1','<?php echo $data_cnt['pid'];?>',1);" />&nbsp;&nbsp;&nbsp;<a id="alink1"<?php if($data3['image1'] =="") {?> style="display:none;align:left"<?php } ?>onclick="javascript:return DeleteImageRow('hdnimage1','alink1','thumb_image1',<?php echo $data_cnt['pid'];?>,'image1');">Delete</a></td>
<?php
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Image Upload 2:</td>";
echo '<td align="left">';
if($data3['image2']) 
	echo '<img style="width: 129px; height: 96px;" class="PopBoxImageSmall" id="thumb_image2" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" src="../../projectimages/'.$data3['image2'].'"/>';
else 
	echo '<img  style="display:none;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image2" />';
	?>
<input type="file" name="image2" id="image2" /><input type="button" value="Upload" onmouseover="this.style.cursor = 'pointer';" name="button222" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload('image2','<?php echo $data_cnt['pid'];?>',2);" />&nbsp;&nbsp;&nbsp;<a id="alink2"<?php if($data3['image2'] =="") {?> style="display:none;align:left"<?php } ?>onclick="javascript:return DeleteImageRow('hdnimage2','alink2','thumb_image2',<?php echo $data_cnt['pid'];?>,'image2');">Delete</a></td>
<?php
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Image Upload 3:</td>";
echo '<td align="left">';
if($data3['image3']) 
	echo '<img style="width: 129px; height: 96px;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image3" src="../../projectimages/'.$data3['image3'].'" />';
else
	echo '<img style="display:none;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image3" />';
	?>
<input type="file" name="image3" id="image3" /><input type="button" value="Upload" onmouseover="this.style.cursor = 'pointer';" name="button333" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload('image3','<?php echo $data_cnt['pid'];?>',3);" />&nbsp;&nbsp;&nbsp;<a id="alink3"<?php if($data3['image3'] =="") {?> style="display:none;align:left"<?php } ?>onclick="javascript:return DeleteImageRow('hdnimage3','alink3','thumb_image3',<?php echo $data_cnt['pid'];?>,'image3');">Delete</a></td>
<?php

echo "</tr>";
echo "<tr>";
echo "<td align='right'>Image Upload 4:</td>";
echo '<td align="left">';
if($data3['image4'])
	echo '<img style="width: 129px; height: 96px;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image4" src="../../projectimages/'.$data3['image4'].'" />';
else 
	echo '<img style="display:none;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image4" />';
	?>
<input type="file" name="image4" id="image4" /><input type="button" value="Upload" onmouseover="this.style.cursor = 'pointer';" name="button444" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload('image4','<?php echo $data_cnt['pid'];?>',4);" />&nbsp;&nbsp;&nbsp;<a id="alink4"<?php if($data3['image4'] =="") {?> style="display:none;align:left"<?php } ?>onclick="javascript:return DeleteImageRow('hdnimage4','alink4','thumb_image4',<?php echo $data_cnt['pid'];?>,'image4');">Delete</a></td>
<?php
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Image Upload 5:</td>";
echo '<td align="left">';
if($data3['image5'])
	echo '<img style="width: 129px; height: 96px;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image5" src="../../projectimages/'.$data3['image5'].'" />';
else
	echo '<img style="display:none;" class="PopBoxImageSmall" onclick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" id="thumb_image5" />';
	?>
<input type="file" name="image5" id="image5" /><input type="button" value="Upload" onmouseover="this.style.cursor = 'pointer';" name="button555" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload('image5','<?php echo $data_cnt['pid'];?>',5);" />&nbsp;&nbsp;&nbsp;<a id="alink5"<?php if($data3['image5'] =="") {?> style="display:none;align:left"<?php } ?>onclick="javascript:return DeleteImageRow('hdnimage5','alink5','thumb_image5',<?php echo $data_cnt['pid'];?>,'image5');">Delete</a></td>
<?php
echo "</tr>";
echo "<tr>";
echo "<td align='right'>File Upload 1:</td>";
echo '<td align="left">';
if($data3['fileupld1'])
{
?>
	<div id="thumb_image16"><a href="../../projectimages/<?php echo $data3['fileupld1']; ?>"><?php echo substr($data3['fileupld1'],$length); ?></a></div>
<?php
}
else 
{
?>
	<div id="thumb_image16" style="display:none;"></div>
<?php
}	
?>
<input type="file" name="image16" id="image16" /><input type="button" value="Upload" onmouseover="this.style.cursor = 'pointer';" name="fileUpload16" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload('image16','<?php echo $data_cnt['pid'];?>',16);" />&nbsp;&nbsp;&nbsp;<a id="alink16"<?php if($data3['fileupld1'] =="") {?> style="display:none;align:left"<?php } ?>onclick="javascript:return DeleteImageRow('hdnimage16','alink16','thumb_image16',<?php echo $data_cnt['pid'];?>,'fileupld1');">Delete</a></td>
<?php
echo "</tr>";

echo "<tr>";
echo "<td align='right'>File Upload 2:</td>";
echo '<td align="left">';
if($data3['fileupld2'])
{
?>
	<div id="thumb_image17"><a href="../../projectimages/<?php echo $data3['fileupld2']; ?>"><?php echo substr($data3['fileupld2'],$length); ?></a></div>
<?php
}
 else 
{?>
	<div id="thumb_image17" style="display:none;"></div>
<?php
}	
?>
<input type="file" name="image17" id="image17" /><input type="button" value="Upload" onmouseover="this.style.cursor = 'pointer';" name="fileUpload17" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload('image17','<?php echo $data_cnt['pid'];?>',17);" />&nbsp;&nbsp;&nbsp;<a id="alink17"<?php if($data3['fileupld2'] =="") {?> style="display:none;align:left"<?php } ?>onclick="javascript:return DeleteImageRow('hdnimage17','alink17','thumb_image17',<?php echo $data_cnt['pid'];?>,'fileupld2');">Delete</a></td>
<?php
echo "</tr>";

echo "<tr>";
echo "<td align='right'>File Upload 3:</td>";
echo '<td align="left">';
if($data3['fileupld3'])
	{
?>
	<div id="thumb_image18"><a href="../../projectimages/<?php echo $data3['fileupld3']; ?>"><?php echo substr($data3['fileupld3'],$length); ?></a></div>
<?php
}
 else 
{?>
	<div id="thumb_image18" style="display:none;"></div>
<?php 
}
?>
<input type="file" name="image18" id="image18" /><input type="button" value="Upload" onmouseover="this.style.cursor = 'pointer';" name="fileUpload18" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload('image18','<?php echo $data_cnt['pid'];?>',18);" />&nbsp;&nbsp;&nbsp;<a id="alink18"<?php if($data3['fileupld3'] =="") {?> style="display:none;align:left"<?php } ?>onclick="javascript:return DeleteImageRow('hdnimage18','alink18','thumb_image18',<?php echo $data_cnt['pid'];?>,'fileupld3');">Delete</a></td>
<?php
echo "</tr>";

echo "<tr>";
echo "<td align='right'>File Upload 4:</td>";
echo '<td align="left">';
if($data3['fileupld4'])
	{
?>
	<div id="thumb_image19"><a href="../../projectimages/<?php echo $data3['fileupld4']; ?>"><?php echo substr($data3['fileupld4'],$length); ?></a></div>
<?php
}
 else 
{
?>
	<div id="thumb_image19" style="display:none;"></div>
<?php 
}	
?>
<input type="file" name="image19" id="image19" /><input type="button" value="Upload" onmouseover="this.style.cursor = 'pointer';" name="fileUpload19" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload('image19','<?php echo $data_cnt['pid'];?>',19);" />&nbsp;&nbsp;&nbsp;<a id="alink19"<?php if($data3['fileupld4'] =="") {?> style="display:none;align:left"<?php } ?>onclick="javascript:return DeleteImageRow('hdnimage19','alink19','thumb_image19',<?php echo $data_cnt['pid'];?>,'fileupld4');">Delete</a></td>
<?php

echo "</tr>";

echo "<tr>";
echo "<td align='right'>File Upload 5:</td>";
echo '<td align="left">';

if($data3['fileupld5'])
	{
?>
	<div id="thumb_image20"><a href="../../projectimages/<?php echo $data3['fileupld5']; ?>"><?php echo substr($data3['fileupld5'],$length); ?></a></div>
<?php
}
 else 
{?>
	<div id="thumb_image20" style="display:none;"></div>
<?php
}	
?>
<input type="file" name="image20" id="image20" /><input type="button" value="Upload" onmouseover="this.style.cursor = 'pointer';" name="fileUpload20" style="cursor: pointer;" onclick="javascript:return ajaxFileUpload('image20','<?php echo $data_cnt['pid'];?>',20);" />&nbsp;&nbsp;&nbsp;<a id="alink20"<?php if($data3['fileupld5'] =="") {?> style="display:none;align:left"<?php } ?>onclick="javascript:return DeleteImageRow('hdnimage20','alink20','thumb_image20',<?php echo $data_cnt['pid'];?>,'fileupld5');">Delete</a></td>
<?php
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Production Sample:</td>";
echo '<td align="left"><input type="text" name="prdctnSample" id="prdctnSample" value="'.$data3['prdctnSample'].'" /></td>';
echo "</tr>";

echo "<tr>";
echo "<td align='right'>Lap Dip:</td>";
echo '<td align="left"><input type="text" name="lpDip" id="lpDip" value="'.$data3['lapDip'].'" /></td>';
echo "</tr>"; 

echo "<tr>";
echo "<td align='right'>ETA Production:</td>";
echo '<td align="left">  <input type="text" name="etaPrdctn" id="etaPrdctn" value="'.$data3['etaProduction'].'" /></td>';
echo "</tr>";

echo "<tr>";
echo "<td align='right'>Project Quote:<strong>$</strong></td>";
echo '<td align="left"> <input type="text" name="projectQuote" id="projectQuote" value="'.$data3['pquote'].'" /></td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Project Cost:<strong>$</strong></td>";
echo '<td align="left"> <input type="text" name="pcost" id="pcost" value="'.$data3['pcost'].'" /></td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Projected Estimated Unit Cost:<strong>$</strong></td>";
echo '<td align="left"> <input type="text" name="pestimate" id="pestimate" value="'.$data3['pestimate'].'" /></td>';
echo "</tr>";
echo "<tr>";
echo "<td align='right'>Project Completion Cost:<strong>$</strong></td>";
echo '<td align="left"> <input type="text" name="pcompcost" id="pcompcost" value="'.$data3['pcompcost'].'" /></td>';
echo "</tr>";
if($data3['projectComments'] !="")
{
?>
    <tr>
    <td>Comments :</td>
    <td><textarea wrap="physical" readonly="readonly" name="projectComments" rows="7" cols="35"><?php echo $data3['projectComments'];?></textarea></td>
    </tr>
<?php
}
?>
<tr>
<td >Project Notes:</td>

<td align="left" valign="top">
<table width="100%" border="0" cellspacing="0" cellpadding="0" id="prjNotes">
<tbody>
  <tr>

	<td align="left" valign="top" colspan="4"><a style="cursor:hand;cursor:pointer;" name="addNotes" id="addNotes" onClick="javascript:popOpen('');"><img height="25px" width="120px" src="<?php echo $mydirectory;?>/images/addNotes.gif" alt="notes"/></a></td></tr>
<?php 	 
    if($pid)
    {
        for($i=0; $i<count($data_notes); $i++)
        {
?>   
        <tr>
<?php
			$limitNotes = substr($data_notes[$i]['notes'],0,10);
			echo " <td width=\"100px\">Notes ".($i+1).": </td>";
			echo " <td >&nbsp;</td>";
            echo " <td width=\"150px\" >".$limitNotes."</td>";
			 echo " <td width=\"150px\" ><a style=\"cursor:hand;cursor:pointer;\" onclick=\"javascript:popOpen('".txtAreaId.($i+1)."', '".($i+1)."' );\">Read more...</a></td>";
			echo " <td >&nbsp;</td>";
			echo " <td ><textarea id=\"txtAreaId".($i+1)."\" name=\"textAreaName[]\" style=\"display:none\">".stripslashes($data_notes[$i]['notes'])."</textarea>
			       <input type='hidden' id=\"dateTimeId".($i+1)."\" value=\"".date("d-m-Y g:i A", $data_notes[$i]['createdTime'])."\" />
				   <input type='hidden'  id=\"hdnNotesId".($i+1)."\" name=\"hdnNotesName[]\" value=\"".$data_notes[$i]['notesId']."\" />
				   <input type='hidden' id=\"empNameId".($i+1)."\" value=\"".$data_notes[$i]['firstName']." ".$data_notes[$i]['lastName']. "\" /></td>";
?>
        </tr>
      
 <?php
        }
    }
?>
   
  </tbody>
</table>
 

</td></tr>
<tr><td>&nbsp;</td></tr>

<tr>
<?php
echo '<td colspan="2"><div align="center"><input type="submit" name="submit" value=" Save Project " onclick="javascript: return fnvalidate();" /><input type="reset" name="cancel" value=" Cancel Project " /></div></td>';
echo "</tr>";
echo "</table>";
if($data3['image1'])
	echo '<input type="hidden" name="hdnimage1" id="hdnimage1" value="'.$data3['image1'].'" />';
else 
	echo '<input type="hidden" name="hdnimage1" id="hdnimage1" />';
if($data3['image2'])
	echo '<input type="hidden" name="hdnimage2" id="hdnimage2" value="'.$data3['image2'].'" />';
else 
	echo '<input type="hidden" name="hdnimage2" id="hdnimage2" />';
if($data3['image3'])
	echo '<input type="hidden" name="hdnimage3" id="hdnimage3" value="'.$data3['image3'].'" />';
else 
	echo '<input type="hidden" name="hdnimage3" id="hdnimage3" />';
if($data3['image4'])
	echo '<input type="hidden" name="hdnimage4" id="hdnimage4" value="'.$data3['image4'].'" />';
else 
	echo '<input type="hidden" name="hdnimage4" id="hdnimage4" />';
if($data3['image5'])
	echo '<input type="hidden" name="hdnimage5" id="hdnimage5" value="'.$data3['image5'].'" />';
else 
	echo '<input type="hidden" name="hdnimage5" id="hdnimage5" />';
if($data3['pattern'])
	echo '<input type="hidden" name="hdnimage6" id="hdnimage6" value="'.$data3['pattern'].'" />';
else 
	echo '<input type="hidden" name="hdnimage6" id="hdnimage6" />';
if($data3['grading'])
	echo '<input type="hidden" name="hdnimage7" id="hdnimage7" value="'.$data3['grading'].'" />';
else 
	echo '<input type="hidden" name="hdnimage7" id="hdnimage7" />';
if($data3['fileupld1'])
	echo '<input type="hidden" name="hdnimage16" id="hdnimage16" value="'.$data3['fileupld1'].'" />';
else 
	echo '<input type="hidden" name="hdnimage16" id="hdnimage16" />';
if($data3['fileupld2'])
	echo '<input type="hidden" name="hdnimage17" id="hdnimage17" value="'.$data3['fileupld2'].'" />';
else 
	echo '<input type="hidden" name="hdnimage17" id="hdnimage17" />';
if($data3['fileupld3'])
	echo '<input type="hidden" name="hdnimage18" id="hdnimage18" value="'.$data3['fileupld3'].'" />';
else 
	echo '<input type="hidden" name="hdnimage18" id="hdnimage18" />';
if($data3['fileupld4'])
	echo '<input type="hidden" name="hdnimage19" id="hdnimage19" value="'.$data3['fileupld4'].'" />';
else 
	echo '<input type="hidden" name="hdnimage19" id="hdnimage19" />';
if($data3['fileupld5'])
	echo '<input type="hidden" name="hdnimage20" id="hdnimage20" value="'.$data3['fileupld5'].'" />';
else 
	echo '<input type="hidden" name="hdnimage20" id="hdnimage20" />';

if($data3['pid'])
	echo '<input type="hidden" name="pid" id="pid" value="'.$data3['pid'].'" />';
echo "</form>";
echo '<script>';
	if($data3['samplesProvided'])
		echo 'var smple='.$data3['samplesProvided'].';';
	else
		echo 'var smple=0;';
	if($data3['production'])
		echo 'var prdn='.$data3['production'].';';
	else
		echo 'var prdn=0;';
echo '</script>';
?>
<div id="textPop" class="popup_block">
<center><div ><strong>Project Note</strong></div></center>
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
<center><strong>Project Note</strong></center>
<table width="80%" border="0" cellspacing="0" cellpadding="0">
<tr id="tr_popEmpId" style="display:none">
<td width="100px" align="left"><strong>Added By : </strong></td><td width="5px">&nbsp;</td><td align="left" id="td_popEmpId"></td>
</tr>
<tr id="tr_popDateTimeId" style="display:none">
<td width="100px" align="left">
<strong>Added Date : </strong>
</td>
<td width="5px">&nbsp;</td>
<td align="left" id="td_popDateTimeId"></td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td width="100px" align="left"><strong>Notes : </strong></td>
<td>&nbsp;</td>
</tr>
  <tr>  	
    <td align="left" width="100"><p id="editPopId"></p></td>
    <td width="10">&nbsp;</td>
  </tr>
</table>
</div>
<script type="text/javascript">
function popOpen(txtId, rowIndex)
{
	if(txtId == null || txtId == "")
	{
		var popID = 'textPop'; //Get Popup Name
	}
	else
	{
		var popID = 'editPop';
		document.getElementById('editPopId').innerHTML =document.getElementById(txtId).value;
		if(rowIndex != null && rowIndex != "")
		{
			document.getElementById('td_popEmpId').innerHTML =document.getElementById('empNameId'+rowIndex).value;
			document.getElementById('td_popDateTimeId').innerHTML =document.getElementById('dateTimeId'+rowIndex).value;
			document.getElementById('tr_popEmpId').style.display = 'block';
			document.getElementById('tr_popDateTimeId').style.display = 'block';
		}
		else
		{
			document.getElementById('td_popEmpId').innerHTML = "";
			document.getElementById('td_popDateTimeId').innerHTML = "";
			document.getElementById('tr_popEmpId').style.display = 'none';
			document.getElementById('tr_popDateTimeId').style.display = 'none';
		}
		
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
		document.getElementById('notes').value="";
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
if(smple==1)
{
	setVisibility('rowId',1);
	if(prdn==1)
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
function DeleteImageRow(image,alink,thumb,id,name)
{
	hdnImage = document.getElementById(image) ;
	var dataString = "imgName="+hdnImage.value+"&pid="+id+"&column_name="+name;
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
ob_end_flush();
?>
