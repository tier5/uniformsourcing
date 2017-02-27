<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
extract($_POST);
			if(isset($element_id)&&$element_id!="")
		{
		$sql = 'UPDATE "tbl_element_package" SET "package"=\''.$packagename.'\',"element_type"=\''.$element.'\',"vendor_id"=\''.$vendor.'\',"style"=\''.$elementstyle.'\',"color"=\''.$elementcolor.'\',"cost"=\''.$elementcost.'\',"image"=\''.$elm_upload_img.'\',"file"=\''.$elm_upload_file.'\' WHERE "element_id"=\''.$element_id.'\'';
		}
		
		else
		{
$sql="INSERT INTO tbl_element_package (";
		
		if(isset($packagename) && $packagename!="")
		$sql.='"package"';
		if(isset($element) && $element!="")
		$sql.=', "element_type"';
		if(isset($vendor) && $vendor!="")
		$sql.=', "vendor_id"';
		if(isset($elementstyle) && $elementstyle!="")
		$sql.=',  "style"';	
		if(isset($elementcolor) && $elementcolor!="")
		$sql.=',  "color"';	
		if(isset($elementcost) && $elementcost!="")
		$sql.=',  "cost"';	
		if(isset($elm_upload_img) && $elm_upload_img!="")
		$sql.=',  "image"';	
		if(isset($elm_upload_file) && $elm_upload_file!="")
		$sql.=',  "file"';	
		$sql.=")";
		$sql.=" VALUES (";
		if(isset($packagename) && $packagename!="")
		$sql.="'".$packagename."'";
		if(isset($element) && $element!="")
		$sql.=" ,'".$element."'";
		if(isset($vendor) && $vendor!="")
		$sql.=" ,'".$vendor."'";
		if(isset($elementstyle) && $elementstyle!="")
		$sql.=" ,'".$elementstyle."'";
		if(isset($elementcolor) && $elementcolor!="")
		$sql.=" ,'".$elementcolor."'";
		if(isset($elementcost) && $elementcost!="")
		$sql.=" ,'".$elementcost."'";
		if(isset($elm_upload_img) && $elm_upload_img!="")
		$sql.=" ,'".$elm_upload_img."'";
		if(isset($elm_upload_file) && $elm_upload_file!="")
		$sql.=" ,'".$elm_upload_file."'";
		
		$sql.=' )';
		}
		//echo $sql;
		if(!($result=pg_query($connection,$sql)))
		{
			$return_arr['error'] = "Basic tab :".pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		}
		pg_free_result($result);
	

$ret_arr = array();
$ret_arr['pack_id'] = $pack_id;

header('Content-type: application/json');
echo json_encode($ret_arr);
		?>