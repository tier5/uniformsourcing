<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
$error = "";
$msg = "";
$return_arr = array();
$return_arr['msg'] = "";
$return_arr['error'] = "";
$return_arr['name'] = "";
$return_arr['id'] = 0;
$fileElementType = "";
extract($_POST);
function make_thumbnails($updir, $img,$fieldname=''){

	$thumbnail_width	= 130;//80;
	$thumbnail_height	= 100;//60;
	//$fieldname=$_GET['field'];
	$thumb_preword	= "Sam_";
	$pro_id = $_GET['id'];
	$img_cnt = $_GET['img_cnt'];
	if($img_cnt)
		$thumbimgname=$thumb_preword.$pro_id."_".$img_cnt."_".date('U').strtolower(strrchr($img,'.'));
	else 
		$thumbimgname=$thumb_preword.$pro_id."_".date('U').strtolower(strrchr($img,'.'));
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
if($srID == "")
{
	$return_arr['error'] = "Please enter Sample Id";
}
if($return_arr['error'] != "")
{
	if(file_exists($fileElementName))		
		@unlink($fileElementName);
	@unlink($_FILES[$fileElementName]);
	echo json_encode($return_arr);
	return;
}
$srID=pg_escape_string($srID);
$sql="Select count(*) as n  from  tbl_sample_database where sample_id_val= '$srID' and status = 1";
if($id >0)
$sql .= " and sample_id <> $id";
if(!($result=pg_query($connection,$sql))){
	print("Failed query: " . pg_last_error($connection));
	if(file_exists($fileElementName))		
		@unlink($fileElementName);
	@unlink($_FILES[$fileElementName]);
	exit;
}
$data_sample = "";
while($row = pg_fetch_array($result))
{
	$data_sample=$row;
}
	
if((int)$data_sample['n'] >0)
{
	$return_arr['error'] = "Sample ID already exist";
	if(file_exists($filePath_fileName))
	@unlink($filePath_fileName);	
	echo json_encode($return_arr);
	return;
}
if($id == 0 || $id =='')
{
	$sql= "Insert into tbl_sample_database (";
	if($srID!="")$sql.=" sample_id_val ";
	$sql.=",status";
	$sql.=",createddate)";
	$sql.="Values(";
	if($srID!="")$sql.="'$srID'";	
	$sql.=",'1'";
	$sql.=",'".date('U')."')";
	if(!($result=pg_query($connection,$sql))){
		$return_arr['error'] ="Error while inserting sample database information to database!";	
		echo json_encode($return_arr);
		return;
	}
		pg_free_result($result);
	
	$sql= "Select sample_id from  tbl_sample_database where sample_id_val='$srID' and status = 1";
	if(!($result=pg_query($connection,$sql))){
		$return_arr['error'] ="Error while getting database sample information from database!";	
		echo json_encode($return_arr);
		return;
	}
	while($row = pg_fetch_array($result))
	{
		$data_uploads=$row;
	}
	$id = $data_uploads['sample_id'];
	pg_free_result($result);
}

	$error = "";
	$msg = "";
	$fileElementName = $_POST['fileId'];
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
	elseif(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES['fileToUpload']['tmp_name'] == 'none')
	{
		$error = 'No file was uploaded..';
	}
	else 
	{
		$file_type = $_FILES[$fileElementName]['type'];
		$file_name = $_FILES[$fileElementName]['name'];			
		$file_size = $_FILES[$fileElementName]['size'];			
		$file_tmp = $_FILES[$fileElementName]['tmp_name'];
		$fileName = date('U')."-".$file_name;
		$filePath_fileName = $upload_dir.$fileName;	
		$isFileUpload = 0;
		$isImg  = 0;
		if((strripos("I".$file_type,"image/") == FALSE))
		{	
			$filePath_fileName = "$upload_dir$fileName";	
			if(file_exists($filePath_fileName)) {
			@ unlink($filePath_fileName);
			}		
			copy( $file_tmp, $filePath_fileName );
			@ chmod($filePath_fileName,0777);
			$fileElementType="image";
			$return_arr["msg"]= " File : Requested file file uploaded sucessfully";
			$isFileUpload = 1;
			$isImg  = 0;
		}
		else if((strripos("I".$file_type,"image/")!= FALSE ))
		{
			if(file_exists("$upload_dir"."$fileName")) {
			@ unlink("$upload_dir"."$fileName");
			}
			copy( $file_tmp, $filePath_fileName );		
			@ chmod($filePath_fileName,0777);
			$fileElementType="file";
			$return_arr["msg"]= " Image : Requested image uploaded sucessfully";
			$isFileUpload = 1;
			$isImg  = 1;
		}
		else
		{
			$fileElementType="";
			if(file_exists($fileElementName))		
			@unlink($fileElementName);
			@unlink($_FILES[$fileElementName]);
			$error="File Upload Error : Not a valid file type !";
			$return_arr["error"] = $error;
			echo json_encode($return_arr);
			return;
		}
		if(file_exists($fileElementName))		
		@unlink($fileElementName);	
		$msg .= " File Name: " . $_FILES[$fileElementName]['name'] . ", ";
		$msg .= " File Size: " . @filesize($_FILES[$fileElementName]['tmp_name']);
		//for security reason, we force to remove all uploaded file
		@unlink($_FILES[$fileElementName]);
		$fileName = pg_escape_string($fileName);
		$sql = "Insert Into tbl_sample_database_uploads ( sample_id ";
			$sql.=", filename";		
			$sql.=",uploadtype ";
			$sql.=", status";
			$sql.=", createddate";
			$sql.=" )";
			$sql.=" Values( ";
			$sql.=" '$id'";
			$sql.=", '$fileName'";		
			$sql.=", '".trim($type)."'";
			$sql.=", '1'";
			$sql.=", ".date('U');
			$sql.=" )";
			if(!($result=pg_query($connection,$sql)))
			{
				$return_arr['error'] = pg_last_error($connection);
				echo json_encode($return_arr);
				return;
			 }
			pg_free_result($result);			
			$return_arr['id'] = $id;
			$return_arr['name'] = $fileName;
	}
	
if($error != "")
	$return_arr["error"] = " Upload File : ".$error;
echo json_encode($return_arr);
return;
?>