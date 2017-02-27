<?php
require('Application.php');
require('../../header.php');
$redirect_page =0;
if(isset($_GET['pge']))
{
	$redirect_page = 1;
}
$isEdit = 0;
$selectedtab = 0;
$pid= 0;
$patternId = 0;
$gradientId = 0;
$purchaseId = 0;
$sampleId = 0;
$pricingId = 0;
$elementId = 0;
if(isset($_GET['id']))
{
	$isEdit = 1;
	$pid = $_GET['id'];
	
	$sql = "Select * from tbl_newproject where status =1 and pid = $pid";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prj=$row;
	}
	pg_free_result($result);
	
	$sql = "Select * from tbl_prjpurchase where status =1 and pid = $pid";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prjPurchase =$row;
	}
	if( $data_prjPurchase['purchaseId'] !="")
	$purchaseId = $data_prjPurchase['purchaseId'];
	pg_free_result($result);
		
	$sql = "select tbl_vendorid,pid,vid,vendor.\"vendorName\" from tbl_prjvendor inner join vendor on vendor.\"vendorID\"=tbl_prjvendor.vid where status =1 and pid = $pid";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prjVendor[]=$row;
	}
	pg_free_result($result);
	
	$sql = "select * from tbl_prjshipping_carrier where status =1 and pid = $pid ";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prj_shipping[]=$row;
	}
	pg_free_result($result);
	
	
	$sql = "select * from tbl_prjshipping_track where status =1 and pid = $pid";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prj_track[]=$row;
	}
	pg_free_result($result);
	
	
	$sql = "select * from tbl_prjshipping_shippedon where status =1 and pid = $pid";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prj_shipping_on[]=$row;
	}
	pg_free_result($result);
	
	
	$sql = "select * from tbl_prjshipping_notes where status =1 and pid = $pid";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prj_shipping_notes[]=$row;
	}
	pg_free_result($result);
	
	
	$sql = "Select * from tbl_prjsample where status =1 and pid = $pid";	
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prjSample =$row;
	}
	if($data_prjSample['sampleId']!="")
	$sampleId = $data_prjSample['sampleId'];
	pg_free_result($result);
	
	$sql = "Select * from tbl_prjpricing where status =1 and pid = $pid";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prjPricing =$row;
	}
	if($data_prjPricing['pricingId']!="")
	$pricingId = $data_prjPricing['pricingId'];
	pg_free_result($result);
	
	$sql = "Select * from \"projectEstimatedUnitCost\" where pid = $pid";	
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prj_estimate =$row;
	}
	if($data_prj_estimate['prj_estimate_id']!="")
	$estimate_id = $data_prj_estimate['prj_estimate_id'];
	pg_free_result($result);
	
	$sql = "Select * from tbl_prmilestone where pid = $pid and status = 1";	
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prj_milestone = $row;
	}
	if($data_prj_milestone['id']!="")
	$milestone_id = $data_prj_milestone['id'];
	pg_free_result($result);
	
	$sql = "Select prj_element_id from tbl_prj_elements where status =1 and pid = $pid";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prj_element_all[]  =$row;
	}
	pg_free_result($result);
	if($data_prj_element_all[(count($data_prj_element_all)-1)]['prj_element_id'] != "")
		$elementId = $data_prj_element_all[(count($data_prj_element_all)-1)]['prj_element_id'];
	
	$sql = "Select tbl_mgt_notes.*,e.firstname as \"firstName\", e.lastname as \"lastName\" from tbl_mgt_notes inner join \"employeeDB\" as e on e.\"employeeID\" =tbl_mgt_notes.\"createdBy\"  where \"isActive\" =1 and pid = $pid";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prjNotes[]  =$row;
	}
	pg_free_result($result);
	
	$sql = "Select * from tbl_prjimage_file where status =1 and pid = $pid";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prjUploads [] =$row;
	}
	
	$imageArr = array();
	$fileArr = array();
	$pattern = "";
	$gradient = "";
	for($i = 0, $img= 0, $file = 0; $i < count($data_prjUploads); $i++)
	{
		if($data_prjUploads[$i]['type'] == 'P')
		{
			$patternId = $data_prjUploads[$i]['prjimageId'];
			$pattern = stripslashes($data_prjUploads[$i]['file_name']);
		}
		else if($data_prjUploads[$i]['type'] == 'G')
		{
			$gradientId = $data_prjUploads[$i]['prjimageId'];
			$gradient = stripslashes($data_prjUploads[$i]['file_name']);
		}
		else if($data_prjUploads[$i]['type'] == 'I')
		{
			$imageArr[$img]['id'] = $data_prjUploads[$i]['prjimageId'];
			$imageArr[$img++]['file'] = stripslashes($data_prjUploads[$i]['file_name']);
		}
		else if($data_prjUploads[$i]['type'] == 'F')
		{
			$fileArr[$file]['id'] = $data_prjUploads[$i]['prjimageId'];
			$fileArr[$file++]['file'] = stripslashes($data_prjUploads[$i]['file_name']);
		}
	}
	pg_free_result($result);
}
$sql= 'Select "employeeID",firstname,lastname,"employeeType" from "employeeDB" as e inner join tbl_newproject as p on p.project_manager = e."employeeID" where e.active =\'yes\' ';
if(!($result=pg_query($connection,$sql))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_employee=$row;
}
pg_free_result($result);


