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
	$sql = "Select is_csr, is_vsr, csr_date, vsr_date from tbl_newproject$tx where pid = ".$pid;
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_status =$row;
	}	
	pg_free_result($result);
 }
$status='<table width="80%" cellpadding="0" cellspacing="0" border="0">'.
'<tr>'.
'<td>'.
'<table cellpadding="1" cellspacing="1" border="0" width="100%">'.
'<tr>'.
'<td align="right" width="50%" height="25">Client Status Report: </td>'.
'<td width="1%">&nbsp;</td>'.
'<td align="left" height="25">';
if($data_status['is_csr'] != '' && $data_status['is_csr'] > 0)
	$status .= "Sent on ".date('m/d/Y',$data_status['csr_date']);
else
	$status .= 'Report Not Send.';
$status .= '</td>'.
'</tr>'.
'<tr>'.
'<td align="right" width="50%" height="25">Vendor Status Report: </td>'.
'<td width="1%">&nbsp;</td>'.
'<td align="left" height="25">';
if($data_status['is_vsr'] != '' && $data_status['is_vsr'] > 0)
	$status .= "Sent on ".date('m/d/Y',$data_status['vsr_date']);
else
	$status .= 'Report Not Send.';
$status .= '</td>'.
'</tr>'.
'</table>'.
'</td>'.
'</tr>'.
'</table>';
//'<script type="text/javascript">document.getElementById("submitButton").setAttribute();</script>';
$return_arr['html'] = $status;
echo json_encode($return_arr);
return;
?>