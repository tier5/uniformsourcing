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
$is_session =0;
$emp_type ="";
$emp_id= "";
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

$isEdit = 0;
$patternId = 0;
$gradientId = 0;
if(isset($_POST['pid']) && $_POST['pid']!=0){
	
	
	$isEdit = 1;
	$sql = "Select * from tbl_prjimage_file$tx where status =1 and pid =$pid ";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	//echo $sql;
	while($row = pg_fetch_array($result)){
		$data_prjUploads[] =$row;
	}

	$imageArr = array();
	$fileArr = array();
	$pattern = "";
	$gradient = "";
	for($i = 0, $img= 0, $file = 0; $i < count($data_prjUploads); $i++)
	{
		if($data_prjUploads[$i]['type'] == 'P')
		{
			$patternId = $data_prjUploads[$i]['prjimageId'];
			$pattern = stripslashes($data_prjUploads[$i]['file_name']);
		}
		else if($data_prjUploads[$i]['type'] == 'G')
		{
			$gradientId = $data_prjUploads[$i]['prjimageId'];
			$gradient = stripslashes($data_prjUploads[$i]['file_name']);
		}
		else if($data_prjUploads[$i]['type'] == 'I')
		{
			$imageArr[$img]['id'] = $data_prjUploads[$i]['prjimageId'];
			$imageArr[$img++]['file'] = stripslashes($data_prjUploads[$i]['file_name']);
		}
		else if($data_prjUploads[$i]['type'] == 'F')
		{
			$fileArr[$file]['id'] = $data_prjUploads[$i]['prjimageId'];
			$fileArr[$file++]['file'] = stripslashes($data_prjUploads[$i]['file_name']);
		}
	}
	pg_free_result($result);
        
  /*$sql = 'SELECT "upload_pack" FROM "tbl_newproject"  "img_file_pack" where "pid"='.$pid;
//echo $sql;
if (!($result = pg_query($connection, $sql))) {
    print("Failed query: " . pg_last_error($connection));
    exit;
}
$row2 = pg_fetch_array($result);
	$data_pack_id=$row2['upload_pack'];  */
	
$q_name='SELECT  list.pack_id,pack.pack_name from "tbl_upload_pack'.$tx.'" as list left join img_file_pack as pack on pack.pack_id=list.pack_id  where  upload_pack_u=1 AND pid='.$pid;
if(!($result=pg_query($connection,$q_name))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	
	while($row_elm = pg_fetch_array($result)){
		$data_package_list[] =$row_elm;
	}    
 pg_free_result($result);
        
//print_r($data_package_list);	
 
 
 
 
 
 
 $sql = 'SELECT "client" FROM "tbl_newproject'.$tx.'" where pid='.$pid;
//echo $sql;
if (!($result = pg_query($connection, $sql))) {
    print("Failed query: " . pg_last_error($connection));
    exit;
}
$rowc = pg_fetch_array($result);
		$d_client=$rowc['client'];
        pg_free_result($result);     
        
 if(isset($d_client)&&$d_client!='')
 {
 $sql = 'SELECT pack."pack_id",pack."pack_name" FROM "img_file_pack" as pack left join "img_file_clients" as cl on cl.pack_id=pack.pack_id where cl.cid='.$d_client;
//echo $sql;
if (!($result = pg_query($connection, $sql))) {
    print("Failed query: " . pg_last_error($connection));
    exit;
}
while($rowp = pg_fetch_array($result)){
		$data_package[] =$rowp;
	}
 
  pg_free_result($result);
 
 }
}



$imageArr_pack = array();
	$fileArr_pack = array();
   // if(isset($data_pack_id)&& trim($data_pack_id)!="")  
    
        
        $count = 0;
        
  $html = '<table width="90%" >
<tr>
<td > ';   
  
  $html = '<table width="90%" >
<tr>
<td valign="top" width="30%">

<table cellpadding="1" cellspacing="1" border="0">
<tr><td>Select A Package:</td>
<td><select onchange="viewPackFiles();" name="upload_pack" id="upload_pack">
<option value="0">--Select--</option>';
for($i=0;$i<count($data_package);$i++)
{
 $html .='<option value="'.$data_package[$i]['pack_id'].'"';
  if(isset($data_pack_id)&& trim($data_pack_id)!=""&&$data_pack_id==$data_package[$i]['pack_id'])  
     $html .=' selected="selected" ';  
     $html .=' >'.$data_package[$i]['pack_name'].'</option>';   
}

$html .='</select></td>
</tr>
<tr><td>&nbsp;</td></tr>';
if(0){
$html .='<tr>
<td align="right">Pattern:</td>
<td align="left">
<input type="file" name="file0" id="file0" onchange="javascript:ajaxFileUpload(0, \'P\', 960,720);" />
<input type="hidden" id="file_name'.$count.'" name="upload_file[]" value="'.$pattern.'"/>
<input type="hidden" id="upload_type'.$count.'" name="upload_type[]" value="P"/>
<input type="hidden" id="upload_id'.$count++.'" name="upload_id[]" value="'.$patternId.'"/>
</td>
</tr>
<tr>
<td align="right">Grading:</td>
<td align="left">
<input type="file" name="file1" id="file1" onchange="javascript:ajaxFileUpload(1, \'G\', 960,720);"/>
<input type="hidden" id="file_name'.$count.'" name="upload_file[]" value="'.$gradient.'"/>
<input type="hidden" id="upload_type'.$count.'" name="upload_type[]" value="G"/>
<input type="hidden" id="upload_id'.$count++.'" name="upload_id[]" value="'.$gradientId.'"/>
</td>
</tr>';
}
$html .='<tr>
<td align="right">Image:</td>
<td align="left">
<input type="file" name="file2" id="file2" onchange="javascript:ajaxFileUpload(2, \'I\', 960,720);"/>
</td>
</tr>
<tr>
<td align="right">File:</td>
<td align="left">
<input type="file" name="file3" id="file3" onchange="javascript:ajaxFileUpload(3, \'F\', 960,720);"/>
</td>
</tr>
</table>';
$html.='</td>'; 


$html.='<td>'; 

 

if(count($data_package_list)>0)
    {
   for($j=0;$j<count($data_package_list);$j++)
   {
       

$sql = 'Select item.*,pack."pack_name" from "img_file_items" as item left join "img_file_pack" as pack on pack."pack_id"=item."pack_id" '.
          ' where item."pack_id"='.$data_package_list[$j]['pack_id'];
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	//echo $sql."<br/>";
	//print_r($row3);
	if(isset($row3)) unset($row3);
	if(isset($data_prjUploads_pack)) unset($data_prjUploads_pack);
	while($row3 = pg_fetch_array($result)){
		$data_prjUploads_pack[] =$row3;
	}
	pg_free_result($result);
//print_r($data_prjUploads);
        
        
        
        
     /*      $sql='select client.cid,cldb.client from img_file_clients as client left join "clientDB" as cldb on cldb."ID"=client.cid where pack_id='.$data_package_list[$j]['pack_id'];
       //echo $sql;
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	if(isset($data_client)) unset($data_client);
	while($row = pg_fetch_array($result)){
		$data_client[] =$row;
	}
        pg_free_result($result);
$sql='select style,img_style_id from img_file_styles as style where pack_id='.$data_package_list[$j]['pack_id'];
       //echo $sql;
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	if(isset($data_style)) unset($data_style);
	while($row = pg_fetch_array($result)){
		$data_style[] =$row;
	}     
        pg_free_result($result);*/

	if(isset($imageArr_pack)) 
	unset($imageArr_pack);
	//print_r($imageArr_pack);
	if(isset($fileArr_pack)) unset($fileArr_pack);
	$pattern_pack = "";
	$gradient_pack = "";
	$img=0; $file=0;
	for($i = 0, $img= 0, $file = 0; $i < count($data_prjUploads_pack); $i++)
	{
		 if($data_prjUploads_pack[$i]['type'] == 'I')
		{
			$imageArr_pack[$img]['id'] = $data_prjUploads_pack[$i]['item_id'];
			$imageArr_pack[$img++]['file'] = stripslashes($data_prjUploads_pack[$i]['filename']);
		}
		else if($data_prjUploads_pack[$i]['type'] == 'F')
		{
			$fileArr_pack[$file]['id'] = $data_prjUploads_pack[$i]['item_id'];
			$fileArr_pack[$file++]['file'] = stripslashes($data_prjUploads_pack[$i]['filename']);
		}
	}
	
   // print_r($imageArr_pack);
 $upload_dir = "$mydirectory/uploadFiles/image_file/";
 
 
 
  $html .='<table width="100%" id="upld_pack_'.$data_package_list[$j]['pack_id'].'"><tr>'.
 ' <td>Style Number: <strong>'.$data_package_list[$j]['pack_name'].'</td> '.'<td valign="top">'. 
 '<img  onclick="$(\'#upld_pack_'.$data_package_list[$j]['pack_id'].'\').remove()" src="../../images/delete.png" /></td></tr>';
  
  
   $html .='<tr><td width="100%">';
    $html .='<input type="hidden" name="upload_packages[]" value="'.$data_package_list[$j]['pack_id'].'"/>';
   
    $html .='<table width="100%"><tr><td width="30%"><strong>Files</strong></td></tr>';
    
    $html .='<tr>';
    /*<td valign="top"> <table>
    
    for($i=0;$i<count($data_client);$i++)
    {
$html .='<tr height="30"><td>'.$data_client[$i]['client'].
'</td></tr>';      
    }

  $html .='</table></td>';  
    
  
  $html .='<td valign="top"><table>';
   for($i=0;$i<count($data_style);$i++)
    {
$html .='<tr height="30" ><td>'.$data_style[$i]['style'].
'</td></tr>';
    }
  $html .='</table></td>'; */
    
    $html.='<td valign="top">';
    if(count($fileArr_pack))
{
    $html .='<table>';
	for($i=0; $i<count($fileArr_pack); $i++,$count++)
	{

$html .= '<tr id="tr_id'.$count.'">
        <td>';
		
		if($fileArr_pack[$i] != "")
		{     
			$html .= '<strong>';
			$html .= (substr($fileArr_pack[$i]['file'], (strpos($fileArr_pack[$i]['file'], "-")+1)));
			$html .= '</strong>
            <a href="download_file_pack.php?file=';
			$html .= $fileArr_pack[$i]['file'];
			$html .= '"><img src="';
			$html .= $mydirectory;
			$html .= '/images/Download.png" alt="download"/></a>';
      

																					
        }

      $html .= '</td></tr>';
	}
         $html .='</table>';
}

$html .='</td></tr>';
    
     $html .='</table>';
  $html .='</td><tr>';
  
 
   $html .='<tr><td width="100%">';
  
  if(count($imageArr_pack))
{
 
  $html .= "Images:<br/>";
	for($i=0; $i<count($imageArr_pack); $i++,$count++)
	{
		if($imageArr_pack[$i] != "" )
		{         
$html .= '<img  style="float:left;padding:5px;" src="';
$html .= $upload_dir.$imageArr_pack[$i]['file'];
$html .= '" width="101" height="89" onClick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');">';
        
		}
     

	}
}



$html .='</td><tr></table>';

   }
    }