$query1='SELECT "ID", "clientID", c."client" as client_name, "active" '.
		 'FROM "clientDB" as c inner join tbl_newproject p on p.client=c."ID"'.
		 ' WHERE p.pid='.$_GET['id'];
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1=$row1;
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

$queryVendor="SELECT \"vendorID\", \"vendorName\", \"active\" ".
		 "FROM \"vendor\" as v inner join tbl_prjvendor as pv on pv.vid = v.\"vendorID\"  ".
		 "WHERE v.\"active\" = 'yes' ";
		 //echo $queryVendor;
	if(!($result=pg_query($connection,$queryVendor))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_Vendr=$row;
}
pg_free_result($result);
$sql="SELECT element_id, elements ".
		 "FROM tbl_elements ".
		 "WHERE status = '1' ".
		 "ORDER BY elements ASC ";
	if(!($result=pg_query($connection,$sql))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_elements[]=$row;
}
pg_free_result($result);
?>

<script type="text/javascript" src="<?php echo $mydirectory;?>/js/tabcontent.js"></script>

<table width="90%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left">
   
    <input type="button" value="Back" onClick="location.href='projectReportVendor.php';" /></td>
    <td>&nbsp;</td>
  </tr>
</table>
<center>
<table width="60%"><tr><td> 
<div align="center" id="message"></div>
<img id="loading" src="<?php echo $mydirectory;?>/images/loading.gif" style="display:none;">
<input type="hidden" id="saveid" <?php if(isset($_GET['sav'])){?>value="1"<?php }else{?>value="0"<?php }?> />
</td></tr></table>
</center>
<font face="arial">
<blockquote>
<font face="arial" size="+2"><b><center>Project Add</center></b></font>
</blockquote>
</font>
<li>
<ul id="countrytabs" class="shadetabs">
<li onClick="javascript:IsSaved('0');"><a href="#" rel="country1" class="selected">Basic</a></li>
<li onClick="javascript:IsSaved('0');"><a href="#" rel="country2">Purchase</a></li>
<li onClick="javascript:IsSaved('0');"><a href="#" rel="country3">Vendor</a></li>
<li onClick="javascript:IsSaved('0');"><a href="#" rel="country4">Samples</a></li>
<li onClick="javascript:IsSaved('0');"><a href="#" rel="country8">Notes</a></li>
<li onClick="javascript:IsSaved('0');"><a href="#" rel="country7">Elements</a></li>
<li onClick="javascript:IsSaved('0');"><a href="#" rel="country10">Order & Shipping</a></li>
<li><a href="#" rel="country6">Uploads</a></li>

</ul>
<div style="clear:left"></div>
<?php 
$adminURL = curPageURL();
$adminURL = substr($adminURL, 0 , strrpos($adminURL, "/"));
$adminURL = substr($adminURL, 0 , strrpos($adminURL, "/")+1);
?>
<form id="validationForm" name="validationForm" method="post" action="">

<div style="border:1px solid gray; width:90%; margin-bottom: 1em; padding: 10px">

<br/>

<div id="country1" class="tabcontent">
<table id="tblID" cellpadding="1" cellspacing="0" border="0">
<tr>
<td align='right'>Choose Client:</td>
<td align="left"><?php echo $data1['client_name'];?></td>
</tr>
<tr>
<td align='right'>Project Manager:</td>
<td align="left">
<?php echo $data_employee['firstname'].$data_employee['lastname'];?> 
</tr>
<tr>
<td align='right'>Project Name:</td>
<td align="left"><?php echo htmlentities($data_prj['projectname']);?></td>
</tr>
<tr>
<td align='right'>Style:</td>
<td align="left"><input type="text" name="style" id="style" value="<?php echo htmlentities($data_prj['style']);?>"/></td>
</tr>
<tr>
<td align='right'>Color:</td>
<td align="left"><?php echo htmlentities($data_prj['color']);?></td>
</tr>
<tr>
<td align='right'>Type of Material:</td>
<td align="left"><input type="text" name="materialtype" value="<?php echo htmlentities($data_prj['materialtype']);?>"/>
<input type="hidden" id="pid" name="pid" value="<?php echo $pid;?>" />
<input type="hidden" name="projectname" value="<?php echo $data_prj['projectname'];?>" />

<input type="hidden" id="isRequired" name="isRequired" value="<?php echo $isRequired;?>" />
</td>
</tr>
<tr>

</table>
</div>

<div id="country2" class="tabcontent">
<table cellpadding="1" cellspacing="1" border="0">
<tr>
<td align='right'>Purchase Order:</td>
<td align="left"><?php echo htmlentities($data_prjPurchase['purchaseorder']);?></td>
</tr>
<tr>
<td align='right'>Purchase Order Due Date:</td>
<td align="left"><?php echo $data_prjPurchase['purchaseduedate'];?></td>
</tr>
<tr>
<td align='right'>Quantity of People:</td>
<td align="left"><?php echo $data_prjPurchase['qtypeople'];?></td>
</tr>
<tr>
<td align='right'>Total No of garments:</td>
<td align="left"><?php echo $data_prjPurchase['totalgarments'];?></td>
</tr>
<tr>
<td align='right'>Sizes Needed:</td>
<td align="left"><?php echo htmlentities($data_prjPurchase['sizeneeded']);?></td>
</tr>
<tr>
<td align='right'>Garment Description:</td>
<td align="left"><?php echo htmlentities($data_prjPurchase['garmentdesc']);?>
<input type="hidden" id="purchaseId" name="purchaseId" value="<?php echo $purchaseId;?>" />
</td>
</tr>
</table>
</div>

