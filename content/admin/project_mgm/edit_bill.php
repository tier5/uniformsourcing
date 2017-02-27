<?php
require('Application.php');
$return_array = array();
$return_array['msg'] = '';
$return_array['error'] = '';
$return_array['td'] = '';
$return_array['type'] = '';
$return_array['pid'] = 0;
if(isset($_POST['type']) && isset($_POST['pid']) && $_POST['pid'] > 0)
{
	$return_array['type']=$_POST['type'];
	$return_array['td']=$_POST['td'];
	$return_array['pid'] = $_POST['pid'];
	if($_POST['type'] == 'load')
	{		
		$sql = 'select is_billed, bill_date from tbl_newproject where pid = '.$_POST['pid'];
		if(!($result=pg_query($connection,$sql))){
			$return_array['error'] = "Failed query select-: " . pg_last_error($connection);
			echo json_encode($return_array);
			return;
		}
		while($row = pg_fetch_array($result)){
			$data_bill=$row;
		}
		pg_free_result($result);
		$return_array['msg'] = '<div> Billed : <input type="checkbox" id="is_billed_'.$return_array['td'].'"';
		if($data_bill['is_billed'] != '' && $data_bill['is_billed'] > 0){
			$return_array['msg'] .= 'checked="checked" value = "1"';
			if($data_bill['bill_date'] == '')
				$data_bill['bill_date'] = 0;
		}
		else{
			$return_array['msg'] .= ' value="0"';
			$data_bill['bill_date'] = 0;
		}
		$return_array['msg'] .= '/><br/>';
		$return_array['msg'] .= ' Date : <input style="width:70px;" type="text" onclick="showDate(this);" id="bill_date_'.$return_array['td'].'"';
		if($data_bill['is_billed'] != '' && $data_bill['is_billed'] > 0 && $data_bill['bill_date'] > 0)
			$return_array['msg'] .= 'value="'.date('m/d/Y', $data_bill['bill_date']).'"';
		else
			$return_array['msg'] .= 'value=""';
		//$return_array['msg'] .= '/><br/><input type="image" src="'.$mydirectory.'/images/testt.png" alt="save" onclick="editBilledinfo('.$return_array['pid'].',\'save\','.$return_array['td'].');" value="Save"/></div>';
	    $return_array['msg'] .= '/><br/><input type="button" onclick="editBilledinfo('.$return_array['pid'].',\'save\','.$return_array['td'].');" value="save"></div>';
	   
	}
	else if($_POST['type'] == 'save')
	{
		$is_billed = 0;
		if($_POST['is_billed'] != 'undefined' && $_POST['is_billed'] != '' )
			$is_billed = $_POST['is_billed'];			
		$bill_date = 0;		
		if($_POST['bill_date'] != 'undefined' && $_POST['bill_date'] > 0 ){		
			$bill_date = strtotime($_POST['bill_date']);
		}
		$sql = 'Update tbl_newproject set is_billed = '.$is_billed.' , bill_date ='.$bill_date.' where pid = '.$_POST['pid'];
		if(!($result=pg_query($connection,$sql))){
			$return_array['error'] = "Failed query Update: " . pg_last_error($connection);
			echo json_encode($return_array);
			return;
		}
		$return_array['msg'] = '<div style="cursor:pointer;cursor:hand;" onclick="javascript:editBilledinfo('.$return_array['pid'].', \'load\', \''.$return_array['td'].'\');" >';
		if($_POST['is_billed'] != '' && $_POST['is_billed'] > 0)
			$return_array['msg'] .= '&nbsp;Yes&nbsp;:&nbsp;'.date('m/d/Y',$bill_date);
		else 	
			$return_array['msg'] .= '&nbsp;No&nbsp;';
		$return_array['msg'] .= '</div>';
	}
}
echo json_encode($return_array);
return;
?>