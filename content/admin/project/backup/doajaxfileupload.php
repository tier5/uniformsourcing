<?php

$upload_dir			= "../../projectimages/";
	

function make_thumbnails($updir, $img){

	$thumbnail_width	= 640;//80;
	$thumbnail_height	= 480;//60;
	$thumb_preword		= "Proj_";		
	$pro_id = $_GET['pro_id'];
	$projImg = $_GET['proj_img'];
	$img_cnt = $_GET['img_cnt'];
	$thumbimgname=$thumb_preword.$pro_id."_".$img_cnt."_".date('U').strtolower(strrchr($img,'.'));
	$arr_image_details	= GetImageSize("$updir"."$img");
	$original_width		= $arr_image_details[0];
	$original_height	= $arr_image_details[1];

	if( $original_width > $original_height ){
		$new_width	= $thumbnail_width;
		$new_height	= intval($original_height*$new_width/$original_width);
	} else {
		$new_height	= $thumbnail_height;
		$new_width	= intval($original_width*$new_height/$original_height);
	}

	$dest_x = intval(($thumbnail_width - $new_width) / 2);
	$dest_y = intval(($thumbnail_height - $new_height) / 2);



	if($arr_image_details[2]==1) { $imgt = "ImageGIF"; $imgcreatefrom = "ImageCreateFromGIF";  }
	if($arr_image_details[2]==2) { $imgt = "ImageJPEG"; $imgcreatefrom = "ImageCreateFromJPEG";  }
	if($arr_image_details[2]==3) { $imgt = "ImagePNG"; $imgcreatefrom = "ImageCreateFromPNG";  }

	if(file_exists("$updir"."$thumbimgname")) {
		@ unlink("$updir"."$thumbimgname");
	}
	if(file_exists("$updir"."$projImg")) {
		@ unlink("$updir"."$projImg");
	}

	if( $imgt ) { 
		$old_image	= $imgcreatefrom("$updir"."$img");
		$new_image	= imagecreatetruecolor($thumbnail_width, $thumbnail_height);
		imageCopyResized($new_image,$old_image,$dest_x, 		
		$dest_y,0,0,$new_width,$new_height,$original_width,$original_height);
		$imgt($new_image,"$updir"."$thumbimgname");//$imgt($new_image,"$updir"."$thumb_preword"."$img");
		@ chmod("$updir"."$thumbimgname",0777);
		return $thumbimgname;
	}	
}

	$error = "";
	$msg = "";
	$fileElementName = $_GET['field'];	
	
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
	}elseif(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES['fileToUpload']['tmp_name'] == 'none')
	{
		$error = 'No file was uploaded..';
	}else 
	{
			$img_cnt = $_GET['img_cnt'];		
			$file_type = $_FILES[$fileElementName]['type'];
			$file_name = $_FILES[$fileElementName]['name'];			
			$file_size = $_FILES[$fileElementName]['size'];			
			$file_tmp = $_FILES[$fileElementName]['tmp_name'];
			$img_path = $upload_dir.$file_name;
			copy( $file_tmp, $img_path ); 
			//$msg .= createThumb($file_name,$file_name,$file_type); 
			if($img_cnt<=15)
			{ 
				$thumbimgname=make_thumbnails($upload_dir, $file_name);	
				@unlink($img_path);	
			}
			else
			{
				$pro_id = $_GET['pro_id'];
				
				$projImg = $_GET['proj_img'];
				$file_name = $_FILES[$fileElementName]['name'];	
				$file_tmp = $_FILES[$fileElementName]['tmp_name'];
				
				$thumbimgname ="Proj_".$pro_id."_".$img_cnt."_".$file_name;
				$img_path = $upload_dir.$thumbimgname;
				if(file_exists("$upload_dir"."$projImg")) {
				@ unlink("$upload_dir"."$projImg");
				}
				if(file_exists("$upload_dir"."$thumbimgname")) {
				@ unlink("$upload_dir"."$thumbimgname");
				}
				if(file_exists("$upload_dir"."$file_name")) {
				@ unlink("$upload_dir"."$file_name");
				}
				copy( $file_tmp, $img_path );
			}		
			//for security reason, we force to remove all uploaded file	
			if(file_exists($fileElementName))		
				@unlink($fileElementName);	
		
	}		
	echo "{";
	echo				"error: '" . $error . "',\n";
	echo				"msg: '" . $thumbimgname . "'\n";
	echo "}";
?>