<div id="country3" class="tabcontent">
<table border="0" cellpadding="1" cellspacing="1">
<tr>
<td>
<table align="left" border="0" cellpadding="1" cellspacing="0" id="tbl_vendor">

</table>
</td>
</tr>
<tr>
<td>
<table align="left" border="0" cellpadding="1" cellspacing="0">
<tr>
<td valign="middle" align="left" colspan="2">Vendor Name: </td> <td width="20px">&nbsp;</td>
    <td align="left" valign="top" ><?php echo $data_Vendr['vendorName'];?></td>
  </tr>
</table>
</td>
</tr>
</table>
</div>

<div id="country4" class="tabcontent">
  <table cellpadding="1" cellspacing="1" border="0">
    <tr>
      <td align='right'>Samples Provided:</td>
<td><?php 
if($isEdit &&  $data_prjSample['sampleprovided']  ==0  )
{
?>
      &nbsp;No
<?php 
}
else
{
?>
      &nbsp;Yes
<?php
}
?>
</td>
<td>&nbsp;</td>
    </tr>
  <td align='right'>Embroidery:</td>
  <td align="left">
    <?php
if($data_prjSample['embroidery'] ==0)
{
?>
    
      &nbsp;Yes &nbsp;
    <?php
}
else
{ 
?>
    
      &nbsp;No
    <?php 
}
?>
</td>
  </tr>
  <tr>
    <td align='right'>Silk Screening:</td>
    <td align="left">
    <?php 
if($data_prjSample['silkscreening'] == 0)
{
?>
  &nbsp;Yes &nbsp;
    
    <?php
}
else
{
?>
 &nbsp;No 
    <?php
}
?>
   </td>
  </tr>
  <tr>
    <td align='right'>ETA Production:</td>
    <td align="left"><?php echo $data_prjSample['etaproduction'];?>
      <input type="hidden" id="sampleId" name="sampleId" value="<?php echo $sampleId;?>"/></td>
  </tr>
  </table>
</div>


<div id="country5" class="tabcontent">
<table cellpadding="1" cellspacing="1" border="0">
<!--New rows added for pt invoice,taxes etc-->
<tr>
<td align='right'>Job Costing:</td>
<td align="left"> <input type="button" value="Click for job costing" onClick="javascript:popOpen(0,'PEC');"  /></td>
</tr>
<tr>
<td align='right'>Project Estimated Unit Cost:<strong>$</strong></td>
<td align="left"><input type="text" name="pestimate" id="pestimate" readonly="readonly" value="" /></td>
</tr>
</table>
<div id="prj_estimatecost" class="popup_block">

<table width="100%">
    <tr>
      <td align="center"><font size="5">P</font><font size="5">roject Estimated Unit Cost
            </font>
        <table width="90%" border="0" cellspacing="1" cellpadding="1">
            <tr>
              <td height="25" align="right" valign="top">Pattern Set-Up:</td>
              <td width="10">&nbsp;</td>
              <td align="left" valign="top"><input id="ptrnsetup" name="ptrnsetup" type="text" class="textBox" value="<?php echo $data_prj_estimate['ptrnsetup'];?>" /></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Grading Set-Up:</td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="grdngsetup" name="grdngsetup" type="text" class="textBox" value="<?php echo $data_prj_estimate['grdngsetup'];?>" /></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Sample Fee Set Up:</td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="smplefeesetup" name="smplefeesetup" type="text" class="textBox" value="<?php echo $data_prj_estimate['smplefeesetup'];?>" /></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Fabric:</td>
              <td>&nbsp;</td>
            <td align="left" valign="top"><input id="fabric" name="fabric" type="text" class="textBox" value="<?php echo $data_prj_estimate['fabric'];?>"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Trim:</td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="trimfee" name="trimfee" type="text" class="textBox" value="<?php echo $data_prj_estimate['trimfee'];?>"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Labor:</td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="labour" name="labour" type="text" class="textBox" value="<?php echo $data_prj_estimate['labour'];?>"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Duty: </td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="duty" name="duty" type="text" class="textBox" value="<?php echo $data_prj_estimate['duty'];?>"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Freight:</td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="frieght" name="frieght" type="text" class="textBox" value="<?php echo $data_prj_estimate['frieght'];?>"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Other: </td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="other" name="other" type="text" class="textBox" value="<?php echo $data_prj_estimate['other'];?>"/></td>
            </tr>
            <tr>
              <td height="25" align="right"><input type="hidden" name="prj_estimate_id" value="<?php echo $estimate_id;?>" /><input name="sbtbutton"  id="sbtbutton" type="button" onMouseOver="this.style.cursor = 'pointer';" value="Save" onclick="javascript:FillEstimatedCost();Fade();" /></td>
              <td>&nbsp;</td>
              <td align="left"><input name="button2" type="reset" onMouseOver="this.style.cursor = 'pointer';"  value="Cancel" onclick="javascript:ClearAllFields();" /></td>
            </tr>
      </table></td>
    </tr>
  </table>
