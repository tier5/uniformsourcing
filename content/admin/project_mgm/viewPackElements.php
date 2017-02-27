<?php
require('Application.php');
extract($_POST);
$ret_arr = array();
$ret_arr['pack_id'] = $pack_id;
$ret_arr['res']=""; 

/* if(isset($_GET['element_id']))
{
   // $sql.="Delete from tbl_element_pack_main where pack_id= ".$_GET['element_id'].";";
	$sql.="Delete from tbl_element_package where pack_id = ".$_GET['element_id'];
       // echo $sql;
	if(!($result=pg_query($connection,$sql)))
	{
		print("Failed delete_quote: " . pg_last_error($connection));
		exit;
	}
	header('location:project_mgm.add.php');
}  */ 
if($pack_id > 0){

$upload_dir = "$mydirectory/uploadFiles/element/";
//$query2="INSERT INTO tbl_upload_pack (pid,upload_pack_e";

$sql = 'Select c.client as client_name,pack.pack_name,v."vendorName",elm.* from "tbl_element_package" as elm '.
 ' left join "tbl_element_pack_main" as pack on pack.pack_id=elm.pack_id '.
 ' left join vendor as v on v."vendorID"=elm.vendor_id left join "clientDB" as c on c."ID"=elm.client '.
 ' '       
.' where elm.pack_id='.$pack_id;
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
$html="";
	// $html='<input type="text" name="element_packages[]" value="'.$pack_id.'"/>'.
    
   $html.='<table width="80%" id="pack_'.$pack_id.'">'.
   '<tr><td align="left"><label>Package Name: <strong> '.
	''.$data_package[0]['pack_name'].'</strong></label></td></tr>'.
   '<tr><td>'.
   '<input type="hidden" name="element_packages[]" value="'.$pack_id.'"/>';
for($j = 0; $j < count($data_package); $j++)
{
	//echo count($data_prj_element_all);
 $html .='<table width="100%" >';
	$html .='<tr>'.
	'<td valign="top">'.
	'<table cellpadding="1" cellspacing="1" border="0" >'.
	'<tr>';
	//<a href="viewPackElements.php?element_id='.$data_package[$j]['pack_id'].'"><img src="../../images/delete.png" /></a></td>'
//$html .='<a href="viewPackElements.php?element_id='.$data_package[$j]['pack_id'].'"><img src="../../images/delete.png" /></a></td>';
	$html .='<tr>'.
	'<td align="right">Element Type:</td>'.
	'<td align="left"> <input type="text" disabled="disabled" value="'.$data_package[$j]['element_type'].'"/></td>'.
	'</tr>';
                
        /*  if($data_package[$j]['location']!="")
          {
              $html .='<tr>'.
	'<td align="right">Location:</td>'.
	'<td align="left"> <input type="text" disabled="disabled" value="'.$data_package[$j]['location'].'"/></td>'.
	'</tr>';
          } 
          else  if($data_package[$j]['yield']!="")
          {
              $html .='<tr>'.
	'<td align="right">Yield:</td>'.
	'<td align="left"> <input type="text" disabled="disabled" value="'.$data_package[$j]['yield'].'"/></td>'.
	'</tr>';
          } */
            
          
    if( $data_package[$j]['element_type']=="Fabric")  
 {
     
   $html.='<tr id="yield_tr'.$j.'" >';
    $html.='  <td align="right">Yield:</td>';
    $html.='  <td align="left"><input type="text" name="yield[]" disabled="disabled"  value="'.$data_package[$j]['yield'].'" id="yield'.$j.'"  /></td>';
   $html.=' </tr> ';    
 }
        
else if($data_package[$j]['element_type']=="Cups & Pads"||$data_package[$j]['element_type']=='Boning'||$data_package[$j]['element_type']=='Buttons'||$data_package[$j]['element_type']=='CMT'||
$data_package[$j]['element_type']=='Grade'||$data_package[$j]['element_type']=='Lap Dip'||$data_package[$j]['element_type']=='Liner'||$data_package[$j]['element_type']=='Marker'
||$data_package[$j]['element_type']=='Packaging'||$data_package[$j]['element_type']=='Pattern'||$data_package[$j]['element_type']=='Underwire')
  {
  
  }  
  else{
    $html .= ' <tr id="location_tr'.$j.'"   ';
   $html.='>';
   $html.=' <td align="right">Location:</td>';
   $html.=' <td align="left"><input type="text" name="location[]" value="'.$data_package[$j]['location'].'"  id="location'. $j.'"  /></td>';
    $html.='</tr>';
  
  }       
          
          
          
          
	$html .='<tr>'.
	'<td align="right">Vendor:</td>'.
	'<td align="left"> <input type="text" disabled="disabled" value="'.$data_package[$j]['vendorName'].'"/></td>'.
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
	'<td align="left"><input type="text" disabled="disabled" value="'.$data_package[$j]['cost'].'"/> </td>'.
	'</tr>'.
	
	'</table>'.
	"</td>".
	
	'<td valign="top" align="center"><img  onclick="$(\'#pack_'.$pack_id.'\').remove()" src="../../images/delete.png" /></td>'.
	
	'<td valign="top" align="right">'.
	'<table  border="0" cellspacing="0" cellpadding="0">'.
	'<tr >'.
	'<td><strong>Image:</strong><br/>'.
	'<img  width="101px" height="89px" src="';
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
        </td>';
	
		$html .='</tr></table></td>';
	
	$html .='</tr>';
	
	
	$html .= '</table>';
	
}        
  $html.='</td></tr></table><br/>';           
        
        
        
        
        
$ret_arr['res']=$html;        
}
header('Content-type: application/json');
echo json_encode($ret_arr);
?>