$html.='</td></tr>';
    
  $html.='<tr> <td>&nbsp;</td><td colspan="3"  id="pack_view"></td></tr>';
   $html.='<tr><td></td><td>';
   
   
   
   
   
  $html.= '<table width="100%">'; 	 
  $upload_dir = "$mydirectory/uploadFiles/project_mgm/";       
$html .=' <tr id="tr_id0" ';
      
if(!(isset($pattern) && $pattern!='')){
	$html .= ' style="display:none;"';
}
$html .= '>
<td><strong>Pattern:</strong><br/>
<img id="img_file0" width="101px" height="89px" src="';
if(isset($pattern)){
	$html .= $upload_dir.$pattern;
}
$html .= '" onClick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" >
 <a style="cursor:hand;cursor:pointer;';
							if($emp_type ==1){
								$html .= 'visibility:hidden;';
							}
							$html .= '" 
   onClick="javascript: DeleteUploads(\'';
$html .= $patternId.'\',0,\''.$pid.'\',\'P\',\'upload\'); document.getElementById(\'tr_id0\').style.display=\'none\'; document.getElementById(\'file_name0\').value=\'\';"><img src="';
$html .= $mydirectory;
$html .= '/images/close.png" alt="delete" />
    </a>
</td>
</td>
</tr>
<tr id="tr_id1" ';
if(!(isset($gradient) && $gradient!='')){
	$html .= 'style="display:none;"';
}
$html .= '>
<td><strong>Gradient:</strong><br/>
<img id="img_file1" width="101px" height="89px" src="';
if(isset($gradient)){
	$html .= $upload_dir.$gradient;
}
$html .= '" onClick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" ><a style="cursor:hand;cursor:pointer;';
							if($emp_type ==1){
								$html .= 'visibility:hidden;';
							}
							$html .= '" 
   onClick="javascript: DeleteUploads(\'';
