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
if($projectName == "")
{
	$return_arr['error'] = "Please enter Project Name";
}
if($return_arr['error'] != "")
{
	if(file_exists($fileElementName))		
		@unlink($fileElementName);
	@unlink($_FILES[$fileElementName]);
	echo json_encode($return_arr);
	return;
}
$projectName=pg_escape_string($projectName);
$sql="Select count(*) as n  from  tbl_newproject where projectname= '$projectName' and status = 1";
if($pid >0)
$sql .= " and pid <> $pid";
if(!($result=pg_query($connection,$sql)))
{		
	$return_arr['error'] = pg_last_error($connection);
	echo json_encode($return_arr);
	return;
}
$data_project = "";
while($row = pg_fetch_array($result))
{
	$data_project=$row;
}
	
if((int)$data_project['n'] >0)
{
	$return_arr['error'] = "Project Name already exist";
	if(file_exists($filePath_fileName))
	@unlink($filePath_fileName);	
	echo json_encode($return_arr);
	return;
}
/*if(file_exists($fileElementName))		
		@unlink($fileElementName);
	@unlink($_FILES[$fileElementName]);
	exit;*/
if($pid == 0 || $pid =='')
{
	$query = "select nextval(('tbl_newproject_pid_seq'::text)::regclass) as pid";
	if(!($result=pg_query($connection,$query)))
	{
		$return_arr['error'] = pg_last_error($connection);
		echo json_encode($return_arr);
		return;
	}	
	while($row = pg_fetch_array($result))
	{
		$data_prj=$row;
	}
	$pid = $data_prj['pid'];
	pg_free_result($result);
	//echo $pid;
	$sql= "Insert into tbl_newproject (";
	if($pid>0)$sql.="pid";
	if($projectName!="")$sql.=",projectname";
	$sql.=",status";
	$sql.=",\"createddate\")";
	$sql.="Values(";
	if($pid>0)$sql.="'$pid'";
	if($projectName!="")$sql.=",'$projectName'";	
	$sql.=",'1'";
	$sql.=",'".date('U')."')";
	if(!($result=pg_query($connection,$sql)))
	{
		$return_arr['error'] = pg_last_error($connection);
		echo json_encode($return_arr);
		return;
	}	
	pg_free_result($result);	
}
$fileElementName = $_POST['fileId'];
$fileFor=$_POST['fileType'];
$fileElementType = "";
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
else if(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none')
{
	$error = 'No file was uploaded..';
}
else if($pid >0)
{
	$file_type = $_FILES[$fileElementName]['type'];
	$file_name = $_FILES[$fileElementName]['name'];			
	$file_size = $_FILES[$fileElementName]['size'];			
	$file_tmp = $_FILES[$fileElementName]['tmp_name'];
	$fileName = date('U')."-".$file_name;
	$filePath_fileName = $upload_dir.$fileName;			
	
	$isImage = 0;
	if((strripos("I".$file_type,"image/") != FALSE))
	{	
		$filePath_fileName = "$upload_dir$fileName";	
		if(file_exists($filePath_fileName)) {
		@ unlink($filePath_fileName);
		}		
		copy( $file_tmp, $filePath_fileName );
		@ chmod($filePath_fileName,0777);
		$fileElementType="image";
		$return_arr["msg"]= "Requested image file uploaded sucessfully";
		$isImage = 1;
	}
	else if((strripos("I".$file_type,"image/")== FALSE ))
	{		
		if(file_exists("$upload_dir"."$fileName")) {
		@ unlink("$upload_dir"."$fileName");
		}
		copy( $file_tmp, $filePath_fileName );		
		@ chmod($filePath_fileName,0777);
		$fileElementType="file";
		$return_arr["msg"]= "Requested file uploaded sucessfully";
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
	//for security reason, we force to remove all uploaded file	
	if(file_exists($fileElementName))		
		@unlink($fileElementName);	
	$msg .= " File Name: " . $_FILES[$fileElementName]['name'] . ", ";
	$msg .= " File Size: " . @filesize($_FILES[$fileElementName]['tmp_name']);
	//for security reason, we force to remove all uploaded file
	@unlink($_FILES[$fileElementName]);
	
	$fileName = pg_escape_string($fileName);
	if($formtype == 'element')
	{
		if($elementid > 0)
		{
			
			$query_Name="UPDATE tbl_prj_elements SET status=1 ";
			if($type == "I")
			{
				if($fileName!="")$query_Name.=", image = '$fileName' ";
				else $query_Name.=", image = null ";
			}
			else if($type == "F")
			{
				if($fileName!="")$query_Name.=", elementfile = '$fileName' ";
				else $query_Name.=", elementfile = null ";
			}
			$query_Name.=", createddate = ".date('U');
			$query_Name.=", updateddate = ".date('U');	
			$query_Name.="  where pid='$pid' and prj_element_id = '$elementid'";
			if(!($result=pg_query($connection,$query_Name)))
			{
				$return_arr['error'] = pg_last_error($connection);
				echo json_encode($return_arr);
				return;
			 }
			pg_free_result($result);
		}
		else
		{
			$query1="INSERT INTO tbl_prj_elements (";
			$query1.=" pid";
			if($type == "I" && $fileName !="")
			{
				$query1.=", image ";
			}
			else if($type == "F" && $fileName !="")
			{
				$query1.=", elementfile ";
			}
			$query1.=", status ";
			$query1.=", createddate ";
			$query1.=", updateddate ";
			$query1.=")";
			$query1.=" VALUES (";
			$query1.=" '".$pid."' ";
			if($type == "I" && $fileName !="")
			{
				$query1.=", '$fileName' ";
			}
			else if($type == "F" && $fileName !="")
			{
				$query1.=" ,'$fileName' ";
			}
			$query1.=" ,1 ";
			$query1.=" ,'".date('U')."' ";
			$query1.=" ,'".date('U')."'";
			$query1.=" )".";";
			if(!($result=pg_query($connection,$query1)))
			{
				$return_arr['error'] = pg_last_error($connection);
				echo json_encode($return_arr);
				return;
			 }
			pg_free_result($result);
		}
		
	}
	else
	{
		if($uploadsId > 0)
		{	
			$deleteFile = "";
			if($type == 'G' || $type == 'P')
			{
				if($data_prj['file_name'] != $fileName)
				{
					$deleteFile = $upload_dir.$data_prj['file_name'];
				}
			}
			$query="UPDATE tbl_prjimage_file SET pid = '".$pid."', status = 1 ";
			$query.=", file_name = '$fileName' ";
			$query.=", \"type\" = '$type' ";
			$query.=", createddate ='".date('U')."'";
			$query.=", updateddate ='".date('U')."'";
			$query.=" where \"prjimageId\" = '$uploadsId' ";
			if(!($result=pg_query($connection,$query)))
			{
				$return_arr['error'] = pg_last_error($connection);
				echo json_encode($return_arr);
				return;
			 }
			pg_free_result($result);
			if($deleteFile != "")
			{
				if(file_exists($deleteFile))		
					@unlink($deleteFile);
			}
		}
		else
		{		
			$sql = "Insert Into tbl_prjimage_file ( pid ";
			$sql.=", file_name";		
			$sql.=",\"type\"";
			$sql.=", status";
			$sql.=" )";
			$sql.=" Values( ";
			$sql.=" '$pid'";
			$sql.=", '$fileName'";		
			$sql.=", '$type'";
			$sql.=", '1'";
			$sql.=" )";
			if(!($result=pg_query($connection,$sql)))
			{
				$return_arr['error'] = pg_last_error($connection);
				echo json_encode($return_arr);
				return;
			 }
			pg_free_result($result);
		}
	}
	$return_arr['id'] = $pid;
	$return_arr['name'] = $fileName;
}
if($error != "")
	$return_arr["error"] = $fileFor." image : ".$error;
echo json_encode($return_arr);
return;
?>