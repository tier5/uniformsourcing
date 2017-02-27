<?php
require('Application.php');
$is_session =0;
$emp_type ="";
$emp_id= "";
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
$sql = "Select * from tbl_prmilestone$tx where pid = $pid and status = 1";	
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



$production='<table width="100%">
    <tr>
      <td align="center">
          <table width="80%" border="0" align="center" cellpadding="1" cellspacing="1">
            <tr>
              <td width="40%" height="25" align="right" valign="top">Lap Dip:</td>
              <td width="1%">&nbsp;</td>
              <td align="left" valign="top">';
			 $production.='<input id="lapDip" name="lapDip" onclick="javascript:showDate(this);" type="text" ';
			 if($emp_type ==2)
			 {$production.='disabled="disabled"'; }
			 $production.='value="'.$data_prj_milestone['lapdip'].'"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Lap Dip Approval:</td>
              <td>&nbsp;</td>
              <td align="left" valign="top">';
			  $production.='<input id="lapDipApprvl" name="lapDipApprvl" onclick="javascript:showDate(this);" type="ptrnsetup"';
			  if($emp_type ==2)
			  {$production.='disabled="disabled"'; }
			  $production.='value="'.$data_prj_milestone['lapdipapproval'].'" /></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Estimated Fabric Delivery Date:</td>
              <td>&nbsp;</td>
            <td align="left" valign="top">';
			$production.='<input id="estDelvry" name="estDelvry" onclick="javascript:showDate(this);" type="text"';
			if($emp_type ==2){$production.='disabled="disabled"'; }
			$production.='value="'.$data_prj_milestone['estdelivery'].'"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Production Sample: </td>
              <td>&nbsp;</td>
              <td align="left" valign="top">';
			  $production.='<input id="pdctSampl" name="pdctSampl" onclick="javascript:showDate(this);" type="text"';
			  if($emp_type ==2){$production.='disabled="disabled"'; }
			  $production.='value="'.$data_prj_milestone['prdtnsample'].'"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Production Sample Approval: </td>
              <td>&nbsp;</td>
              <td align="left" valign="top">';
			$production.='<input id="pdctSamplApprvl" name="pdctSamplApprvl" onclick="javascript:showDate(this);" type="text"'; 
		    if($emp_type ==2)
			{$production.='disabled="disabled"'; }
			$production.='value="'.$data_prj_milestone['prdtnsampleapprval'].'"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Sizing Line:  </td>
              <td>&nbsp;</td>
              <td align="left" valign="top">';
			 $production.='<input id="szngLine" name="szngLine" onclick="javascript:showDate(this);" type="text"';
			 if($emp_type ==2){$production.='disabled="disabled"'; }
			 $production.='value="'.$data_prj_milestone['szngline'].'"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Production target Delivery: </td>
              <td>&nbsp;</td>
              <td align="left" valign="top">';
			  $production.='<input id="prdctnTrgtDelvry" name="prdctnTrgtDelvry" onclick="javascript:showDate(this);" type="text"';
			  if($emp_type ==2)
			  {$production.=' disabled="disabled"'; }
			  $production.='value="'.$data_prj_milestone['prdtntrgtdelvry'].'"/>';
			  $production.='<input type="hidden" id="milestone_id" name="milestone_id" onclick="javascript:showDate(this);" value="'.$milestone_id.'"/></td>
            </tr>
            <tr>
              <td height="25" align="right" valign="top">Design Board Completion: </td>
              <td>&nbsp;</td>
              <td align="left" valign="top">';
			  $production.='<input id="DBcmplt" name="DBcmplt" onclick="javascript:showDate(this);" type="text"';
			  if($emp_type ==2){$production.='disabled="disabled"'; } 
			  $production.='value="'.$data_prj_milestone['desbordcmplt'].'"/></td>
              
            </tr> 
           
    <tr>
     <td height="24" align="right">Design Board Approval:</td>
	 <td>&nbsp;</td> 
     <td align="left"> Yes';
   
	$production.='<input type="radio" value="1" id="DBaprove" name="DBaprove" onclick="javascript:designboardVisible(this);" ';
	if($emp_type ==2){ 
	  	$production.='disabled="disabled"';
	}
	if($data_prj_milestone['desbordappval']!= 0)
	{ $production.='checked="checked"';} 
	$production.='/> No';
	$production.='<input   type="radio" value="0" id="DBaprove" name="DBaprove" onclick="javascript:designboardVisible(this);"'; 
	if($emp_type ==2){ 
	  	$production.='disabled="disabled"';
	}
	if($data_prj_milestone['desbordappval']== 0)
	{$production.='checked="checked"';} 
	$production.='/>
    </td></tr>
    <tr>
    <td align="center" colspan="3">
    <table width="100%" id="designboardcalender"';
	if($data_prj_milestone['desbordappval']== 1){$production.='style="display:"';} else { $production.='style="display:none"';} $production.='>
    <tr>
        <td width="40%" align="right">Design Board Calender:</td><td width="1%">&nbsp;</td><td align="left"><input type="text" name="dbcalender" id="dbcalender" onclick="javascript:showDate(this);" ';
	 if($emp_type ==2){ 
	  $production.='disabled="disabled"';
	  }
	  $production.=' value="'.$data_prj_milestone['design_board_calender'].'" /></td>
        </tr>
        </table>
        </td>
    </tr>
      </table></td>
    </tr>
  </table>';
  //echo $production;
  $return_arr['html'] = $production;
echo json_encode($return_arr);
return;
  ?>