</div>



</div>

<div id="country9" class="tabcontent">
<table width="100%">
    <tr>
      <td align="center">
          <table width="80%" border="0" align="center" cellpadding="1" cellspacing="1">
            <tr>
              <td height="25" align="right" valign="top">Lap Dip:</td>
              <td width="10">&nbsp;</td>
              <td align="left" valign="top"><input id="lapDip" name="lapDip" type="text" class="textBox" readonly="true" value="<?php echo $data_prj_milestone['lapdip'];?>"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Lap Dip Approval:</td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="lapDipApprvl" name="lapDipApprvl" type="ptrnsetup" class="textBox" value="<?php echo $data_prj_milestone['lapdipapproval'];?>" /></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Estimated Fabric Delivery Date:</td>
              <td>&nbsp;</td>
            <td align="left" valign="top"><input id="estDelvry" name="estDelvry" type="text" class="textBox" value="<?php echo $data_prj_milestone['estdelivery'];?>"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Production Sample: </td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="pdctSampl" name="pdctSampl" type="text" class="textBox" value="<?php echo $data_prj_milestone['prdtnsample'];?>"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Production Sample Approval: </td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="pdctSamplApprvl" name="pdctSamplApprvl" type="text" class="textBox" value="<?php echo $data_prj_milestone['prdtnsampleapprval'];?>"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Sizing Line:  </td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="szngLine" name="szngLine" type="text" class="textBox" value="<?php echo $data_prj_milestone['szngline'];?>"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Production target Delivery: </td>
              <td>&nbsp;</td>
              <td align="left" valign="top"><input id="prdctnTrgtDelvry" name="prdctnTrgtDelvry" type="text" class="textBox" value="<?php echo $data_prj_milestone['prdtntrgtdelvry'];?>"/><input type="hidden" id="milestone_id" name="milestone_id" value="<?php echo $milestone_id;?>"/></td>
            </tr>
      </table></td>
    </tr>
  </table>
</div>

<div id="country10" class="tabcontent">

<table width="80%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
           
            <tr>
              <td width="50%" height="25" align="right">Order Placed On:</td>
              <td width="1%">&nbsp;</td>
              <td width="49%" align="left" valign="top"><?php echo $data_prj['order_placeon'];?></td>
            </tr>
             <tr>
            <td colspan="3" align="center">
            <table id="tbl_carrier" width="100%" cellpadding="0" cellspacing="0">
              <tr style="bottom:10px">
                <td width="50%" height="25" align="right" >Carrier:</td>
                <td width="1%">&nbsp;</td>
                <td align="left" valign="top" height="25px"><?php echo htmlentities($data_prj_shipping[0]['carrier']);?></td>
                 <td width="1%">&nbsp;</td>
              </tr>
              <?php 
			  if($isEdit)
			  {
				  for($i=1; $i<count($data_prj_shipping); $i++)
				  {
?>
					  <tr style="bottom:10px">
                <td width="50%" height="25" align="right" >Carrier:</td>
                <td width="1%">&nbsp;</td>
                <td align="left" valign="top" height="25px"><?php echo htmlentities( $data_prj_shipping[$i]['carrier']);?></td>
              </tr>
<?php 					  
				  }
			  }
			  ?>
            </table></td>
            </tr>
            
             <tr>
            <td colspan="3" align="center">
            <table id="tbl_track" width="100%" cellpadding="0" cellspacing="0">
              <tr style="bottom:10px">
                <td width="50%" height="25" align="right" >Tracking Number:</td>
                <td width="1%">&nbsp;</td>
                <td align="left" valign="top" height="25px"><?php echo htmlentities($data_prj_track[0]['tracking_number']);?></td>
                 <td width="1%">&nbsp;</td>
              </tr>
              <?php 
			  if($isEdit)
			  {
				  for($i=1; $i<count($data_prj_track); $i++)
				  {
?>
					  <tr style="bottom:10px">
                <td width="50%" height="25" align="right" >Tracking Number:</td>
                <td width="1%">&nbsp;</td>
                <td align="left" valign="top" height="25px"><?php echo htmlentities($data_prj_track[$i]['tracking_number']);?></td>
             
              </tr>
<?php 					  
				  }
			  }
			  ?>
            </table></td>
            </tr>
            <tr>
            <td colspan="3" align="center">
            <table id="tbl_track" width="100%" cellpadding="0" cellspacing="0">
              <tr style="bottom:10px">
                <td width="50%" height="25" align="right" >Shipped On: </td>
                <td width="1%">&nbsp;</td>
                <td align="left" valign="top" height="25px"><?php echo $data_prj_shipping_on[0]['shipped_on'];?></td>
                 <td width="1%">&nbsp;</td>
              </tr>
              <?php 
			  if($isEdit)
			  {
				  for($i=1; $i<count($data_prj_shipping_on); $i++)
				  {
?>
					  <tr style="bottom:10px">
                <td width="50%" height="25" align="right" >Shipped On: </td>
                <td width="1%">&nbsp;</td>
                <td align="left" valign="top" height="25px"><?php echo htmlentities($data_prj_shipping_on[$i]['shipped_on']);?></td>
                
              </tr>
<?php 					  
				  }
			  }
			  ?>
            </table></td>
            </tr>
            
            
            
                    <tr>
            <td colspan="3" align="center">
            <table id="tbl_track" width="100%" cellpadding="0" cellspacing="0">
              <tr style="bottom:10px">
                <td width="50%" height="25" align="right" >Notes: </td>
                <td width="1%">&nbsp;</td>
                <td align="left" valign="top" height="25px"><?php echo htmlentities($data_prj_shipping_notes[0]['ship_notes']);?></td>
                 <td width="1%">&nbsp;</td>
              </tr>
              <?php 
			  if($isEdit)
			  {
				  for($i=1; $i<count($data_prj_shipping_notes); $i++)
				  {
?>
					  <tr style="bottom:10px">
                <td width="50%" height="25" align="right" >Notes: </td>
                <td width="1%">&nbsp;</td>
                <td align="left" valign="top" height="25px"><?php echo htmlentities($data_prj_shipping_notes[$i]['ship_notes']);?></td>
              </tr>
<?php 					  
				  }
			  }
			  ?>
            </table></td>
            </tr>
      </table>
    
    </td>
  </tr>
