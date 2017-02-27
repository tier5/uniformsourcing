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
$upload_dir = "$mydirectory/uploadFiles/project_mgm/";
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
if($pid > 0)
{
	
	$sql="Select \"prjimageId\",pattern,grading,image,file from tbl_prjimage_file where pid=$pid";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result))
	{
		$data_prjImage=$row;
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
else 
{
	$file_type = $_FILES[$fileElementName]['type'];
	$file_name = $_FILES[$fileElementName]['name'];			
	$file_size = $_FILES[$fileElementName]['size'];			
	$file_tmp = $_FILES[$fileElementName]['tmp_name'];
	$filePath_fileName = $upload_dir.$file_name;			
	$fileName = $file_name;
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
		$return_arr["msg"]= "$fileFor image : Requested image file uploaded sucessfully";
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
		$return_arr["msg"]= "$fileFor image : Requested file uploaded sucessfully";
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
	$sql="Select count(*) as n  from  tbl_newproject where projectname='$projectname'";
	if($pid >0)
	$sql .= " and pid <> $pid";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
	$projectCount = "";
	while($row = pg_fetch_array($result))
	{
		$projectCount=$row;
	}
	if($pid > 0)
	{		
		if((int)$projectCount['n'] >0)
		{
			$return_arr['error'] = "Project Name already exist";
			if(file_exists($filePath_fileName))
			@unlink($filePath_fileName);	
			echo json_encode($return_arr);
			return;
		}
		$query="UPDATE \"tbl_stockImage\" SET \"stockId\" = '$stockId'";
		$deleteFile = "";
		if($fileFor[0] =="F")
		{
			$query.=", \"front\" ='".$stockId.$fileFor[0]."_"."$fileName' ";
			$query.=", \"isFrontImage\" = '$isImage' ";
			if($data_stock['front'] != $fileName)
			{
				$deleteFile = $upload_dir.$data_stock['front'];
			}
		}
		else if($fileFor[0] =="B")
		{
			$query.=", \"back\" = '".$stockId.$fileFor[0]."_"."$fileName' ";
			$query.=", \"isBackImage\" = '$isImage' ";
			if($data_stock['back'] != $fileName)
			{
				$deleteFile = $upload_dir.$data_stock['back'];
			}
		}
		else if($fileFor[0] =="L")
		{
			$query.=", \"label\" = '".$stockId.$fileFor[0]."_"."$fileName' ";
			$query.=", \"isLabelImage\" = '$isImage' ";
			if($data_stock['label'] != $fileName)
			{
				$deleteFile = $upload_dir.$data_stock['label'];
			}
		}
		$query.=", \"partType\" ='$partType'";
		$query.=", \"stockNum1\" = '$stockNum1' ";
		$query.=", \"stockNum2\" ='$stockNum2'";
		$query.=", \"createdDate\" ='".date('U')."'";
		$query.=", \"modifiedDate\" ='".date('U')."'";
		$query.=" where \"stockId\" = '$stockId' ";
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
		if($dataStock['n'] > 0)
		{
			$return_arr['error'] = "Stock Number already exist";
			if(file_exists($filePath_fileName))
			@unlink($filePath_fileName);
			echo json_encode($return_arr);
			return;
		}	
		$query = "Select  (Max(\"stockId\")+1) as \"stockId\" from \"tbl_stockImage\" ";
		if(!($result=pg_query($connection,$query)))
		{
			$return_arr['error'] = pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		}
		$row = pg_fetch_array($result);
		$data=$row;
	  	pg_free_result($result);
			
		if(! $data['stockId']) { $data['stockId']=1; }
		$query="INSERT INTO \"tbl_stockImage\" (\"stockId\", \"partType\"";
		$query.=" ,\"stockNum1\" ";
		$query.=" ,\"stockNum2\" ";
		$query.=" ,companyname ";
		if($fileFor[0] =="F")
		{
			$query.=" ,\"front\" ";
			$query.=" ,\"isFrontImage\" ";
		}
		else if($fileFor[0] =="B")
		{
			$query.=" ,\"back\" ";
			$query.=" ,\"isBackImage\" ";
		}
		else if($fileFor[0] =="L")
		{
			$query.=" ,\"label\" ";
			$query.=" ,\"isLabelImage\" ";
		}
		$query.=" ,\"status\",\"createdDate\",\"modifiedDate\" ) ";
		$query.=" VALUES ('".$data['stockId']."','$partType' ";
		$query.=", '$stockNum1'";
		$query.=", '$stockNum2' ";
		if($companyname == $companyName[0])
		{
			$query.=", 0 ";
		}
		else if($companyname == $companyName[1])
		{
			$query.=", 1 ";
		}
		else if($companyname == $companyName[2])
		{
			$query.=", 2 ";
		}
		$query.=", '".$data['stockId'].$fileFor[0]."_"."$fileName'";
		$query.=", '$isImage' ";
		$query.=", '1' ";
		$query.=", '".date('U')."' ";
		$query.=", '".date('U')."' ";		
		$query.=")";
		if(!($result=pg_query($connection,$query)))
		{
			$return_arr['error'] = pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		 }
	  	pg_free_result($result);
		
	}
	$query = " Select \"stockId\" from  \"tbl_stockImage\" where \"stockNum1\"='$stockNum1' and \"stockNum2\"='$stockNum2' order by \"stockId\" desc  LIMIT 1";
	if(!($result=pg_query($connection,$query))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}

	while($row = pg_fetch_array($result))
	{
		$data_stock=$row;
	}
	pg_free_result($result);
	if($data_stock['stockId'] != "" && $data_stock['stockId'] > 0)
	{
		$stockId = $data_stock['stockId'];		
	}
	rename($filePath_fileName,$upload_dir.$stockId.$fileFor[0].'_'.$fileName);
	$return_arr['id'] = $stockId;
	$return_arr['name'] = $fileName;
}
if($error != "")
	$return_arr["error"] = $fileFor." image : ".$error;
echo json_encode($return_arr);
return;
?>