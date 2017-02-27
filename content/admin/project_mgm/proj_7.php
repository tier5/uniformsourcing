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
	$style_price = ' disabled="disabled"';
}
else if(isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] >0 && $_SESSION['employeeType'] == 2))
{
	$emp_type = $_SESSION['employeeType'] ;
	$emp_id =$_SESSION['employee_type_id'];
	$is_session = 1;
	$style_price = ' disabled="disabled"';
}
if(isset($pid)&&$pid!="")
{
$sql = "Select prj_elm.*,elm.elements from tbl_prj_elements$tx as prj_elm left join tbl_elements as elm on elm.element_id=prj_elm.elementtype where prj_elm.status =1 and prj_elm.pid = $pid";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prj_element_all[]  =$row;
	}
	pg_free_result($result);
	if($data_prj_element_all[(count($data_prj_element_all)-1)]['prj_element_id'] != "")
		$elementId = $data_prj_element_all[(count($data_prj_element_all)-1)]['prj_element_id'];
	
	$sql = "Select tbl_mgt_notes$tx.*,e.firstname as \"firstName\", e.lastname as \"lastName\" from tbl_mgt_notes$tx inner join \"employeeDB\" as e on e.\"employeeID\" =tbl_mgt_notes$tx.\"createdBy\"  where \"isActive\" =1 and pid = $pid";
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

	

   $sql = 'select pack_id,pack_name from "tbl_element_pack_main"';
  
   // echo  $sql;
    if (!($result1 = pg_query($connection, $sql))) {
        $return_arr['error'] = pg_last_error($connection);
        echo json_encode($return_arr);
        return;
    }
    while ($rowp = pg_fetch_array($result1)) {
        $packlist[] = $rowp;
    }
    pg_free_result($result1);
    
   /* $sql = 'Select "elm_pack" from "tbl_newproject" where  pid ='. $pid;
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$elm_pack =$row['elm_pack'];
	}
	pg_free_result($result);*/
    
	$q_name='SELECT  list.pack_id,pack.pack_name from "tbl_upload_pack'.$tx.'" as list left join tbl_element_pack_main as pack on pack.pack_id=list.pack_id  where  upload_pack_e=1 AND pid='.$pid;