</table>


</div>



<div id="country6" class="tabcontent">

<table width="90%" >
<tr>
<td valign="top">

<table cellpadding="1" cellspacing="1" border="0">
<tr>
<td align='right'>Pattern:</td>
<td align="left">
<input type="file" name="pattern" id="pattern" /><input type="button" value="Upload" name="prjpattern" style="cursor: pointer;" onClick="javascript:return ajaxFileUpload('pattern','P',document.getElementById('pid'),0);" /></td>
</tr>
<tr>
<td align='right'>Grading:</td>
<td align="left">
<input type="file" name="grading" id="grading" /><input type="button" value="Upload"  name="prjgrading" style="cursor: pointer;" onClick="javascript:return ajaxFileUpload('grading','G',document.getElementById('pid'),0);" />&nbsp;&nbsp;&nbsp;</td>
</tr>
<tr>
<td align='right'>Image:</td>
<td align="left">
<input type="file" name="image" id="image" /><input type="button" value="Upload" onMouseOver="this.style.cursor = 'pointer';" name="btnimage" style="cursor: pointer;" onClick="javascript:return ajaxFileUpload('image','I',document.getElementById('pid'),0);" /></td>
</tr>
<tr>
<td align='right'>File:</td>
<td align="left">
<input type="file" name="file" id="file" /><input type="button" value="Upload" onMouseOver="this.style.cursor = 'pointer';" name="btnfile" style="cursor: pointer;" onClick="javascript:return ajaxFileUpload('file','F',document.getElementById('pid'),0);" />
</td>
</tr>
</table>
</td>
<td valign="top" align="right">
<table  border="0" cellspacing="0" cellpadding="0">
<?php
if($isEdit)
{
if($pattern)
{
?>
<tr>
<td height="25">Pattern</td>
</tr>
<tr>
   <td>
 <img src="<?php echo ($upload_dir.$pattern);?>" width="101" height="89" id="thumb_image1" onClick="PopEx(this, null,  null, 0, 0, 50, 'PopBoxImageLarge');">	
   <a style="cursor:hand;cursor:pointer;" 
   onClick="javascript:return DeleteUploads('<?php echo $patternId;?>','<?php echo addslashes($pattern);?>','<?php echo $pid;?>','P','upload');"><img src="<?php echo $mydirectory;?>/images/close.png" alt="delete" />
    </a>
	</td>
      </tr>

<?php
}
if($gradient != "")
{
?>
      <tr>
        <td height="25">Grading</td>
      </tr>
      <tr><td>
	   <img src="<?php echo ($upload_dir.$gradient);?>" width="101" height="89" id="thumb_image2" onClick="PopEx(this, null,  null, 0, 0, 50, 'PopBoxImageLarge');"> 
       <a style="cursor:hand;cursor:pointer;" 
       onClick="javascript:return DeleteUploads('<?php echo $gradientId;?>','<?php echo addslashes($gradient);?>','<?php echo $pid;?>','G','upload');"><img src="<?php echo $mydirectory;?>/images/close.png" alt="delete" />
    </a>
      </td>
      </tr>
<?php 
}
?>
<?php
if(count($imageArr))
{
	for($i=0; $i<count($imageArr); $i++)
	{
?>

      <tr>
        <td height="25">Image</td>
      </tr>
      <tr>
        <td>
        
<?php
		if($imageArr[$i] != "" )
		{
?>         
        	<img src="<?php echo ($upload_dir.$imageArr[$i]['file']);?>" width="101" height="89" onClick="PopEx(this, null,  null, 0, 0, 50, 'PopBoxImageLarge');">
             <a style="cursor:hand;cursor:pointer;" onClick="javascript:return DeleteUploads('<?php echo $imageArr[$i]['id'];?>','<?php echo addslashes($imageArr[$i]['file']);?>','<?php echo $pid;?>','I','upload');"><img src="<?php echo $mydirectory;?>/images/close.png" alt="delete" />
    </a> 
<?php
		}
?>
     
        </td>
      </tr>
<?php
	}
}
?>
<?php
if(count($fileArr))
{
	for($i=0; $i<count($fileArr); $i++)
	{
?>

      <tr>
        <td height="25">Files</td>
      </tr>
      <tr>
        <td>
        
<?php
		if($fileArr[$i] != "")
		{ ?>    
			<strong><?php echo (substr($fileArr[$i]['file'], (strpos($fileArr[$i]['file'], "-")+1))); ?></strong>
            <a href="download.php?file=<?php echo $fileArr[$i]['file'];?>"><img src="<?php echo $mydirectory;?>/images/Download.png" alt="download"/></a>
             <a href="javascript:void(0);" onClick="javascript:return DeleteUploads('<?php echo $fileArr[$i]['id'];?>','<?php echo addslashes($fileArr[$i]['file']);?>','<?php echo $pid;?>','F','upload');"><img src="<?php echo $mydirectory;?>/images/close.png" alt="delete"/></a>
<?php		
        }
?>
      </td>
      </tr>
<?php
	}
}
}
?>
<input type="hidden" id="patternId" name="patternId" value="<?php echo $patternId;?>"/>

