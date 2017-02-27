<?php
require('Application.php');
$is_session =0;
$emp_type ="";
$emp_id= "";

extract($_POST);
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
	//$status_query = 'status = 1'; 
	$sql = "Select * from tbl_newproject where $status_query and pid = $pid";
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
$notification='<table>
<tr>
	<td>Notification:</td>
    <td>On';
	$notification.='<input type="radio" name="notification_radio" value="0"';
	if($data_prj['notification'] != 1){
		$notification.='checked="checked"';
	}
	$notification.='/>Off
	<input type="radio" name="notification_radio" value="1"'; 
	if(isset($data_prj['notification']) )
	   { 
		   if($data_prj['notification'] == 1)
		   { 
		   $notification.='checked="checked"'; 
		   }
	   }
$notification.='</td>
	</tr>
</table>';
//echo $notification;
$return_arr['html'] = $notification;
echo json_encode($return_arr);
return;
?>