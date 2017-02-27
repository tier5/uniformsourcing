<?php
require('Application.php');
extract($_POST);
$upload_dir = "$mydirectory/uploadFiles/image_file/";
        
     

$sql = 'Select item.*,pack."pack_name" from "img_file_items" as item left join "img_file_pack" as pack on pack."pack_id"=item."pack_id" '.
          ' where item."pack_id"='.$pack_id;
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
        
        
        
        
           $sql='select client.cid,cldb.client from img_file_clients as client left join "clientDB" as cldb on cldb."ID"=client.cid where pack_id='.$pack_id;
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
         $sql='select style.style_id,invstyle."styleNumber" from img_file_styles as style left join "tbl_invStyle" as invstyle on invstyle."styleId"=style.style_id where pack_id='.$pack_id;
       //echo $sql;
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	if(isset($data_style)) unset($data_style);
	while($row = pg_fetch_array($result)){
		$data_style[] =$row;
	}     
        pg_free_result($result);

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
 ' <td>Style Number: <strong>'.$data_prjUploads_pack['pack_name'].'</td> '.'<td valign="top">'. 
 '<img  onclick="$(\'#upld_pack_'.$data_package_list[$j]['pack_id'].'\').remove()" src="../../images/delete.png" /></td></tr>';
  
  
   $html .='<tr><td width="100%">';
    
   $html .='<input type="hidden" name="upload_packages[]" value="'.$pack_id.'"/>';
    $html .='<table width="100%"><tr><td width="30%"><strong>Clients</strong></td><td width="30%"><strong>Styles</strong></td><td width="30%"><strong>Files</strong></td></tr>';
    
    $html .='<tr><td valign="top"><table>';
    
    
     for($i=0;$i<count($data_client);$i++)
    {
$html .='<tr height="30"><td>'.$data_client[$i]['client'].
'</td></tr>';      
    }

  $html .='</table></td>';  
    
  
  $html .='<td valign="top"><table>';
   for($i=0;$i<count($data_style);$i++)
    {
$html .='<tr height="30" ><td>'.$data_style[$i]['styleNumber'].
'</td></tr>';
    }
  $html .='</table></td>'; 
    
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
$html .= '<img  style="float:left;" src="';
$html .= $upload_dir.$imageArr_pack[$i]['file'];
$html .= '" width="101" height="89" onClick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');">';
        
		}
     

	}
}



$html .='</td><tr></table>';

echo $html;
?>