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
$ret_arr['file_name']=(substr($data_elements['file'], (strpos($data_elements['file'], "-") + 1)));
}
header('Content-type: application/json');
echo json_encode($ret_arr);
?>















