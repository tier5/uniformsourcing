<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
extract($_POST);
$ret_arr = array();
$ret_arr['oid'] = $oid;
$ret_arr['msg']='';
$ret_arr['error']='';

if(isset($oid) && $oid !="")
{
$sql = 'SELECT count(*) as count from tbl_fill_orders where oid=' .$oid;
	if(!($result=pg_query($connection,$sql)))
		{
			$return_arr['error'] = "Basic tab :".pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		}
		$row = pg_fetch_array($result);
		pg_free_result($result);
		
}
			if(isset($row['count']) && $row['count']!="")
		{
		$sql = 'UPDATE "tbl_fill_orders" SET "vendor"=\''.trim($vendor).'\',"client"=\''.trim($clientname).'\',"prj_name"=\''.$prj_name.'\',"pu_order"=\''.trim($pu_order).'\',"placed"=\''.trim($placed).'\',"expected"=\''.trim($expected).'\',"trk_num"=\''.trim($trk_num).'\',"upload"=\''.$elm_upload_img.'\' WHERE "oid"=\''.$oid.'\'';
		//echo $sql;
		}
		
		else
		{
			if (!isset($prj_name) || $prj_name == '')
    $message .= 'Project Name is Required! ';

if ($message != '')
{
    $ret['error'] = $message;
    echo json_encode($ret);
    return;
}
			if ($oid == 0){
			 $sql2    = "select count(prj_name) as count from tbl_fill_orders where prj_name='$prj_name' ";
    if (!($result = pg_query($connection, $sql2)))
    {
        $ret['error'] = "Failed check project name: " . pg_last_error($connection);
        echo json_encode($ret);
        return;
    }
    $row = pg_fetch_array($result);
    pg_free_result($result);
    if (isset($row['count']) && $row['count'] > 0)
    {
        $ret['error'] = 'Project already exist.!';
        echo json_encode($ret);
        return;
    }
			}
$sql="INSERT INTO tbl_fill_orders (";
		
		if(isset($vendor) && $vendor!="")
		$sql.='"vendor"';
		if(isset($clientname) && $clientname!="")
		$sql.=', "client"';
		if(isset($prj_name) && $prj_name!="")
		$sql.=', "prj_name"';
		if(isset($pu_order) && $pu_order!="")
		$sql.=', "pu_order"';
		if(isset($placed) && $placed!="")
		$sql.=', "placed"';
		if(isset($expected) && $expected!="")
		$sql.=', "expected"';
		if(isset($trk_num) && $trk_num!="")
		$sql.=', "trk_num"';		
		if(isset($elm_upload_img) && $elm_upload_img!="")
		$sql.=', "upload"';
		$sql.=")";

		$sql.=" VALUES (";
		if(isset($vendor) && $vendor!="")
		$sql.="'".trim($vendor)."'";
		
		if(isset($clientname) && $clientname!="")
		$sql.=" ,'".trim($clientname)."'";
		
		if(isset($prj_name) && $prj_name!="")
		$sql.=" ,'".trim($prj_name)."'";
		
		if(isset($pu_order) && $pu_order!="")
		$sql.=" ,'".trim($pu_order)."'";
		
		if(isset($placed) && $placed!="")
		$sql.=" ,'".$placed."'";
		
		if(isset($expected) && $expected!="")
		$sql.=" ,'".trim($expected)."'";
		
		if(isset($trk_num) && $trk_num!="")
		$sql.=" ,'".trim($trk_num)."'";	
		
		if(isset($elm_upload_img) && $elm_upload_img!="")
		$sql.=" ,'".$elm_upload_img."'";	
		$sql.=" )";
		}
		//echo $sql;
		if ($sql != '')
		{
			if(!($result=pg_query($connection,$sql)))
			{
				$return_arr['error'] = "Basic tab :".pg_last_error($connection);
				echo json_encode($return_arr);
				return;
			}
			pg_free_result($result);
			$ret_arr['msg'] = 'Fill Orders submitted successfully';
		}

header('Content-type: application/json');
echo json_encode($ret_arr);
		?>