if(!($result=pg_query($connection,$q_name))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	
	while($row_elm = pg_fetch_array($result)){
		$data_package_list[] =$row_elm;
	}    
 pg_free_result($result);
        
  if(count($data_package_list)>0)
  {
 $upload_dir_pack = "$mydirectory/uploadFiles/element/";

        
  }     
        
$element='<div class="content" align="center">
';
$element.='<table width="80%"><tr align="left"><td align="left">Select A Package: '.
'<select onchange="SelectElementFields($(this).val());"  name="element_pack" id="upload_pack_sel">'.
'<option value="0">--Select--</option>';
//echo "elm".$elm_pack;
for($i=0;$i<count($packlist);$i++)
{
$element.='<option value="'.$packlist[$i]['pack_id'].'"';
       if(isset($elm_pack)&& $elm_pack==$packlist[$i]['pack_id']) 
         $element.=' selected="selected" ';   
     $element.='>';

      $element.=$packlist[$i]['pack_name'].'</option>';   
 }

 $element.='</select></td></tr>';
  $element.='<tr><td id="pack_content">';
 
 
 $html="";
 for($k=0;$k<count($data_package_list);$k++)
 {
	 
	 $sql = 'Select c.client as client_name,pack.pack_name,v."vendorName",elm.* from "tbl_element_package" as elm '.
 ' left join "tbl_element_pack_main" as pack on pack.pack_id=elm.pack_id '.
 ' left join vendor as v on v."vendorID"=elm.vendor_id left join "clientDB" as c on c."ID"=elm.client '.
' where elm.pack_id='.$data_package_list[$k]['pack_id'];
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
        
	if(isset($data_package))   
	unset($data_package);
	
	while($row_elm = pg_fetch_array($result)){
		$data_package[] =$row_elm;
	}     
 pg_free_result($result); 
	 
	 
	 
  $html.='<table width="80%" id="pack_'.$k.'"><tr><td><tr><td><input type="hidden" name="element_packages[]" value="'.$data_package_list[$k]['pack_id'].'"/>'.
  '<strong>Package Name: '.$data_package_list[$k]['pack_name'].'</strong>'.
  '<img  align="right" onclick="$(\'#pack_'.$k.'\').remove()" src="../../images/delete.png" /></td>'.
  '</tr><tr><td>';     
for($j = 0; $j < count($data_package); $j++)
{
	//echo count($data_prj_element_all);
 $html .'<table width="100%">';
	$html .='<tr>'.
	'<td valign="top">'.
	'<table cellpadding="1" cellspacing="1" border="0">'.
	'<tr>'.
	'<td align="right">Element Type:</td>'.
	'<td align="left">
         <input type="text" disabled="disabled" value="'.$data_package[$j]['element_type'].'"/>   
            </td>'.
	'</tr>'.
	'<tr>'.
	'<td align="right">Vendor:</td>'.
	'<td align="left">
     <input type="text" disabled="disabled" value="'.$data_package[$j]['vendorName'].'"/>         
</td>'.
        
	'</tr>'.
	'<tr>
	<td align="right">Quanity:</td>
	<td align="left"><input type="text" disabled="disabled" value"'.$data_package[$j]['elem_quanity'].'" />
	</tr>'.
	'<tr>'.
	'<td align="right">Style:</td>'.
	'<td align="left"><input type="text" disabled="disabled" value="'.$data_package[$j]['style'].'"/>  </td>'.
	'</tr>'.
	'<tr>'.
	'<td align="right">Color:</td>'.
	'<td align="left"><input type="text" disabled="disabled" value="'.$data_package[$j]['color'].'"/> </td>'.
	'</tr>'.
	'<tr>'.
	'<td align="right">Cost:</td>'.
	'<td align="left"><input type="text" disabled="disabled" value="'.$data_package[$j]['cost'].'"/> </td></td>'.
	'</tr>'.
	'<tr>
	<td align="right">Labor:</td>
	<td align="left"><input type="text" disabled="disabled" value="'.$data_package[$j]['labor'].'"/></td>
	</tr>'.
	'<tr>
	<td align="right">Order Date:</td>
	<td align="left"><input type="text" disabled="disabled" value="'.$data_package[$j]['order_date'].'"/></td>
	</tr>'.
	'<tr>
	<td align="right">Confirmation Number:</td>
	<td align="left"><input type="text" disabled="disabled" value="'.$data_package[$j]['elem_conf_num'].'"/></td>
	</tr>'.
	'<tr>
	<td align="right">Tracking Number:</td>
	<td align="left"><input type="text" disabled="disabled" value="'.$data_package[$j]['elem_track_num'].'"/></td>
	</tr>'.
	'<tr>
	<td align="right">Delivered:</td>
	<td align="left"><input type="text" disabled="disabled" value="'.$data_package[$j]['elem_delivered'].'"/></td>
	</tr>'.
	


	'</table>'
	."</td>"
	.'<td valign="top" align="right">'.
	'<table  border="0" cellspacing="0" cellpadding="0">
	<tr >
	<td><strong>Image:</strong><br/>
	<img  width="101px" height="89px" src="';
	if(isset($data_package[$j]['image'])){
		$html .=$upload_dir_pack.$data_package[$j]['image'];
	}
	$html .= '" onClick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" >';
					
	'</td>
	</tr>';
	$html .= '<tr>
	<td><strong>File:</strong><br/>';
	if(isset($data_package[$j]['file']))
            {
		$html .= (substr($data_package[$j]['file'], (strpos($data_package[$j]['file'], "-")+1)));
		//$html .= $data_prjElements['elementfile'];
	}
	$html .= '<a href="download.php?file='.$data_package[$j]['elementfile'].'"><img src="'.$mydirectory.'/images/Download.png" alt="download" /></a>
        </td>
	</tr>';
	
	
	$html .= '</table>';
	
}        
  $html.='</td></tr></table>';           
}
   $element.=$html;
 $element.= '</td></tr>';

 
 $count = 101;
$html = '';
for($j = 0; $j < count($data_prj_element_all); $j++)
{
	//echo count($data_prj_element_all);
	$html .= '<tr><td align="center"><input type="image" ';
        if($emp_type ==1){
                $html .= ' style="visibility:hidden" ';
        }
        $html .= 'class="deleteTd" onclick="javascript:DeleteUploads(\''. $data_prj_element_all[$j]['prj_element_id'].'\',\''.$count++ .'\',\''.$pid.'\',\'I\',\'element\'); DeleteUploads(\''. $data_prj_element_all[$j]['prj_element_id'].'\',\''.$count-- .'\',\''.$pid.'\',\'I\',\'element\'); deleteElement(\''.$data_prj_element_all[$j]['prj_element_id'].'\',\''.$pid.'\')" src="../../images/delete.png"><table width="80%" >'.
	'</td></tr><tr>'.
	'<td valign="top">'.
	'<table cellpadding="1" cellspacing="1" border="0">'.
	'<tr>'.
	'<td align="right">Element Type:</td>'.
	'<td align="left"><select name="elementtype[]" '.$style_price.' id="element_type'.$j.'" onChange="showlocation('.$j.');">';
	for($i=0; $i < count($data_elements); $i++){
		if($data_prj_element_all[$j]['elementtype']==$data_elements[$i]['element_id'])
			$html .= '<option value="'.$data_elements[$i]['element_id'].'" selected="selected">'.$data_elements[$i]['elements'].'</option>';
		else 
			$html .= '<option value="'.$data_elements[$i]['element_id'].'">'.$data_elements[$i]['elements'].'</option>';
	}
	
	$html .= '</select></td>'.
	'</tr>';
       
        
 if( $data_prj_element_all[$j]['elements']=="Fabric")  
 {
      $html .= ' <tr id="location_tr'.$j.'"   style="display:none;"';
   $html.='>';
   $html.=' <td align="right">Location:</td>';
   $html.=' <td align="left"><input type="text" name="location[]" value=""  id="location'. $j.'"  /></td>';
    $html.='</tr>';
   $html.='<tr id="yield_tr'.$j.'" >';
    $html.='  <td align="right">Yield:</td>';
    $html.='  <td align="left"><input type="text" name="yield[]"  value="'.$data_prj_element_all[$j]['yield'].'" id="yield'.$j.'"  /></td>';
   $html.=' </tr> ';    
 }
        
else if($data_prj_element_all[$j]['elements']=="Cups & Pads"||$data_prj_element_all[$j]['elements']=='Boning'||$data_prj_element_all[$j]['elements']=='Buttons'||$data_prj_element_all[$j]['elements']=='CMT'||
$data_prj_element_all[$j]['elements']=='Grade'||$data_prj_element_all[$j]['elements']=='Lap Dip'||$data_prj_element_all[$j]['elements']=='Liner'||$data_prj_element_all[$j]['elements']=='Marker'
||$data_prj_element_all[$j]['elements']=='Packaging'||$data_prj_element_all[$j]['elements']=='Pattern'||$data_prj_element_all[$j]['elements']=='Underwire')
  {
   $html .= ' <tr id="location_tr'.$j.'"   style="display:none;"';
   $html.='>';
   $html.=' <td align="right">Location:</td>';
   $html.=' <td align="left"><input type="text" name="location[]" value=""  id="location'. $j.'"  /></td>';
    $html.='</tr>';
      $html.='<tr id="yield_tr'.$j.'" style="display:none;" >';
    $html.='  <td align="right">Yield:</td>';
    $html.='  <td align="left"><input type="text"  name="yield[]"  value="" id="yield'.$j.'"  /></td>';
  }  
  else{
    $html .= ' <tr id="location_tr'.$j.'"   ';
   $html.='>';
   $html.=' <td align="right">Location:</td>';
   $html.=' <td align="left"><input type="text" name="location[]" value="'.$data_prj_element_all[$j]['location'].'"  id="location'. $j.'"  /></td>';
    $html.='</tr>';
   $html.='<tr id="yield_tr'.$j.'" style="display:none;" >';
    $html.='  <td align="right">Yield:</td>';
    $html.='  <td align="left"><input type="text"  name="yield[]"  value="" id="yield'.$j.'"  /></td>';
  }  
        
        $html.='<tr>'.
	'<td align="right">Vendor:</td>'.
	'<td align="left"><select name="vendor_ID[]" '.$style_price.'>';
	for($i=0; $i < count($data_Vendr); $i++){
		if($data_prj_element_all[$j]['vid']==$data_Vendr[$i]['vendorID'])
			$html .= '<option value="'.$data_Vendr[$i]['vendorID'].'" selected="selected">'.$data_Vendr[$i]['vendorName'].'</option>';
		else 
			$html .= '<option value="'.$data_Vendr[$i]['vendorID'].'">'.$data_Vendr[$i]['vendorName'].'</option>';
	}
	$html .= '</select></td>'.
	'</tr>'.
	'<tr>
	<td align="right">Quanity:</td>
	<td align="left"><input type="text" name="elemquantity[]" id="elemquantity" value="'.$data_prj_element_all[$j]['elem_quanity'].'" '.$style_price.' /></td>
	</tr>'.
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
	<td align="right">Labor:</td>
	<td align="left"><input type="text" name="elementlabor[]" id="elementlabor" value="'.$data_prj_element_all[$j]['element_labor'].'" '.$style_price.' /></td>
	</tr>'.
	'<tr>
	<td align="right">Order Date:</td>
	<td align="left"><input type="text" name="order_date[]" onclick="javascript:showDate(this);" value="'.$data_prj_element_all[$j]['order_date'].'" '.$style_price.' /></td>
	</tr>'.
	'<tr>
	<td align="right">Confirmation Number:</td>
	<td align="left"><input type="text" name="element_conf_num[]" id="element_conf_num" value="'.$data_prj_element_all[$j]['elem_conf_num'].'" '.$style_price.' /></td>
	</tr>'.
	'<tr>
	<td align="right">Tracking Number:</td>
	<td align="left"><input type="text" name="element_track_num[]" id="element_track_num" value="'.$data_prj_element_all[$j]['elem_track_num'].'" '.$style_price.' /></td>
	</tr>'.
	'<tr>
	<td align="right">Delivered:</td>
	<td align="left"><input type="checkbox" name="element_delivered[]" id="element_delivered"  ' ;
     if($data_prj_element_all[$j]['elem_delivered']=="yes")      
     $html .= ' checked="checked"';    
$html .= '/></td>
	</tr>'.
	'<tr>
	<td align="right">Image:</td>
	<td align="left">
	<input type="file" name="file'.$count.'" id="file'.$count.'" onchange="javascript:ajaxFileUpload('.$count.', \'I\', 960,720);" />
	<input type="hidden" id="file_name'.$count.'" name="element_file0[]" value="'.$data_prj_element_all[$j]['image'].'"/>
	<input type="hidden" id="upload_type'.$count.'" name="element_type0[]" value="I"/>
	<input type="hidden" id="upload_id'.$count++ .'" name="element_id0[]" value="'.$data_prj_element_all[$j]['prj_element_id'].'"/>
	</td>
	</tr>'.
	'<tr>
	<td align="right">File:</td>
	<td align="left">
	<input type="file" name="file'.$count.'" id="file'.$count.'" onchange="javascript:ajaxFileUpload('.$count.', \'F\', 960,720);" />
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
		$html .= 'style="display:none;"';
	}
	$html .= '>
	<td><strong>Image:</strong><br/>
	<img id="img_file'.$count.'" width="101px" height="89px" src="';
	if(isset($data_prj_element_all[$j]['image'])){
		$html .= $upload_dir.$data_prj_element_all[$j]['image'];
	}
	$html .= '" onClick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" ><a style="cursor:hand;cursor:pointer;';
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
	<tr id="tr_id'.$count.'" ';
	if(!(isset($data_prj_element_all[$j]['elementfile']) && $data_prj_element_all[$j]['elementfile'] !='')){
		$html .= 'style="display:none;"';
	}
	$html .= '>
	<td><strong>File:</strong><br/>';
	if(isset($data_prj_element_all[$j]['elementfile'])){
		$html .= (substr($data_prj_element_all[$j]['elementfile'], (strpos($data_prj_element_all[$j]['elementfile'], "-")+1)));
		//$html .= $data_prjElements['elementfile'];
	}
	$html .= '<a href="download.php?file='.str_replace("#","%23",$data_prj_element_all[$j]['elementfile']).'"><img src="'.$mydirectory.'/images/Download.png" alt="download" /></a><a ';
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
<table id="content_table" width="80%" border="0" cellspacing="0" cellpadding="0">';
$element.= $html;
$element.= '</table>'.
'<input type="hidden" id="selectedDiv" value="'.$selectedtab.'"  />'.
'<input type="hidden" id="selectedId" value="'.$elementId.'"  />'.
'<input type="hidden" id="elementCount" value="'.$selectedtab.'" />'.
'<input type="button" value="Add New Element" onclick="AddElement()"  '.$style_price.' />';
$return_arr['html'] =$element;
}
echo json_encode($return_arr);
return;
?>