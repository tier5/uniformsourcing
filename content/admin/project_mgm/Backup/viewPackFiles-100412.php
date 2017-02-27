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
	//echo $sql;
	while($row = pg_fetch_array($result)){
		$data_prjUploads[] =$row;
	}
//print_r($data_prjUploads);
	$imageArr = array();
	$fileArr = array();
	$pattern = "";
	$gradient = "";
	for($i = 0, $img= 0, $file = 0; $i < count($data_prjUploads); $i++)
	{
		 if($data_prjUploads[$i]['type'] == 'I')
		{
			$imageArr[$img]['id'] = $data_prjUploads[$i]['item_id'];
			$imageArr[$img++]['file'] = stripslashes($data_prjUploads[$i]['filename']);
		}
		else if($data_prjUploads[$i]['type'] == 'F')
		{
			$fileArr[$file]['id'] = $data_prjUploads[$i]['item_id'];
			$fileArr[$file++]['file'] = stripslashes($data_prjUploads[$i]['filename']);
		}
	}
	pg_free_result($result);
        
           if (count($imageArr)) {
            for ($i = 0; $i < count($imageArr); $i++, $count++) {?>
        <tr><td>
     <img src="<?php echo $upload_dir.$imageArr[$i]['file']; ?>"
 width="101" height="89" onClick="PopEx(this, null,  null, 0, 0, 50, 'PopBoxImageLarge');"/>
    
  <!--   <a style="cursor:hand;cursor:pointer"
       onClick="javascript:  DeleteSingleRow(this);">
                                
                            <img src="<?php //echo $mydirectory; ?>/images/close.png" alt="delete" />
                            </a>-->
                              
                            
                                
                        </td>
                    </tr>
        <?php
        }
            
    }
    
     if (count($fileArr)) {
        for ($i = 0; $i < count($fileArr); $i++, $count++) {
            ?>
                        
                    <tr id="tr_id<?php echo $count; ?>">
                        <td>Files:<br/>
                            
            <?php if ($fileArr[$i] != "") {?>
                                    
        <strong><?php (substr($fileArr[$i]['file'], (strpos($fileArr[$i]['file'], "-") + 1))); ?> </strong>
       <a href="download.php?file=<?php echo $fileArr[$i]['file']; ?>">
       <img src="<?php echo $mydirectory; ?>/images/Download.png" alt="download"/></a>
                              
      
  <!--  <a    href="javascript:void(0);" 
   onClick="javascript: DeleteSingleRow(this);">
     <img src="<?php //echo $mydirectory?>/images/close.png" alt="delete"/></a>-->
      
                                        
                <?php } ?>
                         
                            </td>
                        </tr>
                            <?php
                            
               } 
         } 
?>