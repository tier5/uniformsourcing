<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
$error = "";
$msg = "";
$html = "";
$return_arr = array();
extract($_POST);
$tx='';
if(isset($close))
$tx='_closed';
$return_arr['error'] = "";
$return_arr['name'] = "";
$return_arr['html'] = "";

if(isset($_POST['pid']) && $_POST['pid']!=0){
	$sql ='select firstname,lastname from "employeeDB" where "employeeID"='.$_SESSION["employeeID"].' and active =\'yes\'';
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row_cnt = pg_fetch_array($result)){
		$data_employee_name=$row_cnt;
	}
	pg_free_result($result);
	$sql="";

	$sql = "Select tbl_mgt_notes$tx.*,e.firstname as \"firstName\", e.lastname as \"lastName\" from tbl_mgt_notes$tx inner join \"employeeDB\" as e on e.\"employeeID\" =tbl_mgt_notes$tx.\"createdBy\"  where \"isActive\" =1 and pid = $pid";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prjNotes[]  =$row;
	}
	pg_free_result($result);

}
$html = '<table>
<tr>
<td>
Project Notes:
</td>
<td>
<table width="100%" border="0" cellspacing="0" cellpadding="0" id="prjNotes">
<tbody>
  <tr>
   <td align="left" valign="top" colspan="4"><a ';
   	if($emp_type ==2){
		$html .= 'style="visibility:hidden"';
	}
	$html .= 'style="cursor:hand;cursor:pointer;" name="addNotes" id="addNotes" onClick="javascript:popOpen(0,\'AN\');">
	<img height="25px" width="120px" src="'.$mydirectory.'/images/addNotes.gif" alt="notes"/></a>
<input type="hidden" id="notesid" name="notesid" value="'.$notesid.'"/></td>
  </tr>';
   if($pid)
    {
        for($i=0; $i<count($data_prjNotes); $i++)
        {
        	$html .= '<tr>';
			$limitNotes = substr($data_prjNotes[$i]['notes'],0,10);
			$html .= ' <td width="100px">Notes '.($i+1).': </td>';
			$html .= ' <td >&nbsp;</td>';
            	$html .= ' <td width="150px" >'.date("m-d-Y g:i A", $data_prjNotes[$i]['createdTime']).'</td>
						<td width="90px" >'.$data_prjNotes[$i]['firstName'].$data_prjNotes[$i]['lastName'].'</td>
						<td width="90px" ><strong>'.$data_prjNotes[$i]['title'].'</strong></td>';
			$html .= ' <td width="150px" ><a style="cursor:hand;cursor:pointer;" onclick="javascript:popOpen('.($i+1).',\'EN\' );" >Read..</a></td>';
			$html .= ' <td >&nbsp;</td>';
			$html .= ' <td ><textarea id="title_Id'.($i+1).'"  style="display:none">'.stripslashes($data_prjNotes[$i]['title']).'</textarea>
					<textarea id="txtAreaId'.($i+1).'"  style="display:none">'.stripslashes($data_prjNotes[$i]['notes']).'</textarea>
			       <input type="hidden" id="dateTimeId'.($i+1).'" value="'.date("d-m-Y g:i A", $data_prjNotes[$i]['createdTime']).'" />
				   <input type="hidden"  id="hdnNotesId'.($i+1).'" name="hdnNotesName[]" value="'.$data_prjNotes[$i]['notesid'].'" />
				   <input type="hidden" id="empNameId'.($i+1).'" value="'.$data_prjNotes[$i]['firstName'].' '.$data_prjNotes[$i]['lastName']. '" /></td>			
         	</tr>';
        }
    }
  
$html .= '</tbody>
</table>
</td>
</tr>
</table>
<br/>
<br/>
<div id="textPop" class="popup_block">

<center><div><strong>Project Note</strong></div></center><br />
<table width="80%" border="0" cellspacing="1" cellpadding="0">
 <tr>
 	<td width="80px" align="left">Title:</td>
    <td width="2px" align="left">&nbsp;</td>
    <td align="left"><input type="text" id="title_id" name="notes_title" value="" size="28"  onkeydown="limitTextArea(\'title_id\',50,\'counter\');" onkeyup="limitTextArea(\'title_id\',50,\'counter\');" /><span id="counter"></span></td>
    </tr>
  <tr>
  <td align="left">Notes:</td>
    <td align="left">&nbsp;</td>
    <td align="left"><textarea id="prj_notes" name="notesId"></textarea></td>
    </tr>
    <tr>
        <td align="center" colspan="3"><input type="hidden" id="employee_name" value="'.$data_employee_name['firstname'].$data_employee_name['lastname'].'" /><input type="button" name="notesSubmit" id="notesSubmit" value="Submit" onClick="javascript:onNotesSubmit(\'prjNotes\');Fade();" />
    <input type="button" id="cancel" value="Cancel" />
    </td>
  </tr>
</table>
</div>
<div id="editPop" class="popup_block">
<center><div ><strong>Project Note</strong></div></center>
<table width="80%" border="0" cellspacing="0" cellpadding="0">
  <tr id="tr_popEmpId">
    <td width="90px" align="left"><strong>Added By : </strong></td>
    <td width="2px">&nbsp;</td>
    <td align="left" id="td_popEmpId">&nbsp;</td>
  </tr>
  <tr id="tr_popDateTimeId">
    <td><strong>Added Date : </strong></td>
    <td>&nbsp;</td>
    <td align="left" id="td_popDateTimeId">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="90px" align="left"><strong>Title :</strong></td>
    <td width="2px">&nbsp;</td>
    <td align="left"><p id="edit_title"></p></td>
  </tr>
  <tr>
    <td align="left"><strong>Notes :</strong></td>
    <td>&nbsp;</td>
    <td align="left"><p id="editPopId"></p></td>
  </tr>
</table>';
//echo $html;

$return_arr['html'] =$html;
echo json_encode($return_arr);
return;
?>