<input type="hidden" id="gradientId" name="gradientId" value="<?php echo $gradientId;?>"/>

</table>
</td>
</tr>
</table>
</div>
<input type="hidden" id="isEdit" name="isEdit" value="<?php echo $isEdit;?>" />
<div id="country7" class="tabcontent">
<div class="vmenu">
<ul id="element_tab" class="vertabs">
<li onClick="javascript:loadElements(0,0);"><a href="javascript:void(0);" rel="element_tab0" >Add New Element</a></li>
<?php

for($i = 0; $i < count($data_prj_element_all); $i++)
{
	if($elementId == $data_prj_element_all[$i]['prj_element_id'])
	{
		echo '<li onClick="javascript:loadElements('.$data_prj_element_all[$i]['prj_element_id'].','.($i+1).');"><a href="javascript:void(0);" rel="element_tab'.($i+1).'" class="selected" >Element - '.($i+1).'</a></li>';
		$selectedtab = $i+1;
	}
	else
		echo '<li onClick="javascript:loadElements('.$data_prj_element_all[$i]['prj_element_id'].','.($i+1).');"><a href="javascript:void(0);" rel="element_tab'.($i+1).'" >Element - '.($i+1).'</a></li>';
}
?>
</ul>
</div>
<div id="element_tab0" class="tabcontent">
</div>
<?php
for($i = 0; $i < count($data_prj_element_all); $i++)
{
	echo '<div id="element_tab'.($i+1).'" class="tabcontent"></div>';
}
?>
</div>
<div id="country8" class="tabcontent">
<table>
<tr>
<td>
Project Notes:
</td>
<td>
<table width="100%" border="0" cellspacing="0" cellpadding="0" id="prjNotes">
<tbody>
  <tr>
   <td align="left" valign="top" colspan="4"><a style="cursor:hand;cursor:pointer;" name="addNotes" id="addNotes" onClick="javascript:popOpen(0,'AN');"><img height="25px" width="120px" src="<?php echo $mydirectory;?>/images/addNotes.gif" alt="notes"/></a>
<input type="hidden" id="notesid" name="notesid" value="<?php echo $notesid;?>"/></td>
  </tr>
 <?php 
   if($pid)
    {
        for($i=0; $i<count($data_prjNotes); $i++)
        {
?>   
        <tr>
<?php
			$limitNotes = substr($data_prjNotes[$i]['notes'],0,10);
			echo " <td width=\"100px\">Notes ".($i+1).": </td>";
			echo " <td >&nbsp;</td>";
            echo " <td width=\"150px\" >".stripslashes($limitNotes)."</td>";
			 echo " <td width=\"150px\" ><a style=\"cursor:hand;cursor:pointer;\" onclick=\"javascript:popOpen(".($i+1).",'EN' );\">Read more...</a></td>";
			echo " <td >&nbsp;</td>";
			echo " <td ><textarea id=\"txtAreaId".($i+1)."\"  style=\"display:none\">".stripslashes($data_prjNotes[$i]['notes'])."</textarea>
			       <input type='hidden' id=\"dateTimeId".($i+1)."\" value=\"".date("d-m-Y g:i A", $data_prjNotes[$i]['createdTime'])."\" />
				   <input type='hidden'  id=\"hdnNotesId".($i+1)."\" name=\"hdnNotesName[]\" value=\"".$data_prjNotes[$i]['notesid']."\" />
				   <input type='hidden' id=\"empNameId".($i+1)."\" value=\"".$data_prjNotes[$i]['firstName']." ".$data_prjNotes[$i]['lastName']. "\" /></td>";
			
?>
        </tr>
      
 <?php
        }
    }
?>
  
  
  </tbody>
</table>
</td>
</tr>
</table>
<br/>
<br/>
<div id="textPop" class="popup_block">

<center><div><strong>Project Note</strong></div></center>
<table width="80%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><textarea id="notes" name="notesId" cols="60" rows="10"></textarea></td>
    </tr>
    <tr>
        <td align="center"><input type="button" name="notesSubmit" id="notesSubmit" value="Submit" onClick="javascript:onNotesSubmit('prjNotes',document.getElementById('notes'));Fade();" />
    <input type="button" id="cancel" value="Cancel" />
    </td>
  </tr>
</table>
</div>
<div id="editPop" class="popup_block">
<center><div ><strong>Project Note</strong></div></center>
<table width="80%" border="0" cellspacing="0" cellpadding="0">


