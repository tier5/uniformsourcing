<?php
require('Application.php');
extract($_POST);
$ret_arr = array();
$ret_arr['pack_id'] = $pack_id;
$ret_arr['index'] = $index;
$ret_arr['img_count'] = $img_count;
$ret_arr['ele_type'] = 0;
$ret_arr['vendor'] = 0;
$ret_arr['style'] = '';
$ret_arr['color'] = '';
$ret_arr['cost'] = '';
$ret_arr['image'] = '';
$ret_arr['file'] = '';

if($pack_id > 0){

$upload_dir = "$mydirectory/uploadFiles/image_file/";
$sql = 'Select "vendorID","vendorName" from "vendor" ';
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($rowv = pg_fetch_array($result)){
		$data_vendor[]  =$rowv;
	}
	pg_free_result($result);

$sql = 'Select * from "tbl_element_package" '.
          ' where "element_id"='.$pack_id;
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	//echo $sql;
	while($row = pg_fetch_array($result)){
		$data_elements =$row;
	}
//print_r($data_elements);
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

$ret_arr['ele_type'] = $data_elements['element_type'];
$ret_arr['vendor'] = $data_elements['vendor_id'];
$ret_arr['style'] = $data_elements['style'];
$ret_arr['color'] = $data_elements['color'];
$ret_arr['cost'] = $data_elements['cost'];
$ret_arr['image'] = $data_elements['image'];
$ret_arr['file'] = $data_elements['file'];
}
header('Content-type: application/json');
echo json_encode($ret_arr);
?>
