$html .= $gradientId.'\',1,\''.$pid.'\',\'G\',\'upload\');javascript:document.getElementById(\'tr_id1\').style.display=\'none\'; document.getElementById(\'file_name1\').value=\'\';"><img src="';
$html .= $mydirectory;
$html .= '/images/close.png" alt="delete" />
    </a>
</td>
</tr>';


if(count($imageArr))
{
  $html .= ' <tr><td> <strong>Images:</strong></td></tr><tr><td >';
	for($i=0; $i<count($imageArr); $i++,$count++)
	{

$html .= '<div style="float:left;padding:5px;" id="tr_id'.$count.'">';
		if($imageArr[$i] != "" )
		{         
$html .= '<img  src="';
$html .= $upload_dir.$imageArr[$i]['file'];
$html .= '" width="101" height="89" onClick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');">
             <a style="cursor:hand;cursor:pointer;';
							if($emp_type ==1){
								$html .= 'visibility:hidden;';
							}
							$html .= '" onClick="javascript: DeleteUploads(\'';
$html .= $imageArr[$i]['id'];
$html .= '\',\'';
$html .= addslashes($imageArr[$i]['file']);
$html .= '\',\'';
$html .= $pid;
$html .= '\',\'I\',\'upload\'); $(this).parent().remove();"><img src="';
$html .= $mydirectory;
$html .= '/images/close.png" alt="delete" />
    </a><input type="hidden" id="file_name'.$count.'" name="upload_file[]" value=""/>
<input type="hidden" id="upload_type'.$count.'" name="upload_type[]" value="I"/>
<input type="hidden" id="upload_id'.$count.'" name="upload_id[]" value="'.$imageArr[$i]['id'].'"/>';
		}
     
$html .= '</div>';
	}
       $html .= ' </td></tr>';
}
if(count($fileArr))
{
    $html .= '<tr><td>Files:</td></tr>';
	for($i=0; $i<count($fileArr); $i++,$count++)
	{

$html .= '<tr id="tr_id'.$count.'">
        <td>';
		
		if($fileArr[$i]['file'] != "")
		{     
			$html .= '<strong>';
			$html .= (substr($fileArr[$i]['file'], (strpos($fileArr[$i]['file'], "-")+1)));
			$html .= '</strong>
            <a href="download.php?file=';
			$html .= str_replace("#","%23",$fileArr[$i]['file']);
			$html .= '"><img src="';
			$html .= $mydirectory;
			$html .= '/images/Download.png" alt="download"/></a>
             <a ';
			if($emp_type ==1){
				$html .= 'style="visibility:hidden;"';
			}
							$html .= 'href="javascript:void(0);" onClick="javascript: DeleteUploads(\'';
									$html .= $fileArr[$i]['id'];
									$html .= '\',\'';
									$html .= addslashes($fileArr[$i]['file']);
									$html .= '\',\'';
									$html .= $pid;
									$html .= '\',\'F\',\'upload\'); DeleteSingleRow(this);"><img src="';
			 						$html .= $mydirectory.'/images/close.png" alt="delete"/></a><input type="hidden" id="file_name'.$count.'" name="upload_file[]" value=""/>
<input type="hidden" id="upload_type'.$count.'" name="upload_type[]" value="F"/>
<input type="hidden" id="upload_id'.$count.'" name="upload_id[]" value="'.$fileArr[$i]['id'].'"/>';
																					
        }

      $html .= '</td>
      </tr>';
      
	}
}
$html.= '<tr align="right"> <td >&nbsp;</td><td ><table  border="0" cellspacing="0" cellpadding="0" id="image_view" width="100%"/></td></tr>';

$html .= '<tr>
<td>
<div id="img_file3"></div>';
$html .= '</td>
</tr>';	  
$html .= '</table>'.
'</div></td></tr></table></div>'.
'</td>'.
'</tr>';

$html.= '</table>';
$html.= '</td></tr></table>';

  $html.= '</table>';  
   
   
    $return_arr['html'] =$html;

echo json_encode($return_arr);
return;
?>