<tr id="tr_popEmpId" style="display:none">
<td width="100px" align="left"><strong>Added By : </strong></td>
<td width="5px">&nbsp;</td><td align="left" id="td_popEmpId"></td>
</tr>
<tr id="tr_popDateTimeId" style="display:none">
<td width="100px" align="left">
<strong>Added Date : </strong></td>
<td width="5px">&nbsp;</td>
<td align="left" id="td_popDateTimeId"></td>
</tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>



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
</div>
</div>
<table align="center">
<tr>
<td>
<input type="submit" id="submitButton" name="submitButton" value="Save" />
<input type="reset" id="reset" name="reset" value="Cancel" />
</td>
</tr>
</table>

</form>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/ajaxfileupload.js"></script>
<script type="text/javascript" src="<?php echo $mydirectory;?>/js/PopupBox.js"></script>
<script src="project.js" type="text/javascript"></script>
<?php 
if($isEdit)
{
echo '<script type="text/javascript">';
	if($data_prjSample['sampleprovided'] !="")
	{
		echo 'var smple='.$data_prjSample['sampleprovided'].';';
	}
	else
	{
		echo 'var smple=0;';
	}
	if($data_prjSample['product_client'] !="")
	{
		echo 'var prdn='.$data_prjSample['product_client'].';';
	}
	else
	{
		echo 'var prdn=0;';
	}
echo '</script>';
}
?>
<script type="text/javascript">
$().ready(function() {
var countries=new ddtabcontent("countrytabs");
countries.setpersist(true);
countries.setselectedClassTarget("link");
countries.init();

var vtab=new ddtabcontent("element_tab");
vtab.setpersist(false);
vtab.setselectedClassTarget("link");
vtab.init();

<?php 

if($selectedtab > 0)
{
	echo "loadElements('$elementId','$selectedtab');";
}
else
{
	echo "loadElements(0,0);";
}
?>
$('.ship_class').datepicker({
            changeMonth: true,
            changeYear: true,
        }).click(function() { $(this).datepicker('show'); });
});
function showDate(obj)
{
	$(obj).datepicker({
            changeMonth: true,
            changeYear: true,
        }).click(function() { $(obj).datepicker('show'); });
	$(obj).datepicker('show');
}


