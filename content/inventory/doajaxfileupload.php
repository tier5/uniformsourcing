<?php
require('Application.php');

$return_arr = array();
$return_arr['msg'] = "";
$return_arr['error'] = "";
$return_arr['imageSrc'] = "";
$return_arr['imageName'] = "";
$error = "";
$msg = "";

$fileElementName = $_GET['field'];	
$fileElementType = $_GET['fileType'];
$fileStyleId = $_GET['projectId'];	
$fileColorId	= $_GET['colorId'];
$opt	= $_GET['opt'];


/*if($opt==1)
{
    
    $file_name = $_FILES[$fileElementName]['name'];
    $thumbimgname=$file_name;
    
  $img_path = $upload_dir.$thumbimgname;
  $thumbimgname = make_thumbnails($upload_dir, $file_name, $upload_dir_image);
		if(file_exists("$upload_dir"."$file_name")) {
		@ unlink("$upload_dir"."$thumbimgname");
		}		
		copy( $file_tmp, $img_path );
		//$thumbimgname = make_thumbnails($upload_dir, $file_name, $upload_dir_image);  
}*/

function make_thumbnails($updir, $img, $destDir){
  
    require '../imageResize.php';
    $thumbimgname = $img;
    $thumb_w = 200;
    $thumb_h = 200;
    $w = 800;
    $h = 600;
    $image = new Resize_Image;
    list($width, $height) = getimagesize("$updir"."$img");
    if ($width > $height) {
                $thumb_h = 0;
                $h = 0;
	} else {
                $thumb_w = 0;
                $w = 0;
	}
        if($thumb_h == 0)
        {
            unset($image->new_height);
            $image->new_width = $thumb_w;
        }
        else if($thumb_w == 0)
        {
            unset($image->new_width);
            $image->new_height = $thumb_h;
        }

    $image->image_to_resize = "$updir"."$img"; // Full Path to the file

    $image->ratio = true; // Keep Aspect Ratio?

    // Name of the new image (optional) - If it's not set a new will be added automatically
    $image->new_image_name = pathinfo($thumbimgname, PATHINFO_FILENAME);
    $image->new_image_ext = pathinfo($thumbimgname, PATHINFO_EXTENSION);
    /* Path where the new image should be saved. If it's not set the script will output the image without saving it */

    $image->save_folder = $destDir.'thumbs/';

    $process = $image->resize();    

    if($process['result'] && $image->save_folder)
    {
        if($h == 0)
        {
            unset($image->new_height);
            $image->new_width = $w;
        }
        else if($w == 0)
        {
            unset($image->new_width);
            $image->new_height = $h;
        }
        $image->save_folder = $destDir;
        $process = $image->resize();
        if($process['result'] && $image->save_folder)
             return $image->new_image_name.'.'.$image->new_image_ext;
        return FALSE;
    }
    return FALSE;
//
//   	$thumbnail_width	= 320;//80;
//	$thumbnail_height	= 240;//60;
//	$arr_image_details	= GetImageSize("$updir"."$img");
//	$original_width		= $arr_image_details[0];
//	$original_height	= $arr_image_details[1];
//	$thumbimgname = $img;
//        
//       
//        
//	if( $original_width > $original_height ){
//		$new_width	= $thumbnail_width;
//		$new_height	= intval($original_height*$new_width/$original_width);
//	} else {
//		$new_height	= $thumbnail_height;
//		$new_width	= intval($original_width*$new_height/$original_height);
//	}
//	
//	$dest_x = intval(($thumbnail_width - $new_width) / 2);
//	$dest_y = intval(($thumbnail_height - $new_height) / 2);
//
//
//
//	if($arr_image_details[2]==1) { $imgt = "ImageGIF"; $imgcreatefrom = "ImageCreateFromGIF";  }
//	if($arr_image_details[2]==2) { $imgt = "ImageJPEG"; $imgcreatefrom = "ImageCreateFromJPEG";  }
//	if($arr_image_details[2]==3) { $imgt = "ImagePNG"; $imgcreatefrom = "ImageCreateFromPNG";  }
//
//	if(file_exists("$destDir"."$thumbimgname")) {
//		@ unlink("$destDir"."$thumbimgname");
//	}	
//
//	if( $imgt ) { 
//		$old_image	= $imgcreatefrom("$updir"."$img");
//		$new_image	= imagecreatetruecolor($thumbnail_width, $thumbnail_height);
//		imageCopyResized($new_image,$old_image,$dest_x, 		
//		$dest_y,0,0,$new_width,$new_height,$original_width,$original_height);
//		$imgt($new_image,"$destDir"."$thumbimgname");//$imgt($new_image,"$updir"."$thumb_preword"."$img");
//		@ chmod("$destDir"."$thumbimgname",0777);
//		
//		if(file_exists("$updir"."$img")) {
//		@ unlink("$updir"."$img");
//		}
//		
//		return $thumbimgname;
//	}	
}
if(!empty($_FILES[$fileElementName]['error']))
{
	switch($_FILES[$fileElementName]['error'])
	{

		case '1':
			$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
			break;
		case '2':
			$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
			break;
		case '3':
			$error = 'The uploaded file was only partially uploaded';
			break;
		case '4':
			$error = 'No file was uploaded.';
			break;

		case '6':
			$error = 'Missing a temporary folder';
			break;
		case '7':
			$error = 'Failed to write file to disk';
			break;
		case '8':
			$error = 'File upload stopped by extension';
			break;
		case '999':
		default:
			$error = 'No error code avaiable';
	}
}
else if(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES['fileToUpload']['tmp_name'] == 'none')
{
	$error = 'No file was uploaded..';
}
else 
{		
	$file_type = $_FILES[$fileElementName]['type'];
	$file_name = $_FILES[$fileElementName]['name'];			
	$file_size = $_FILES[$fileElementName]['size'];			
	$file_tmp = $_FILES[$fileElementName]['tmp_name'];
	$img_path = $upload_dir.$file_name;			
	$thumbimgname = $file_name;
	if((strripos("I".$file_type,"image/") != FALSE) && ($fileElementType=='image'))
	{ 
		$img_path = $upload_dir.$thumbimgname;
		if(file_exists("$upload_dir"."$thumbimgname")) {
		@ unlink("$upload_dir"."$thumbimgname");
		}		
		copy( $file_tmp, $img_path );
		$thumbimgname = make_thumbnails($upload_dir, $file_name, $upload_dir_image);
                if($thumbimgname === FALSE)
                {
                    $error="File Upload Error : Unable to create thumbnail version of image !";
                    $return_arr["error"] = $error;
                    echo json_encode($return_arr);
                    return;
                }  
		$fileElementType="image";
		$return_arr["msg"]=$thumbimgname;
                //echo $thumbimgname;
	}
	else if((strripos("I".$file_type,"image/")== FALSE )&& ($fileElementType=='file'))
	{		
		$img_path = $upload_dir_general.$thumbimgname;
		if(file_exists("$upload_dir_general"."$thumbimgname")) {
		@ unlink("$upload_dir_general"."$thumbimgname");
		}
		if(file_exists("$upload_dir_general"."$file_name")) {
		@ unlink("$upload_dir_general"."$file_name");
		}
		copy( $file_tmp, $img_path );			
		$fileElementType="file";
	}
	else
	{
		$fileElementType="";
		$error="File Upload Error : Not a valid file type !";
		$return_arr["error"] = $error;
		echo json_encode($return_arr);
		return;
	}
	if(isset($fileColorId) && $fileColorId >0)
	{	
		$sql = 'Select "image","styleId" from "tbl_invColor" where "colorId" = '.$fileColorId;
		if(!($result=pg_query($connection,$sql)))
		{
				  $error = ("Failed query: " . pg_last_error($connection));
				  $return_arr["error"] = $error;
				  echo json_encode($return_arr);
				  return;
		}
			$row = pg_fetch_array($result);
			$dataImage = $row['image'];
			$styleId = $row['styleId'];
			if(file_exists("$upload_dir_image"."$dataImage")){
			@unlink("$upload_dir_image"."$dataImage");
                        @unlink("$upload_dir_image"."thumbs/$dataImage");
                        }
			$imageName = substr($thumbimgname,0,(strlen($thumbimgname)-4))."_".$styleId.substr($thumbimgname,(strlen($thumbimgname)-4));
			if(file_exists("$upload_dir_image"."$thumbimgname")) {
								@ rename("$upload_dir_image"."$thumbimgname", "$upload_dir_image"."$imageName");
							}
                        if(file_exists("$upload_dir_image"."thumbs/$thumbimgname")) {
                                @ rename($upload_dir_image."thumbs/"."$thumbimgname", $upload_dir_image."thumbs/"."$imageName");
                        }	
                        //echo $upload_dir_image."thumbs/"."$imageName";
		$sql = "Update \"tbl_invColor\" set \"image\" = '".$imageName."' where \"colorId\" = ".$fileColorId;
		if(!($result=pg_query($connection,$sql)))
		{
				  $error = ("Failed query: " . pg_last_error($connection));
				  $return_arr["error"] = $error;
				  echo json_encode($return_arr);
				  return;
		}
		 $return_arr['imageSrc'] = $upload_dir_image.$imageName;
		 $return_arr['imageName'] = $imageName;
		 echo json_encode($return_arr);
		 return;
	}
	//for security reason, we force to remove all uploaded file	
	if(file_exists($fileElementName))		
		@unlink($fileElementName);	
	/*if($thumbimgname!="" && ($fileElementType!=""))
	{	
		$sql = "";
		switch($fileElementType)
		{
		  case "image":
		  {
			  $sql="INSERT INTO tbl_projImageFile ( pid";
			  $sql.=", \"fileName\" ";			  
			  $sql.=", status )";
			  $sql.=" VALUES ('".$fileProjectId."' ";
			  $sql.=", '".$thumbimgname."' ";			  
			  $sql.=",'1' )";			  
			  break;
		  }
		  case "file":
		  {
			  $sql="INSERT INTO tbl_projGeneralFile ( pid";
			  $sql.=", \"fileName\" ";	  
			  $sql.=", status )";
			  $sql.=" VALUES ('".$fileProjectId."' ";
			  $sql.=", '".$thumbimgname."' ";
			  $sql.=" ,'1' )";			  
			  break;
		  }
		}
		if($sql != "")
		{
			if(!($result=pg_query($connection,$sql))){
				  $error = ("Failed query: " . pg_last_error($connection));
				  $return_arr["error"] = $error;
				  echo json_encode($return_arr);
				  return;
			}
		}						
	}*/
}
$return_arr["error"] = $error;
echo json_encode($return_arr);
return;
?>