<?php if (0){ ?>
Select a default Package: <select onchange="AddElementPackage($(this))" name="upload_pack" id="upload_pack">
<option value="0">--Select--</option>
<?php for($i=0;$i<count($data_package);$i++)
{?>
<option value="<?php echo $data_package[$i]['element_id'];?>" ><?php echo $data_package[$i]['package'];?></option>   
<?php } ?>
</select>

       <input type="image" class="deleteTd" src="../../images/delete.png" 
 onclick=" DeleteUploads('','<?php echo ++$count;?>','','I','editTime'); DeleteUploads('','<?php
 echo ++$count;?>','','F','editTime');"> 
       <input type="hidden" name="selected_pack[]" value="<?php echo $pack_id;?>"/>
<table width="80%" ><tr><td valign="top"><table cellpadding="1" cellspacing="1" border="0"><tr><td align="right">
Element Type:</td>
         <td align="left"><select name="elementtype[]" >
  <option value="">--- Select ------</option>
            
  <option value="Artwork" <?php if (isset($data_elements['element_type']) && $data_elements['element_type'] != "" && $data_elements['element_type'] == "Artwork") {
    echo ' selected="selected" ';
}
?>>Artwork</option>
  <option value="Beads/Crystals"<?php if (isset($data_elements['element_type']) && $data_elements['element_type'] != "" && $data_elements['element_type'] == "Beads/Crystals")
    echo ' selected="selected" ';
?>>Beads/Crystals</option>
  <option value="Boning"<?php if (isset($data_elements['element_type']) && $data_elements['element_type'] != "" && $data_elements['element_type'] == "Boning")
              echo ' selected="selected" ';
?>>Boning</option>
  <option value="Buttons"<?php if (isset($data_elements['element_type']) && $data_elements['element_type'] != "" && $data_elements['element_type'] == "Buttons")
              echo ' selected="selected" ';
?>>Buttons</option>
  <option value="Cups/Pads"<?php if (isset($data_elements['element_type']) && $data_elements['element_type'] != "" && $data_elements['element_type'] == "Cups/Pads")
              echo ' selected="selected" ';
?>>Cups/Pads</option>
  <option value="Fabric"<?php if (isset($data_elements['element_type']) && $data_elements['element_type'] != "" && $data_elements['element_type'] == "Fabric")
              echo ' selected="selected" ';
?>>Fabric</option>
  <option value="Hardware"<?php if (isset($data_elements['element_type']) && $data_elements['element_type'] != "" && $data_elements['element_type'] == "Hardware")
              echo ' selected="selected" ';
?>>Hardware</option>
  <option value="Liner"<?php if (isset($data_elements['element_type']) && $data_elements['element_type'] != "" && $data_elements['element_type'] == "Liner")
              echo ' selected="selected" ';
?>>Liner</option>
  <option value="Labels"<?php if (isset($data_elements['element_type']) && $data_elements['element_type'] != "" && $data_elements['element_type'] == "Labels")
              echo ' selected="selected" ';
?>>Labels</option>
  <option value="Other"<?php if (isset($data_elements['element_type']) && $data_elements['element_type'] != "" && $data_elements['element_type'] == "Other")
              echo ' selected="selected" ';
?>>Other</option>
  <option value="Thread"<?php if (isset($data_elements['element_type']) && $data_elements['element_type'] != "" && $data_elements['element_type'] == "Thread")
              echo ' selected="selected" ';
?>>Thread</option>
  <option value="Trim/Piping"<?php if (isset($data_elements['element_type']) && $data_elements['element_type'] != "" && $data_elements['element_type'] == "Trim/Piping")
              echo ' selected="selected" ';
?>>Trim/Piping</option>
  <option value="Zippers"<?php if (isset($data_elements['element_type']) && $data_elements['element_type'] != "" && $data_elements['element_type'] == "Zippers")
					echo ' selected="selected" ';?>>Zippers</option>
</select>
         
         
         </td></tr>
           <tr><td align="right">Vendor:</td><td align="left"><select name="vendor_ID[]" >
                       
  <?php for($i=0;$i<count($data_vendor);$i++)
            {
            if(isset($data_vendor[$i]['vendorID']) && $data_vendor[$i]['vendorID']!="")
            echo '<option value="'.$data_vendor[$i]['vendorID'].'"';
			   if(isset($datalist['vendorID'])&&$datalist['vendor_id']!="" && $datalist['vendorID']==$data_vendor[$i]['vendorID'])
			   echo '"selected"=selected';
			echo '>'.$data_vendor[$i]['vendorName'].'</option>';
            }
			?>
                   
                   </select></td></tr><tr><td align="right">Style:</td>
              <?php // print_r($data_elements); ?>
<td align="left"><input type="text"  name="elementstyle[]" id="elementstyle" value="<?php
if(isset($data_elements['style'])&&$data_elements['style']!="")
echo $data_elements['style'];?>" /></td></tr>
<tr><td align="right">Color:</td><td align="left"><input type="text"  name="elementcolor[]" id="elementcolor" value="<?php
if(isset($data_elements['color'])&&$data_elements['color']!="")
echo $data_elements['color'];?>" />
</td></tr><tr><td align="right">Cost:</td><td align="left"><input type="text"   name="elementcost[]" id="elementcost" value="<?php
if(isset($data_elements['cost'])&&$data_elements['cost']!="")
echo $data_elements['cost'];?>" /></td></tr>
<tr><td align="right">Image:</td><td align="left"><input type="file" name="file<?php echo --$count;?>" 
   id="file<?php echo $count;?>"  onchange="javascript:ajaxFileUpload('<?php echo $count;?>', 'I', 960,720);" />
        <input type="hidden" id="file_name<?php echo $count?> " name="element_file0[]" value=""/>
        <input type="hidden" id="upload_type<?php echo $count;?>"  
      name="element_type0[]" value="I"/>
        <input type="hidden" id="upload_id<?php echo ++$count;?> " name="element_id0[]" value=""/></td></tr>
<tr><td align="right">File:</td><td align="left">
        <input type="file" name="file<?php echo  $count;?>" id="file<?php echo $count;?>"
       onchange="javascript:ajaxFileUpload('<?php echo $count;?>', 'F', 960,720);" />
        <input type="hidden" id="file_name<?php echo $count;?> " name="element_file1[]" value=""/>
        <input type="hidden" id="upload_type<?php echo --$count;?> " name="element_type1[]" value="F"/></td></tr>
    </tr></table>
            <input type="hidden" id="element_id" name="element_id[]" value="0"/>
</td><td valign="top" align="right"><table  border="0" cellspacing="0" cellpadding="0"><tr id="tr_id<?php echo $count;?>" 
    style="display:none;"><td><strong>Image:</strong><br/><img id="img_file'+ count 
" width="101px" height="89px" src="<?php echo $upload_dir.$data_elements['image'];?>" onClick="PopEx(this, null,  null, 0, 0, 50, 'PopBoxImageLarge');" >
<a style="cursor:hand;cursor:pointer;" 
  onClick=" javascript: DeleteUploads('','<?php echo $count;?>','','I','editTime'); document.getElementById('tr_id<?php 
  echo $count;?>').style.display='none'; document.getElementById('file_name<?php echo ++$count;?>').value='' " >
 <img src="<?php echo $mydirectory; ?>/images/close.png" alt="delete" /></a></td>	</tr>
        <tr id="tr_id
<?php echo $count;?>" style="display:none;" >
            
        </tr></table></td></tr></table>
	
    
  
<?php } ?>