<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
$error = "";
$msg = "";
$html = "";
$return_arr = array();
extract($_POST);
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
	$sql = "Select * from tbl_prjimage_file where status =1 and pid =$pid ";
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
	
}

$count = 0;
$html = '<table width="90%" >
<tr>
<td valign="top">

<table cellpadding="1" cellspacing="1" border="0">
<tr>
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
</tr>
<tr>
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
</table>
</td>
<td valign="top" align="right">
<table  border="0" cellspacing="0" cellpadding="0" id="image_view" >
<tr id="tr_id0" ';
if(!(isset($pattern) && $pattern!='')){
	$html .= 'style="display:none;"';
}
$html .= '>
<td><strong>Pattern:</strong><br/>
<img id="img_file0" width="101px" height="89px" src="';
if(isset($pattern)){
	$html .= $upload_dir.$pattern;
}
$html .= '" onClick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" ><a style="cursor:hand;cursor:pointer;';
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
	for($i=0; $i<count($imageArr); $i++,$count++)
	{

$html .= '<tr id="tr_id'.$count.'">
        <td><strong>Image:</strong><br/>';
		if($imageArr[$i] != "" )
		{         
$html .= '<img src="';
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
$html .= '\',\'I\',\'upload\'); DeleteSingleRow(this);"><img src="';
$html .= $mydirectory;
$html .= '/images/close.png" alt="delete" />
    </a><input type="hidden" id="file_name'.$count.'" name="upload_file[]" value=""/>
<input type="hidden" id="upload_type'.$count.'" name="upload_type[]" value="I"/>
<input type="hidden" id="upload_id'.$count.'" name="upload_id[]" value="'.$imageArr[$i]['id'].'"/>';
		}
     
$html .= '</td>
      </tr>';
	}
}
if(count($fileArr))
{
	for($i=0; $i<count($fileArr); $i++,$count++)
	{

$html .= '<tr id="tr_id'.$count.'">
        <td>Files:<br/>';
		
		if($fileArr[$i] != "")
		{     
			$html .= '<strong>';
			$html .= (substr($fileArr[$i]['file'], (strpos($fileArr[$i]['file'], "-")+1)));
			$html .= '</strong>
            <a href="download.php?file=';
			$html .= $fileArr[$i]['file'];
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
$html .= '<tr>
<td>
<div id="img_file3"></div>';
$html .= '</td>
</tr>';	  
$html .= '</table>
</td>
</tr>
</table>';

$return_arr['html'] =$html;
echo json_encode($return_arr);
return;
?>