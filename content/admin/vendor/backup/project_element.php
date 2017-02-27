<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
$id = 0;
$pid = 0;
$return_arr = array();
$return_arr['html'] = "";
$return_arr['error'] = "";
if(isset($_POST['id']))
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
		 "FROM tbl_elements as e ".
		 "WHERE status = '1' inner join tbl_prj_elements pr on pr.prj_element_id = e.element_id";
	if(!($result=pg_query($connection,$sql))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_elements=$row;
}
pg_free_result($result);

$queryVendor="SELECT \"vendorID\", \"vendorName\", \"active\" ".
		 "FROM \"vendor\" as v inner join tbl_prjvendor as pv on pv.vid = v.\"vendorID\"  ".
		 "WHERE v.\"active\" = 'yes'";
		 //echo $queryVendor;
	if(!($result=pg_query($connection,$queryVendor))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_Vendr=$row;
}
pg_free_result($result);


$html = '<table width="80%" >'.
'<tr>'.
'<td valign="top">'.
'<table cellpadding="1" cellspacing="1" border="0">'.
'<tr>'.
'<td align="right">Element Type:</td>'.
'<td align="left">'.$data_elements['elements'].'</td>'.
'</tr>'.
'<tr>'.
'<td align="right">Vendor:</td>'.
'<td align="left">'.$data_Vendr['vendorName'].'</td>'.
'</tr>'.
'<tr>'.
'<td align="right">Style:</td>'.
'<td align="left">'.$data_prjElements['style'].'</td>'.
'</tr>'.
'<tr>'.
'<td align="right">Color:</td>'.
'<td align="left">'.$data_prjElements['color'].'</td>'.
'</tr>'.
'<tr>'.
'<td align="right">Cost:</td>'.
'<td align="left">'.$data_prjElements['element_cost'].'</td>'.
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
 '<img src="'.($upload_dir.$data_prjElements['image']).'" width="101" height="89" onClick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');">'.	
   '</td></tr>';
}
if($data_prjElements['elementfile'] != "")
{
   $html .=  '<tr>'.
        '<td height="25">File</td>'.
      '</tr>'.
      '<tr><td>'.
	   '<strong>'.(substr($data_prjElements['elementfile'], (strpos($data_prjElements['elementfile'], "-")+1))).'</strong>'.
       '</td></tr>';
	
}
$html .= '</table>'.
'</td>'.
'</tr>'.
'</table>';
$return_arr['html'] = $html;
echo json_encode($return_arr);
return;
?>