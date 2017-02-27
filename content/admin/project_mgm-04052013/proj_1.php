<?php
require('Application.php');
$is_session =0;
$emp_type ="";
$emp_id= "";
$purchaseId = 0;
extract($_POST);
$tx='';
if(isset($close))
$tx='_closed';
$return_arr = array();
$return_arr['error'] = "";
$return_arr['html'] = "";

if(isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] >0 && $_SESSION['employeeType'] == 1))
{
	$emp_type = $_SESSION['employeeType'] ;
	$emp_id =  $_SESSION['employee_type_id'];
	$is_session = 1;
	$style_price = ' style="visibility:hidden"';
}
else if(isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] >0 && $_SESSION['employeeType'] == 2))
{
	$emp_type = $_SESSION['employeeType'] ;
	$emp_id =$_SESSION['employee_type_id'];
	$is_session = 1;
	$style_price = ' disabled="disabled"';
}
 if($_POST[pid]>0)
 {
	$sql = "Select * from tbl_prjpurchase$tx where pid = ".$pid." limit 1";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prjPurchase =$row;
	}
	if( $data_prjPurchase['purchaseId'] >0)
		$purchaseId = $data_prjPurchase['purchaseId'];
	pg_free_result($result);
 }
$purchase='<table width="80%" cellpadding="0" cellspacing="0" border="0">'.
'<tr>'.
'<td>'.
'<input type="hidden" name="tab1" id="tab1" value="0" />'.  
'<table cellpadding="1" cellspacing="1" border="0" width="100%">'.
'<tr>'.
'<td align="right" width="50%" height="25">Purchase Order:</td>'.
'<td width="1%">&nbsp;</td>'.
'<td align="left" height="25">'.'<input';
if($emp_type >0){
	$purchase.=' disabled="disabled"';
	}
	$purchase.=' type="text" name="purchaseOrder" id="purchaseOrder" value="'.htmlentities($data_prjPurchase['purchaseorder']).'" /></td>'.
'</tr>'.
'<tr>'.
'<td align="right" height="25">Purchase Order Due Date:</td>'.
'<td>&nbsp;</td>'.
'<td align="left" height="25"><input ';
if($emp_type >0){ 
$purchase.=' disabled="disabled"';
                 }
$purchase.='type="text" name="poDueDate" id="prj_poDueDate" onclick="javascript:showDate(this);" value="'.$data_prjPurchase['purchaseduedate'].'"/></td>'.
'</tr>'.
'<tr>'.
'<td align="right" height="25">Quantity of People:</td>'.
'<td>&nbsp;</td>'.
'<td align="left" height="25"><input ';
if($emp_type >0){ 
$purchase.='disabled="disabled"';
                  }
$purchase.='type="text" name="quanPeople" id="quanPeople" value="'.$data_prjPurchase['qtypeople'].'"/></td>'.
'</tr>'.
'<tr>'.
'<td align="right" height="25">Sizes Needed:</td>'.
'<td>&nbsp;</td>'.
'<td align="left" height="25"><input ';
if($emp_type >0){ 
$purchase.='disabled="disabled" ';
 }
$purchase.='type="text" id="sizeNeeded" name="sizeNeeded" value="'.htmlentities($data_prjPurchase['sizeneeded']).'" /></td>'.
'</tr>'.
'<tr>'.
'<td align="right" height="25">Garment Description:</td>'.
'<td>&nbsp;</td>'.
'<td align="left" height="25"><textarea ';
if($emp_type >0){ 
$purchase.='disabled="disabled"';
}
$purchase.='wrap="physical" name="garDescription" id="garDescription" rows="7" cols="35" >';
 
$purchase.=htmlspecialchars($data_prjPurchase['garmentdesc']).'</textarea>
<input ';
$purchase.='type="hidden" id="purchaseId" name="purchaseId" value="'.$purchaseId .'" />
</td>'.
'</tr>'.
'<tr>
<td align=\'right\' height="25">PT Invoice#:</td>
<td>&nbsp;</td>
<td align="left" height="25"><input ';
if($emp_type >0){
	$purchase.='disabled="disabled"';
}
$purchase.='type="text" name="ptinvoice" value="'.htmlentities($data_prjPurchase['pt_invoice']).'" /></td>
</tr>
</table>'.
'</td>'.
'</tr>'.
'</table>';
$return_arr['html'] = $purchase;
echo json_encode($return_arr);
return;
?>