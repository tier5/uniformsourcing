<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
$id = 0;
$pid = 0;
$is_session = 0;
$return_arr = array();
$return_arr['html'] = "";
$return_arr['error'] = "";
if(isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] >0 && $_SESSION['employeeType'] == 1))
{
	$is_session = 1;
}
else if(isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] >0 && $_SESSION['employeeType'] == 2))
{
	$is_session = 1;
}
if(isset($_POST['id']) && $_POST['id']>0)
{
	$id = $_POST['id'];
	$pid = $_POST['pid'];	
	$sql = "Select * from tbl_prj_elements where status = 1 and prj_element_id = $id ";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result))
	{
		$data_prjElements  =$row;
	}
	pg_free_result($result);
	if($data_prjElements['prj_element_id'] !="")
		$elementid = $data_prjElements['prj_element_id'];
	if($pid == 0)
		$pid = $data_prjElements['pid'];
	$data_prjElements['style'] = stripslashes($data_prjElements['style']);
	$data_prjElements['color'] = stripslashes($data_prjElements['color']);
	$data_prjElements['image'] = stripslashes($data_prjElements['image']);
	$data_prjElements['element_cost'] = stripslashes($data_prjElements['element_cost']);
	$data_prjElements['elementfile'] = stripslashes($data_prjElements['elementfile']);
}
else
{
	$elementid = 0;
	$data_prjElements['style'] = "";
	$data_prjElements['color'] = "";
	$data_prjElements['image'] = "";
	$data_prjElements['elementfile'] = "";
	$data_prjElements['elementtype'] = 0;
	$data_prjElements['element_cost'] = 0;
	$data_prjElements['vid'] = 0;
}
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

$html = '<table width="80%" >'.
'<tr>'.
'<td valign="top">'.
'<table cellpadding="1" cellspacing="1" border="0">'.
'<tr>'.
'<td align="right">Element Type:</td>'.
'<td align="left"><select name="elementtype">';
for($i=0; $i < count($data_elements); $i++){
	if($data_prjElements['elementtype']==$data_elements[$i]['element_id'])
		$html .= '<option value="'.$data_elements[$i]['element_id'].'" selected="selected">'.$data_elements[$i]['elements'].'</option>';
	else 
		$html .= '<option value="'.$data_elements[$i]['element_id'].'">'.$data_elements[$i]['elements'].'</option>';
}

$html .= '</select></td>'.
'</tr>'.
'<tr>'.
'<td align="right">Vendor:</td>'.
'<td align="left"><select name="vendor_ID">';
for($i=0; $i < count($data_Vendr); $i++){
	if($data_prjElements['vid']==$data_Vendr[$i]['vendorID'])
		$html .= '<option value="'.$data_Vendr[$i]['vendorID'].'" selected="selected">'.$data_Vendr[$i]['vendorName'].'</option>';
	else 
		$html .= '<option value="'.$data_Vendr[$i]['vendorID'].'">'.$data_Vendr[$i]['vendorName'].'</option>';
}

$html .= '</select></td>'.
'</tr>'.
'<tr>'.
'<td align="right">Style:</td>'.
'<td align="left"><input type="text" name="elementstyle" id="elementstyle" value="'.$data_prjElements['style'].'" /></td>'.
'</tr>'.
'<tr>'.
'<td align="right">Color:</td>'.
'<td align="left"><input type="text" name="elementcolor" id="elementcolor" value="'.$data_prjElements['color'].'" /></td>'.
'</tr>'.
'<tr>'.
'<td align="right">Cost:</td>'.
'<td align="left"><input type="text" name="elementcost" id="elementcost" value="'.$data_prjElements['element_cost'].'" /></td>'.
'</tr>'.
'<tr>'.
'<td align="right">Image:</td>'.
'<td align="left">'.
'<input type="file" name="elementimage" id="elementimage" /><input type="button" value="Upload" onMouseOver="this.style.cursor = \'pointer\';" name="btnimage" style="cursor: pointer;" onClick=\'javascript:return ajaxFileUpload("elementimage","I",'.$pid.',"element");\' /></td>'.
'</tr>'.
'<tr>'.
'<td align="right">File:</td>'.
'<td align="left">'.
'<input type="file" name="elementfile" id="elementfile" /><input type="button" value="Upload" onMouseOver="this.style.cursor = \'pointer\';" name="btnfile" style="cursor: pointer;" onClick="javascript:return ajaxFileUpload(\'elementfile\',\'F\','.$pid.',\'element\');" />'.
'<input type="hidden" id="elementid" name="elementid" value="'.$id.'"/>'.
'</td>'.
'</tr>'.
'</table>'.
'</td>'.
'<td valign="top" align="right">'.
'<table  border="0" cellspacing="0" cellpadding="0">';
if($data_prjElements['image'] !="")
{
$html .= '<tr>'.
'<td height="25">Image</td>'.
'</tr>'.
'<tr><td>'.
 '<img src="'.($upload_dir.$data_prjElements['image']).'" width="101" height="89" onClick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');">';	
   
   if($is_session !=1)
   {
   $html .= '<a style="cursor:hand;cursor:pointer;" onClick="javascript:return DeleteUploads(\''.$data_prjElements['prj_element_id'].'\',\''.addslashes($data_prjElements['image']).'\',\''.$pid.'\',\'I\',\'element\');"><img src="'.$mydirectory.'/images/close.png" alt="delete" />';
   }
$html .='</a></td></tr>';
}
if($data_prjElements['elementfile'] != "")
{
   $html .=  '<tr>'.
        '<td height="25">File</td>'.
      '</tr>'.
      '<tr><td>'.
	   '<strong>'.(substr($data_prjElements['elementfile'], (strpos($data_prjElements['elementfile'], "-")+1))).'</strong>'.
	   ' <a href="download.php?file='.$data_prjElements['elementfile'].'"><img src="'.$mydirectory.'/images/Download.png" alt="download"/></a>';
	   if($is_session !=1)
   		{
       $html .='<a style="cursor:hand;cursor:pointer;" onClick="javascript:return DeleteUploads(\''.$data_prjElements['prj_element_id'].'\',\''.addslashes($data_prjElements['elementfile']).'\',\''.$pid.'\',\'F\',\'element\');"><img src="'.$mydirectory.'/images/close.png" alt="delete" /></a>';
		}
   $html .='</td></tr>';
	
}
$html .= '</table>'.
'</td>'.
'</tr>'.
'</table>';
$return_arr['html'] = $html;
echo json_encode($return_arr);
return;
?>