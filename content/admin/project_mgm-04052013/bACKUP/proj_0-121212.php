<?php
require('Application.php');
extract($_POST);
$return_arr = array();
$return_arr['html'] = "";
$return_arr['error'] = "";
$return_arr['tabid'] ="";
$is_session =0;
$emp_type ="";
$emp_id= "";
$tab_id = 0;
$return_arr['tabid'] = $tab_id;
if(isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] >0 && $_SESSION['employeeType'] == 1))
{
	$emp_type = $_SESSION['employeeType'] ;
	$emp_id =  $_SESSION['employee_type_id'];
	$is_session = 1;
}
else if(isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] >0 && $_SESSION['employeeType'] == 2))
{
	$emp_type = $_SESSION['employeeType'] ;
	$emp_id =$_SESSION['employee_type_id'];
	$is_session = 1;
}
if(isset($_POST['pid']) && $_POST['pid']!=0)
{
	$sql = "Select projectname,color,client,materialtype,project_manager,project_manager1,project_manager2,stock_or_custom from tbl_newproject where $status_query and pid = $pid";
	//echo $sql;
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prj=$row;
	}
	pg_free_result($result);
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

$sql= 'Select "employeeID",firstname,lastname,"employeeType" from "employeeDB" where active =\'yes\' ';
if(!($result=pg_query($connection,$sql))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_employee[]=$row;
}
pg_free_result($result);
$html ='<table width="101%" cellpadding="0" style="padding-right:10px;" cellspacing="0" border="0">'.
'<tr>'.
'<td>'.
'<table id="tblID" cellpadding="0" cellspacing="0" border="0" width="80%">'.
'<tr>'.
'<td align="right" width="50%" height="25">Choose Client:</td>'.
'<td width="1%">&nbsp;</td>'.
'<td align="left"><select name="clientID" id="clientID" onchange="javascript:load_client_shipper();"';
if($emp_type >0)
{ 
	$html .='disabled="disabled"';
}
$html .=' ><option value="0">---Select---</option>';
for($i=0; $i < count($data1); $i++)
{
	if($data_prj['client']==$data1[$i]['ID'])
		$html .='<option value="'.$data1[$i]['ID'].'" selected="selected">'.$data1[$i]['client'].'</option>';
	else 
		$html .='<option value="'.$data1[$i]['ID'].'">'.$data1[$i]['client'].'</option>';
}
$html .='</select></td>'.
'</tr>'.
'<tr>'.
'<td align="right" height="25">Primary Project Manager:</td>'.
'<td>&nbsp;</td>'.
'<td align="left">'.
'<select name="project_manager"';
if($emp_type >0)
{ 
$html .='disabled="disabled"';
}
$html .='>';
 for($i=0; $i < count($data_employee); $i++)
 {
	if($data_employee[$i]['employeeID'] == $data_prj['project_manager'])
		$html .='<option value="'.$data_employee[$i]['employeeID'].'" selected="selected">'.$data_employee[$i]['firstname'].$data_employee[$i]['lastname'].'</option>';
	else 
		$html .='<option value="'.$data_employee[$i]['employeeID'].'">'.$data_employee[$i]['firstname'].$data_employee[$i]['lastname'].'</option>';
}
$html .='</select>';
$html .='</tr>'.
        
        '<tr>'.
'<td align="right" height="25">Secondary Project Manager 1:</td>'.
'<td>&nbsp;</td>'.
'<td align="left">'.
'<select name="project_manager1"' ;
if($emp_type >0)
{ 
$html .='disabled="disabled"';
}
$html .='>';
$html .='<option value="" >select</option>';
 for($i=0; $i < count($data_employee); $i++)
 {
	if($data_employee[$i]['employeeID'] == $data_prj['project_manager1'])
		$html .='<option value="'.$data_employee[$i]['employeeID'].'" selected="selected">'.$data_employee[$i]['firstname'].$data_employee[$i]['lastname'].'</option>';
	else 
		$html .='<option value="'.$data_employee[$i]['employeeID'].'">'.$data_employee[$i]['firstname'].$data_employee[$i]['lastname'].'</option>';
}
$html .='</select>';
$html .='</tr>'.

                '<tr>'.
'<td align="right" height="25">Secondary Project Manager 2:</td>'.
'<td>&nbsp;</td>'.
'<td align="left">'.
'<select name="project_manager2"';
if($emp_type >0)
{ 
$html .='disabled="disabled"';
}
$html .='>';
$html .='<option value="" >select</option>';
 for($i=0; $i < count($data_employee); $i++)
 {
	if($data_employee[$i]['employeeID'] == $data_prj['project_manager2'])
		$html .='<option value="'.$data_employee[$i]['employeeID'].'" selected="selected">'.$data_employee[$i]['firstname'].$data_employee[$i]['lastname'].'</option>';
	else 
		$html .='<option value="'.$data_employee[$i]['employeeID'].'">'.$data_employee[$i]['firstname'].$data_employee[$i]['lastname'].'</option>';
}
$html .='</select>';
$html .='</tr>'.
'<tr>'.
'<td align="right" height="25">Project Name:</td>'.
'<td>&nbsp;</td>'.
'<td align="left"><input type="text" id="projectName" name="projectName"';
if($emp_type >1)
{
	$html .='readonly="readonly"';
}
	$html .=' value="'.htmlentities($data_prj['projectname']).'" /></td>'.
'</tr>'.
'<tr>'.
'<td align="right" height="25">Color:</td>'.
'<td>&nbsp;</td>'.
'<td align="left"><input type="text" name="color"';
if($emp_type >1)
{
	$html .='readonly="readonly"';
}
$html .=' value="'.htmlentities($data_prj['color']).'" />'.
'</td>'.
'</tr>'.
'<tr>'.
'<td align="right" height="25">Type of Material:</td>'.
'<td>&nbsp;</td>'.
'<td align="left"><input type="text" name="materialtype"';
if($emp_type ==2)
{
	$html .='readonly="readonly"';
}
$html .=' value="'.htmlentities($data_prj['materialtype']).'"/>'.
'</td>'.
'</tr>'.
'<tr align="left"';
	 if($emp_type ==2){ 
	  $html.='style="visibility:hidden;"';
	  }
	  $html.='>'.
'<td align="right" height="25">Notification to:</td>'.
'<td>&nbsp;</td>'.
'<td><select name="notification_select" id="notification_select" onchange="javascript:changeDepositFields();"';
if($emp_type >0)
{
	$html .='disabled="disabled"'; 
}
$html .='>';
                 
  if(($data_prj['stock_or_custom'] != 1) && ($data_prj['stock_or_custom'] != 2))
  {
	 $html .='<option value="0" selected="selected">---select---</option><option value="1">Stock</option>'.
	  '<option value="2">Custom</option>';
  }
  else if($data_prj['stock_or_custom'] == 1) 
  {
	   $html .='<option value="0">---select---</option><option value="1" selected="selected">Stock</option><option value="2">Custom</option>';
  }
  else if($data_prj['stock_or_custom'] == 2) 
  {
	  $html .='<option value="0">---select---</option><option value="1" >Stock</option><option value="2" selected="selected">Custom</option>';
  }
  else
  {
	  $html .='<option value="0" selected="selected">---select---</option><option value="1">Stock</option>'.
	  '<option value="2">Custom</option>';
  }
      $html .='</select></td>'.
'</tr>'.
'</table>'.
'</td>'.
'</tr>'.
'</table>';
$return_arr['html'] = $html;
echo json_encode($return_arr);
return;
?>