</script>
<script type="text/javascript">
function clearElements()
{
	document.getElementById('element_tab0').innerHTML = "";
	for(i=0;i < <?php echo count($data_prj_element_all); ?>;i++)
	{
		if(document.getElementById('element_tab'+(i+1)) != null)
			document.getElementById('element_tab'+(i+1)).innerHTML = "";
	}
}
function loadElements(id,divId)
{
	var dataString = "id="+id+"&pid=<?php echo $pid; ?>";
	$.ajax({
	 type: "POST",
	 url: "project_element.php",
	 data: dataString,
	 dataType: "json",
	 success:
	 function(data)
	 {
		 if(data!=null)
		 {
			 if(data.error)
			 {
				 $("#message").html("<div class='errorMessage'><strong>Sorry, "+data.error +"</strong></div>");
			 }
			 else
			 {
				 if(data.html != "")
				 {
					 clearElements();
					 document.getElementById('element_tab'+divId).innerHTML = data.html;
				 }
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
<script type="text/javascript">

function DeleteNotes(notesId)
{
	var dataString = "notesId="+notesId;
	$.ajax({
		   type: "POST",
		   url: "notesDelete.php",
		   data: dataString,
		   dataType: "json",
		   success:
		   function(data)
		   {
			   if(data!=null)
			   {
				   if(data.name || data.error)
				   {
					   $("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>");
				   }
				   else
				   {
					   $("#message").html("<div class='successMessage'><strong>Notes Removed...</strong></div>");
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
<script type="text/javascript">

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
	//element1.setAttribute("href","#");
	element1.style.cursor ="hand";
	element1.style.cursor ="pointer";
	element1.innerHTML = "Read more...";
	element1.onclick = function(){popOpen(rowCount,'EN');};
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
	<?php 
	if($pid !=0 && $noteid >0)
	{
	?>
	var cell6 = row.insertCell(6);	
	cell6.innerHTML="<a class=\"alink\" href=\"javascript:;\" onClick=\"DeleteCurrentNotesRow(this);\"><img style=\"width:32px;height:25px;\" src=\"<?php echo $mydirectory;?>/images/delete.png\" ></a>";
	<?php 
	}
	?>
}
</script>
<script type="text/javascript">
function popOpen(rowIndex,type)
{
	//alert(type);
	if(type=='AN')
	{
		var popID = 'textPop'; //Get Popup Name
	}
	else if(type=='EN')
	{
		var popID = 'editPop';
		document.getElementById('editPopId').innerHTML = document.getElementById('txtAreaId'+rowIndex).value;		
		<?php 
		if($isEdit )
		{
		?>
			var notesid = document.getElementById('hdnNotesId'+rowIndex).value;
			if(notesid >0 )
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
		<?php 
		}
		?>
	}
	else if(type =='PEC')
	{
		var popID = 'prj_estimatecost';
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
		document.validationForm.notesId.value="";
	}
function popupWindow() 
{
	var hdn = document.getElementById('hdn_sampleNum').value;
 if(hdn != "")
  var url = "<?php echo $adminURL;?>samplerequest/samplerequest_new.add.php?id="+hdn;
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
</script>

<script type="text/javascript"> 
function DeleteUploads(id,filename,prj_id,type,formtype)
{
	var dataString = "filename="+filename+"&tableid="+id+"&pid="+prj_id+"&type="+type+"&formtype="+formtype;
	$.ajax({
		   type: "POST",
		   url: "delete_uploads.php",
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
						$("#message").html("<div class='successMessage'><strong>File Removed...</strong></div>");
						$(location).attr("href","project_mgm.add.php?id="+data.id);
						
					}
				}
				else
				{
				$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
				}
				
			}
		});
}
$(function(){$("#validationForm").submit(function(){
if($("#validationForm").valid())
	{
		var pid = document.getElementById('pid');
		  dataString = $("#validationForm").serialize();
		  $.ajax({
				 type: "POST",
				 url: "project_mgmSubmit.php",
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
							if(document.getElementById('isEdit').value==1)
							{
								$("#message").html("<div class='successMessage'><strong>Project Management details Updated. Thank you.</strong></div>");
								
							}
							else
							{
								$("#message").html("<div class='successMessage'><strong>New Project Management Information Added. Thank you.</strong></div>");
							}
							$(location).attr("href","project_mgm.add.php?id="+data.id+"&sav=1");	
						}
					  } 
					else
					{
					 $("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
					}
				}
		});
		  return false;
	  }
	   });
			   });
function DeleteCurrentNotesRow(obj)
{	
	var delRow = obj.parentNode.parentNode;
	var tbl = delRow.parentNode.parentNode;
	var rIndex = delRow.sectionRowIndex;		
	var rowArray = new Array(delRow);
	DeleteRow(rowArray);
}

function DeleteCurrentRow(obj<?php if($isEdit) {?>,Id<?php }?>,tbl_vendorid)
{
	var delRow = obj.parentNode.parentNode;
	var tbl = delRow.parentNode.parentNode;
	var rIndex = delRow.sectionRowIndex;		
	var rowArray = new Array(delRow);
	<?php
	if($isEdit)
	{ 
	?>	
	var pid = document.getElementById('pid').value;
	var dataString = "vendorId="+tbl_vendorid+"&pid="+pid;
	$.ajax({
		   type: "POST",
		   url: "vendorDelete.php",
		   data: dataString,
		   dataType: "json",
		   success:
	function(data)
	{
		if(data!=null)
		{
			if(data.name || data.error)
			{
				$("#message").html("<div class='errorMessage'><strong>Sorry, " + data.name + data.error +"</strong></div>"); 
			}
			else 
			{
					$("#message").html("<div class='errorMessage'><strong>Vendor Removed...</strong></div>");
			}
		}
		else
		{
			$("#message").html("<div class='errorMessage'><strong>Sorry, Unable to process.Please try again later.</strong></div>");
		}
	}
});
<?php }?>
	DeleteRow(rowArray);
}
function DeleteRow(rowObjArray)
{	
for (var i=0; i<rowObjArray.length; i++) {
	var rIndex = rowObjArray[i].sectionRowIndex;
	rowObjArray[i].parentNode.deleteRow(rIndex);
}	
}
function ajaxFileUpload(fileId,type,prjId,formtype)
{
	var saved = 0;
	if(document.getElementById('saveid').value == 0)
	{
		if(confirm("Please save other project information before uploading image/file(s)\n\t Do you want to continue uploading?"))
			saved = 1;
	}
	else
		saved = 1;
	if(saved)
	{	
	$("#loading")
		.ajaxStart(function(){
			$(this).show();
		})
		.ajaxComplete(function(){
			$(this).hide();
		});
		var pid = document.getElementById('pid').value;
		
		var uploadsId = 0;
		if(type == 'P')
			uploadsId = document.getElementById('patternId').value;
		else if(type == 'G')
			uploadsId = document.getElementById('gradientId').value;
		var pattern = document.getElementById('pattern');
		var grading = document.getElementById('grading');
		var uploadimage = document.getElementById('image');
		var uploadfile = document.getElementById('file');
		var elementid = document.getElementById('elementid');
		$.ajaxFileUpload
		(
			{
				url:'doajaxfileupload.php',
				secureuri:false,
				fileElementId:fileId,
				dataType: 'json',
				data:{pattern:pattern.value,grading:grading.value, uploadimage:uploadimage.value, uploadfile:uploadfile.value, fileId:fileId, pid:pid,uploadsId:uploadsId,type:type,formtype:formtype},
				success: function (data, status)
				{
					if(typeof(data.error) != 'undefined')
					{
						if(data.error != '')
						{
							$("#message").html("<div class='errorMessage'><strong>"+data.error +"</strong></div>");
						}else
						{
							$("#message").html("<div class='successMessage'><strong>"+data.msg +"</strong></div>");
						
							document.getElementById(fileId).value="";
							//document.getElementById('alink'+img_cnt).style.display="block";
							$(location).attr("href","project_mgm.add.php?id="+data.id+"&sav=1");							
						}
					}
				},
				error: function (data, status, e)
				{
					$("#message").html("<div class='errorMessage'><strong>"+e+"</strong></div>");
				}
			}
		)
		
		return false;
	}

}
	</script>
<?php 
require('../../trailer.php');
?>