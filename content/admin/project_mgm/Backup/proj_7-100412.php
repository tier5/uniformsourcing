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
	$style_price = ' disabled="disabled"';
}
else if(isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] >0 && $_SESSION['employeeType'] == 2))
{
	$emp_type = $_SESSION['employeeType'] ;
	$emp_id =$_SESSION['employee_type_id'];
	$is_session = 1;
	$style_price = ' disabled="disabled"';
}
$sql = "Select * from tbl_prj_elements where status =1 and pid = $pid";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prj_element_all[]  =$row;
	}
	pg_free_result($result);
       // echo $sql;
      //  print_r($data_prj_element_all);
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

	
	
	
	$sql = 'Select "package","element_id" from "tbl_element_package" ';
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	//echo $sql;
	while($row_elm = pg_fetch_array($result)){
		$data_package[] =$row_elm;
	}
	
	
	

$element='<div class="content" align="center">';

$count = 101;
$html = '';
for($j = 0; $j < count($data_prj_element_all); $j++)
{
	//echo count($data_prj_element_all);
        
	$html .= '<tr id="element'.$j.'"><td align="center"><input type="image" ';
        if($emp_type ==1){
                $html .= ' style="visibility:hidden" ';
        }
        $html .= 'class="deleteTd" onclick="javascript:DeleteUploads(\''. $data_prj_element_all[$j]['prj_element_id'].'\',\''.$count++ .'\',\''.
                $pid.'\',\'I\',\'element\'); DeleteUploads(\''. $data_prj_element_all[$j]['prj_element_id'].'\',\''.
                $count-- .'\',\''.$pid.'\',\'I\',\'element\'); deleteElement(\''.$data_prj_element_all[$j]['prj_element_id'].'\',\''.
                $pid.'\')" src="../../images/delete.png"><table width="80%" >';	
        $html .= '<tr>'.
	'<td valign="top">'.
	'<table cellpadding="1" cellspacing="1" border="0" >';
        $html .='<tr><td align="right">Select a defult Package:</td>'.
            '<td align="left"><select onchange="SelectElementFields('.$j.','.$count.')" name="element_pack[]" id="upload_pack'.$j.'">'
        .'<option value="0">--Select--</option>';
        for($i=0;$i<count($data_package);$i++)
        {
            if($data_package[$i]['element_id'] == $data_prj_element_all[$j]['pack_id'])
                $html.='<option selected="selected" value="'. $data_package[$i]['element_id'].'" >'.$data_package[$i]['package'].'</option>';   
            else 
                $html.='<option value="'. $data_package[$i]['element_id'].'" >'.$data_package[$i]['package'].'</option>';   
        } 
        $html.='</select></td></tr>';
	$html .= '<tr>'.
	'<td align="right">Element Type:</td>'.
	'<td align="left"><select id="element_type" name="elementtype[]" '.$style_price.'>';
	for($i=0; $i < count($data_elements); $i++){
		if($data_prj_element_all[$j]['elementtype']==$data_elements[$i]['element_id'])
			$html .= '<option value="'.$data_elements[$i]['element_id'].'" selected="selected">'.$data_elements[$i]['elements'].'</option>';
		else 
			$html .= '<option value="'.$data_elements[$i]['element_id'].'">'.$data_elements[$i]['elements'].'</option>';
	}
	
	$html .= '</select></td>'.
	'</tr>'.
	'<tr>'.
	'<td align="right">Vendor:</td>'.
	'<td align="left"><select id="element_vendor" name="vendor_ID[]" '.$style_price.'>';
	for($i=0; $i < count($data_Vendr); $i++){
		if($data_prj_element_all[$j]['vid']==$data_Vendr[$i]['vendorID'])
			$html .= '<option value="'.$data_Vendr[$i]['vendorID'].'" selected="selected">'.$data_Vendr[$i]['vendorName'].'</option>';
		else 
			$html .= '<option value="'.$data_Vendr[$i]['vendorID'].'">'.$data_Vendr[$i]['vendorName'].'</option>';
	}
	$html .= '</select></td>'.
	'</tr>'.
	'<tr>'.
	'<td align="right">Style:</td>'.
	'<td align="left"><input type="text" name="elementstyle[]" id="elementstyle" value="'.$data_prj_element_all[$j]['style'].'" '.$style_price.' /></td>'.
	'</tr>'.
	'<tr>'.
	'<td align="right">Color:</td>'.
	'<td align="left"><input type="text" name="elementcolor[]" id="elementcolor" value="'.$data_prj_element_all[$j]['color'].'"  '.$style_price.' /></td>'.
	'</tr>'.
	'<tr>'.
	'<td align="right">Cost:</td>'.
	'<td align="left"><input type="text" name="elementcost[]" id="elementcost" value="'.$data_prj_element_all[$j]['element_cost'].'" '.$style_price.' /></td>'.
	'</tr>'.
	'<tr>
	<td align="right">Image:</td>
	<td align="left">
	<input type="file" name="file'.$count.'" id="file'.$count.'" onchange="javascript:ajaxFileUpload('.$count.', \'I\', 960,720);" />
             <input type="hidden" id="image_from'.$count.'" name="image_from[]" ';
        if(isset($data_prj_element_all[$j]['img_type']))
        $html .= ' value="'.$data_prj_element_all[$j]['img_type'].'" ';
        else
              $html .= ' value="CURR" ';
	$html .= '/><input type="hidden" id="file_name'.$count.'" name="element_file0[]" value="'.$data_prj_element_all[$j]['image'].'"/>
	<input type="hidden" id="upload_type'.$count.'" name="element_type0[]" value="I"/>
	<input type="hidden" id="upload_id'.$count++ .'" name="element_id0[]" value="'.$data_prj_element_all[$j]['prj_element_id'].'"/>
           
	</td>
	</tr>'.
	'<tr>
	<td align="right">File:</td>
	<td align="left">
	<input type="file" name="file'.$count.'" id="file'.$count.'" onchange="javascript:ajaxFileUpload('.$count.', \'F\', 960,720);" />
             <input type="hidden" id="file_from'.$count.'" name="file_from[]"';
           if(isset($data_prj_element_all[$j]['file_type']))
        $html .= ' value="'.$data_prj_element_all[$j]['file_type'].'" ';
        else
              $html .= ' value="CURR" ';
        $html.='/>
	<input type="hidden" id="file_name'.$count.'" name="element_file1[]" value="'.$data_prj_element_all[$j]['elementfile'].'"/>
	<input type="hidden" id="upload_type'.$count--.'" name="element_type1[]" value="F"/>
	</td>
	</tr>'.
	'</tr>'.
	'</table>'.
	'<input type="hidden" id="element_id" name="element_id[]" value="'.$data_prj_element_all[$j]['prj_element_id'].'"/>
	</td>'.
	'<td valign="top" align="right">'.
	'<table  border="0" cellspacing="0" cellpadding="0">
	<tr id="tr_id'.$count.'" ';
	if(!(isset($data_prj_element_all[$j]['image']) && $data_prj_element_all[$j]['image']!='')){
		$html .= ' style="display:none;" ';
	}
	$html .= '>
	<td><strong>Image:</strong><br/>
	<img id="img_file'.$count.'" width="101px" height="89px" src="';
	if(isset($data_prj_element_all[$j]['image'])){
            if($data_prj_element_all[$j]['img_type']=="CURR")
		$html .= $upload_dir.$data_prj_element_all[$j]['image'];
            else
                $html .= $upload_dir_elm_pack.$data_prj_element_all[$j]['image'];
	}
	$html .= '" onClick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" ><a id="del_img" style="cursor:hand;cursor:pointer;';
							if($emp_type ==1){
								$html .= 'visibility:hidden;';
							}
							$html .= '" 
	   onClick=" javascript: DeleteUploads(\''. $data_prj_element_all[$j]['prj_element_id'].'\',\''.$count.'\',\''.$pid.'\',\'I\',\'element\'); document.getElementById(\'tr_id'.$count.'\').style.display=\'none\'; document.getElementById(\'file_name'.$count++.'\').value=\'\'; " ><img src="';
	$html .= $mydirectory;
	$html .= '/images/close.png" alt="delete" />
		</a>
	</td>
	</tr>
	<tr id="tr_id'.($count).'" ';
	if(!(isset($data_prj_element_all[$j]['elementfile']) && $data_prj_element_all[$j]['elementfile'] !='')){
		$html .= 'style="display:none;"';
	}
	$html .= '>
	<td ><strong>File:</strong><br/>';
	if(isset($data_prj_element_all[$j]['elementfile'])){
		$html .= (substr($data_prj_element_all[$j]['elementfile'], (strpos($data_prj_element_all[$j]['elementfile'], "-")+1)));
		//$html .= $data_prjElements['elementfile'];
	}
	$html .= '<a href="download.php?file='.$data_prj_element_all[$j]['elementfile'].'"><img src="'.$mydirectory.'/images/Download.png" alt="download" /></a><a ';
							if($emp_type ==1){
								$html .= 'style="visibility:hidden;"';
							}
							$html .= 'href="javascript:void(0);" onClick="javascript:DeleteUploads(\''.$data_prj_element_all[$j]['prj_element_id'].'\',\''.$count.'\',\''.$pid.'\',\'F\',\'element\'); document.getElementById(\'tr_id'.$count.'\').style.display=\'none\'; document.getElementById(\'file_name'.$count++.'\').value=\'\';"><img src="'.$mydirectory.'/images/close.png" alt="delete"/></a></td>
	</tr>';
	
	
	
	
	
	$html .= '</table>'.
	'</td>'.
	'</tr>'.
	'</table></td></tr>';
}
$element.='
<input type="hidden" id="selectedDiv" value="'.$selectedtab.'"  />
<input type="hidden" id="selectedId" value="'.$elementId.'"  />
<input type="hidden" id="elementCount" value="'.$selectedtab.'" />
<table id="content_table" width="100%" border="0" cellspacing="0" cellpadding="0">';
$element.= $html;
$element.= '</table>';
/*Select a defult Package: <select onchange="AddElementPackage($(this).val());" name="upload_pack" id="upload_pack">
<option value="0">--Select--</option>';
for($i=0;$i<count($data_package);$i++)
{
$element.='<option value="'.$data_package[$i]['element_id'].'"';
  if(isset($data_pack_id)&& trim($data_pack_id)!=""&&$data_pack_id==$data_package[$i]['pack_id'])  
    $element.=' selected="selected" ';  
     $element.=' >'.$data_package[$i]['package'].'</option>';   
}

$element.='</select>*/
$element.='<input type="button" value="Add New Element" onclick="AddElement()"  '.$style_price.' />';
$return_arr['html'] =$element;
echo json_encode($return_arr);
return;
?>