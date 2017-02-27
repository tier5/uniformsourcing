<?php
require('Application.php');
extract($_POST);
$ret_arr = array();
$ret_arr['pack_id'] = $pack_id;
$ret_arr['res']=""; 

if($pack_id > 0){

$upload_dir = "$mydirectory/uploadFiles/element/";

    

$sql = 'Select pack.pack_name,v."vendorName",elm.* from "tbl_element_package" as elm '.
 ' left join "tbl_element_pack_main" as pack on pack.pack_id=elm.pack_id '.
 ' left join vendor as v on v."vendorID"=elm.vendor_id'.
' where elm.pack_id='.$pack_id;
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
        
	//echo $sql;
	while($row_elm = pg_fetch_array($result)){
		$data_package[] =$row_elm;
	}     
 pg_free_result($result);
//print_r($data_package);
        
   $html='<table width="80%"><tr><td>';     
for($j = 0; $j < count($data_package); $j++)
{
	//echo count($data_prj_element_all);
 $html .'<table width="100%"><tr><td>';
	$html .='<tr>'.
	'<td valign="top">'.
	'<table cellpadding="1" cellspacing="1" border="0">'.
	'<tr>'.
	'<td align="right">Element Type:</td>'.
	'<td align="left">
         <input type="text" disabled="disabled" value="'.$data_package[$j]['element_id'].'"/>   
            </td>'.
	'</tr>'.
	'<tr>'.
	'<td align="right">Vendor:</td>'.
	'<td align="left">
     <input type="text" disabled="disabled" value="'.$data_package[$j]['vendorName'].'"/>         
</td>'.
	'</tr>'.
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
	


	'</table>'
	."</td>"
	.'<td valign="top" align="right">'.
	'<table  border="0" cellspacing="0" cellpadding="0">
	<tr >
	<td><strong>Image:</strong><br/>
	<img  width="101px" height="89px" src="';
	if(isset($data_package[$j]['image'])){
		$html .=$upload_dir.$data_package[$j]['image'];
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
        
        
        
        
        
$ret_arr['res']=$html;        
}
header('Content-type: application/json');
echo json_encode($ret_arr);
?>















