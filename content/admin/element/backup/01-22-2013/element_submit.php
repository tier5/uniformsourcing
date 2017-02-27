<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
$package_id=0;
extract($_POST);
 header('Content-type: application/json'); 
 $q_add='';
if(isset($pack_id)&&$pack_id!=""&&$pack_id!='undefined')
    $q_add.=' and pack_id!='.$pack_id;
$q= 'SELECT  count(*) as count  FROM "tbl_element_pack_main" WHERE "pack_name"=\''.$packagename.'\' '.$q_add ;
            if ($q != "") {
      
                if (!($result = pg_query($connection, $q))) {
                    $return_arr['error'] = pg_last_error($connection);
                    echo json_encode($return_arr);
                    return;
                }
                $row2=  pg_fetch_array($result);
                pg_free_result($result);
                $q = "";
            
       if($row2['count']>0)         
       {
     $return_arr['error'] = 'This package name is already exist.Please chose another one and continue...';
     echo json_encode($return_arr);
                
                    return;      
       }   
            }   
 
 
 
 
if(isset($pack_id) && $pack_id!="" && $pack_id!=0)
		{
		$sql = 'UPDATE "tbl_element_pack_main" SET "pack_name"=\''.$packagename.'\',"updated_date"='.date('U');
                        
                         if(isset($client) && $client!="")
                             $sql .=', client='.$client;
                         else
                             $sql .=', client=NULL';
                             
                      $sql .=' WHERE "pack_id"=\''.$pack_id.'\'';
                
                $package_id= $pack_id; 
		}
		
		else
		{

$sql='INSERT INTO "tbl_element_pack_main" ("created_date"';
		
if (isset($packagename) && $packagename != "")
        $sql.=',"pack_name"';

    if (isset($client) && $client != "")
        $sql.=',  "client"';
                
$sql.=') values('.date('U');
if(isset($packagename) && $packagename!="")
		$sql.=",'".$packagename."'";

 if(isset($client) && $client!="")
                  $sql.=" ,'".$client."'";
$sql.=')'; 
                }

//echo $sql;
if(!($result=pg_query($connection,$sql)))
		{
			$return_arr['error'] = pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		}
		pg_free_result($result);
                
              
                
                if($package_id==0)
                {
                $sql='SELECT max(pack_id) as pack_id from "tbl_element_pack_main" ';  
                if(!($result=pg_query($connection,$sql)))
		{
			$return_arr['error'] = pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		}
                $row1=  pg_fetch_array($result);
		pg_free_result($result);
               $package_id= $row1['pack_id'];
                }
                
if(isset($package_id)&&$package_id!=0)
{
    $sql='Delete from "tbl_element_package" WHERE pack_id='.$package_id.';'; 
    for($i=0;$i<count($elementtype);$i++)
    {
   
$sql.='INSERT INTO tbl_element_package ("pack_id"';
		
		
		if(isset($elementtype[$i]) && $elementtype[$i]!="")
		$sql.=', "element_type"';
		if(isset($vendor_ID[$i]) && $vendor_ID[$i]!="")
		$sql.=', "vendor_id"';
		if(isset($elementstyle[$i]) && $elementstyle[$i]!="")
		$sql.=',  "style"';	
		if(isset($elementcolor[$i]) && $elementcolor[$i]!="")
		$sql.=',  "color"';	
		if(isset($elementcost[$i]) && $elementcost[$i]!="")
		$sql.=',  "cost"';	
		if(isset($elementlabor[$i]) && $elementlabor[$i]!="")
		$sql.=', "labor"';
		if(isset($order_date[$i]) && $order_date[$i]!="")
		$sql.=', "order_date"';
		if(isset($element_conf_num[$i]) && $element_conf_num[$i]!="")
		$sql.=', "conf_num"';
		if(isset($element_track_num[$i]) && $element_track_num[$i]!="")
		$sql.=', "track_num"';
		if(isset($element_delivered[$i]) && $element_delivered[$i]!="")
		$sql.=', "delivered"';
		if(isset($element_file0[$i]) && $element_file0[$i]!="")
		$sql.=',  "image"';	
		if(isset($element_file1[$i]) && $element_file1[$i]!="")
		$sql.=',  "file"';	
                
		$sql.=")";
		$sql.=" VALUES (".$package_id;
	
		if(isset($elementtype[$i]) && $elementtype[$i]!="")
		$sql.=" ,'".$elementtype[$i]."'";
		if(isset($vendor_ID[$i]) && $vendor_ID[$i]!="")
		$sql.=" ,'".$vendor_ID[$i]."'";
		if(isset($elementstyle[$i]) && $elementstyle[$i]!="")
		$sql.=" ,'".$elementstyle[$i]."'";
		if(isset($elementcolor[$i]) && $elementcolor[$i]!="")
		$sql.=" ,'".$elementcolor[$i]."'";
		if(isset($elementcost[$i]) && $elementcost[$i]!="")
		$sql.=" ,'".$elementcost[$i]."'";
		if(isset($elementlabor[$i]) && $elementlabor[$i]!="")
		$sql.=", '".$elementlabor[$i]."'";
		if(isset($order_date[$i]) && $order_date[$i]!="")
		$sql.=", '".$order_date[$i]."'";
		if(isset($element_conf_num[$i]) && $element_conf_num[$i]!="")
		$sql.=", '".$element_conf_num[$i]."'";
		if(isset($element_track_num[$i]) && $element_track_num[$i]!="")
		$sql.=", '".$element_track_num[$i]."'";
		if(isset($element_delivered[$i]) && $element_delivered[$i]!="")
		$sql.=", '".$element_delivered[$i]."'";
		if(isset($element_file0[$i]) && $element_file0[$i]!="")
		$sql.=" ,'".$element_file0[$i]."'";
		if(isset($element_file1[$i]) && $element_file1[$i]!="")
		$sql.=" ,'".$element_file1[$i]."'";
		
                 
		$sql.=' ); ';
	}
		//echo $sql;
		if(!($result=pg_query($connection,$sql)))
		{
			$return_arr['error'] = "Basic tab :".pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		}
		pg_free_result($result);
}

$ret_arr = array();
$ret_arr['pack_id'] = $package_id;

header('Content-type: application/json');
echo json_encode($ret_arr);
		?>