<?php 
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
$current_page ="new_order.add.php";
$type = "project_purchase";
$paging = 'paging=';
if(isset($_GET['paging']) && $_GET['paging'] != "")
{
	$paging .= $_GET['paging'];
}
else
{
	$paging .= 1;
}
$filePath = $upload_dir;
$return_arr = array();
extract($_POST);
$return_arr['error'] = "";
$return_arr['name'] = "";
$return_arr['id'] = "";
if((isset($_POST['prj_name']) && $_POST['prj_name']!="") && (isset($_POST['prj_id']) && $_POST['prj_id']!=""))
{
	$isEdit = 1;
	$source_pid = $_POST['prj_id'];
	$name = $_POST['prj_name'];
	$sql = "Select * from tbl_newproject where projectname = '$name'";
	if(!($result=pg_query($connection,$sql))){
		print("Failed sql: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_prj_name=$row;
	}
	pg_free_result($result);
	if(count($data_prj_name)>0)
	{
		$return_arr['error'] = "Project name already exist!";	
	  	echo json_encode($return_arr);
	  	return;
	}
	else 
	{
		$sql = "Select * from tbl_newproject_closed where pid = $source_pid";
		//echo $sql;
		if(!($result=pg_query($connection,$sql))){
			print("Failed sql: " . pg_last_error($connection));
			exit;
		}
		while($row = pg_fetch_array($result)){
			$data_prj=$row;
		}
		pg_free_result($result);
		
		$sql = "Select * from tbl_prjpurchase_closed where status =1 and pid = $source_pid";
		//echo $sql;
		if(!($result=pg_query($connection,$sql))){
			print("Failed sql: " . pg_last_error($connection));
			exit;
		}
		while($row = pg_fetch_array($result)){
			$data_prjPurchase =$row;
		}
		if( $data_prjPurchase['purchaseId'] !="")
		$purchaseId = $data_prjPurchase['purchaseId'];
		pg_free_result($result);
			
		$sql = "select * from tbl_prjvendor_closed inner join vendor on vendor.\"vendorID\"=tbl_prjvendor_closed.vid where status =1 and pid = $source_pid";
		//echo $sql;
		if(!($result=pg_query($connection,$sql))){
			print("Failed sql: " . pg_last_error($connection));
			exit;
		}
		while($row = pg_fetch_array($result)){
			$data_prjVendor[]=$row;
		}
		pg_free_result($result);
		
				
		$sql = "select * from tbl_prj_style_closed where status =1 and pid = $source_pid";
		//echo $sql;
		if(!($result=pg_query($connection,$sql))){
			print("Failed sql: " . pg_last_error($connection));
			exit;
		}
		while($row = pg_fetch_array($result)){
			$data_prj_style[]=$row;
		}
		pg_free_result($result);	
		$sql = "Select * from tbl_prjsample_closed where status =1 and pid = $source_pid";	
		//echo $sql;
		if(!($result=pg_query($connection,$sql))){
			print("Failed sql: " . pg_last_error($connection));
			exit;
		}
		while($row = pg_fetch_array($result)){
			$data_prjSample =$row;
		}
		if($data_prjSample['sampleId']!="")
		$sampleId = $data_prjSample['sampleId'];
		pg_free_result($result);
		
		$sql = "Select * from tbl_prjpricing_closed where status =1 and pid = $source_pid";
		//echo $sql;
		if(!($result=pg_query($connection,$sql))){
			print("Failed sql: " . pg_last_error($connection));
			exit;
		}
		while($row = pg_fetch_array($result)){
			$data_prjPricing =$row;
		}
		if($data_prjPricing['pricingId']!="")
		$pricingId = $data_prjPricing['pricingId'];
		pg_free_result($result);
		
		$sql = "Select * from \"projectEstimatedUnitCost\" where pid = $source_pid";
	//	echo $sql;	
		if(!($result=pg_query($connection,$sql))){
			print("Failed sql: " . pg_last_error($connection));
			exit;
		}
		while($row = pg_fetch_array($result)){
			$data_prj_estimate =$row;
		}
		if($data_prj_estimate['prj_estimate_id']!="")
		$estimate_id = $data_prj_estimate['prj_estimate_id'];
		pg_free_result($result);
		
		$sql = "Select * from tbl_prmilestone_closed where pid = $source_pid and status = 1";	
		//echo $sql;
		if(!($result=pg_query($connection,$sql))){
			print("Failed sql: " . pg_last_error($connection));
			exit;
		}
		while($row = pg_fetch_array($result)){
			$data_prj_milestone = $row;
		}
		if($data_prj_milestone['id']!="")
		$milestone_id = $data_prj_milestone['id'];
		pg_free_result($result);
		
		$sql = "Select * from tbl_prj_elements_closed where status =1 and pid = $source_pid";
		//echo $sql;
		if(!($result=pg_query($connection,$sql))){
			print("Failed sql: " . pg_last_error($connection));
			exit;
		}
		while($row = pg_fetch_array($result)){
			$data_prj_element_all[]  =$row;
		}
		pg_free_result($result);
		if($data_prj_element_all[(count($data_prj_element_all)-1)]['prj_element_id'] != "")
			$elementId = $data_prj_element_all[(count($data_prj_element_all)-1)]['prj_element_id'];
		//	echo $elementId;
		
		$sql = "Select tbl_mgt_notes_closed.*,e.firstname as \"firstName\", e.lastname as \"lastName\" from tbl_mgt_notes_closed inner join \"employeeDB\" as e on e.\"employeeID\" =tbl_mgt_notes_closed.\"createdBy\"  where \"isActive\" =1 and pid = $source_pid";
		//echo $sql;
		if(!($result=pg_query($connection,$sql))){
			print("Failed sql: " . pg_last_error($connection));
			exit;
		}
		while($row = pg_fetch_array($result)){
			$data_prjNotes[]  =$row;
		}
		pg_free_result($result);
		
		$sql = "Select * from tbl_prjimage_file_closed where status =1 and pid = $source_pid";
		//echo $sql;
		if(!($result=pg_query($connection,$sql))){
			print("Failed sql: " . pg_last_error($connection));
			exit;
		}
		while($row = pg_fetch_array($result)){
			$data_prjUploads[] =$row;
		}
		
		$imageArr = array();
		$fileArr = array();
		$pattern = "";
		$gradient = "";
		for($i = 0, $img= 0, $file = 0; $i < count($data_prjUploads); $i++)
		{
			if($data_prjUploads[$i]['type'] == 'P')
			{
				$patternId = $data_prjUploads[$i]['prjimageId'];
				$pattern = stripslashes($data_prjUploads[$i]['file_name']);
			}
			else if($data_prjUploads[$i]['type'] == 'G')
			{
				$gradientId = $data_prjUploads[$i]['prjimageId'];
				$gradient = stripslashes($data_prjUploads[$i]['file_name']);
			}
			else if($data_prjUploads[$i]['type'] == 'I')
			{
				$imageArr[$img]['id'] = $data_prjUploads[$i]['prjimageId'];
				$imageArr[$img++]['file'] = stripslashes($data_prjUploads[$i]['file_name']);
			}
			else if($data_prjUploads[$i]['type'] == 'F')
			{
				$fileArr[$file]['id'] = $data_prjUploads[$i]['prjimageId'];
				$fileArr[$file++]['file'] = stripslashes($data_prjUploads[$i]['file_name']);
			}
		}
		pg_free_result($result);
		
		/* order and shippping values*/
		$sql = "select tbl_prjorder_shipping_closed.*,tbl_carriers.weblink from  tbl_prjorder_shipping_closed left join tbl_carriers on tbl_carriers.carrier_id = tbl_prjorder_shipping_closed.carrier_id where tbl_prjorder_shipping_closed.status=1 and pid = $source_pid";
		//echo $sql;
		if(!($result=pg_query($connection,$sql))){
			print("Failed sql: " . pg_last_error($connection));
			exit;
		}
		while($row = pg_fetch_array($result)){
			$data_order_shipping[]=$row;
		}
		pg_free_result($result);
	}
	$sql= 'Select "employeeID",firstname,lastname,"employeeType" from "employeeDB" where active =\'yes\' ';
	if(!($result=pg_query($connection,$sql))){
		print("Failed sql: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_employee[]=$row;
	}
	pg_free_result($result);
	
	
	$sql=("SELECT \"ID\", \"clientID\", \"client\", \"active\" ".
			 "FROM \"clientDB\" ".
			 "WHERE \"active\" = 'yes' ".
			 "ORDER BY \"client\" ASC");
	if(!($result1=pg_query($connection,$sql))){
		print("Failed sql: " . pg_last_error($connection));
		exit;
	}
	while($row1 = pg_fetch_array($result1)){
		$data1[]=$row1;
	}
	pg_free_result($result1);
	
	$sql = 'select id, "srID" from "tbl_sampleRequest" where status=1';
	if(!($result1=pg_query($connection,$sql))){
		print("Failed sql: " . pg_last_error($connection));
		exit;
	}
	while($row1 = pg_fetch_array($result1)){
		$data_sample[]=$row1;
	}
	pg_free_result($result1);
	
	$queryVendor="SELECT \"vendorID\", \"vendorName\", \"active\" ".
			 "FROM \"vendor\" ".
			 "WHERE \"active\" = 'yes' ".
			 "ORDER BY \"vendorName\" ASC ";
			 //echo $queryVendor;
		if(!($result=pg_query($connection,$queryVendor))){
		print("Failed sql: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_Vendr[]=$row;
	}
	pg_free_result($result);
	$sql="SELECT element_id, elements ".
			 "FROM tbl_elements ".
			 "WHERE status = '1' ".
			 "ORDER BY elements ASC ";
		if(!($result=pg_query($connection,$sql))){
		print("Failed sql: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_elements[]=$row;
	}
	pg_free_result($result);
	
	/* carrier values*/
	$sql = "select carrier_id,carrier_name  from  tbl_carriers where status=1";
	if(!($result=pg_query($connection,$sql))){
		print("Failed sql: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_carrier[]=$row;
	}
	pg_free_result($result);	
	
	if(count($data_prj)>0)
	{
		$sql = "Select nextval(('tbl_newproject_pid_seq'::text)::regclass) as pid";
		if(!($result=pg_query($connection,$sql))){
			$return_arr['error'] ="Error while getting project nextval from database!";	
			echo json_encode($return_arr);
			return;
		}
		$destination_pid = pg_fetch_result($result, 0, 'pid');
		pg_free_result($result);
		$sql="INSERT INTO tbl_newproject (";
		if($destination_pid!="")$sql.=" pid ";
		if($name!="")$sql.=", projectname ";
		if($data_prj['client']!="")$sql.=" ,client ";
		if($data_prj['color']!="")$sql.=", color ";
		if($data_prj['style']!="")$sql.=", style ";
		if($data_prj['materialtype']!="")$sql.=", materialtype ";
		if($data_prj['project_manager']!="")$sql.=", project_manager ";
		$sql.=", status ";
		if($data_prj['updateddate']!="")$sql.=", updateddate ";
		if($data_prj['created_date']!="")$sql.=", created_date ";
		if($data_prj['createdby']!="")$sql.=", createdby ";
		if($data_prj['stock_or_custom']!="")$sql.=", stock_or_custom ";
		if($data_prj['notification']!="")$sql.=", notification ";
		$sql.=")";
		$sql.=" VALUES (";
		if($destination_pid!="")$sql.=" '$destination_pid' ";
		if($name!="")$sql.=", '".$name."' ";
		if($data_prj['client']!="")$sql.=", ".pg_escape_string($data_prj['client']);
		if($data_prj['color']!="")$sql.=", '".pg_escape_string($data_prj['color'])."' ";
		if($data_prj['style']!="")$sql.=", '".pg_escape_string($data_prj['style'])."' ";
		if($data_prj['materialtype']!="")$sql.=" ,'".pg_escape_string($data_prj['materialtype'])."' ";
		if($data_prj['project_manager']!="")$sql.=" ,".pg_escape_string($data_prj['project_manager']);
		$sql.=" ,1";
		if($data_prj['updateddate']!="")$sql.=" ,'".pg_escape_string($data_prj['updateddate'])."' ";
		if($data_prj['created_date']!="")$sql.=" ,'".pg_escape_string($data_prj['created_date'])."'";
		if($data_prj['createdby']!="")$sql.=" ,'".pg_escape_string($data_prj['createdby'])."'";
		if($data_prj['stock_or_custom']!="")$sql.=" ,".pg_escape_string($data_prj['stock_or_custom']);
		if($data_prj['notification']!="")$sql.=", '".pg_escape_string($data_prj['notification'])."' ";
		$sql.=" )";
		//echo $sql;
		if(!($result= pg_query($connection,$sql)))
		{
		  $return_arr['error'] = "Error while storing project information to database!";	
		  echo json_encode($return_arr);
		  return;
		}
		$sql = "";
		pg_free_result($result);
		
		if(count($data_prjPurchase)>0)
		{
			$sql="INSERT INTO tbl_prjpurchase (";
			$sql.=" pid";
			if($data_prjPurchase['sizeneeded']!="")$sql.=", sizeneeded ";
			if($data_prjPurchase['garmentdesc']!="")$sql.=", garmentdesc ";
			if($data_prjPurchase['pt_invoice']!="")$sql.=", pt_invoice";
			$sql.=", status ";
			if($data_prjPurchase['createddate']!="")$sql.=", createddate ";
			if($data_prjPurchase['updateddate']!="")$sql.=", updateddate ";
			$sql.=")";
			$sql.=" VALUES (";
			$sql.=" $destination_pid ";
			if($data_prjPurchase['sizeneeded']!="")$sql.=", '".pg_escape_string($data_prjPurchase['sizeneeded'])."' ";
			if($data_prjPurchase['garmentdesc']!="")$sql.=" ,'".pg_escape_string($data_prjPurchase['garmentdesc'])."' ";
			if($data_prjPurchase['pt_invoice']!="")$sql.=" ,'".pg_escape_string($data_prjPurchase['pt_invoice'])."' ";
			$sql.=" ,1";
			if($data_prjPurchase['createddate']!="")$sql.=" ,'".pg_escape_string($data_prjPurchase['createddate'])."' ";
			if($data_prjPurchase['updateddate']!="")$sql.=" ,'".pg_escape_string($data_prjPurchase['updateddate'])."'";
			$sql.=" );";
			//echo $sql;
			if(!($result= pg_query($connection,$sql)))
			{
			  $return_arr['error'] = "Error while storing purchase information to database!";	
			  echo json_encode($return_arr);
			  return;
			}
			$sql = "";
			pg_free_result($result);
		}//end of purchase if statement
		if(count($data_prjVendor)>0)
		{
			for($vendor_index =0;$vendor_index < count($data_prjVendor); $vendor_index++)
			{
				$sql.="INSERT INTO tbl_prjvendor(";
				$sql.=" pid ";
				$sql.=", status ";
				if($data_prjVendor[$vendor_index]['createddate']!="")$sql.=", createddate ";
				if($data_prjVendor[$vendor_index]['vid']!="")$sql.= ", vid";
                                if($data_prjVendor[$vendor_index]['sent_date']!="")$sql.= ", sent_date";
                                 if($data_prjVendor[$vendor_index]['confirm_num']!="")$sql.= ", confirm_num";
                                  if($data_prjVendor[$vendor_index]['upload_file']!="")$sql.= ", upload_file";
                                     if($data_prjVendor[$vendor_index]['vendor_po']!="")$sql.= ", vendor_po";
				$sql.=" ) VALUES(";
				$sql.=" '$destination_pid' ";
				$sql.=", 1";
				if($data_prjVendor[$vendor_index]['createddate']!="")$sql.=",'".pg_escape_string($data_prjVendor[$vendor_index]['createddate'])."' ";
				if($data_prjVendor[$vendor_index]['vid']!="")$sql.=", '".pg_escape_string($data_prjVendor[$vendor_index]['vid'])."' ";
                                if($data_prjVendor[$vendor_index]['sent_date']!="")$sql.=", '".pg_escape_string($data_prjVendor[$vendor_index]['sent_date'])."' ";
                                if($data_prjVendor[$vendor_index]['confirm_num']!="")$sql.=", '".pg_escape_string($data_prjVendor[$vendor_index]['confirm_num'])."' ";
                                if($data_prjVendor[$vendor_index]['upload_file']!="")$sql.=", '".pg_escape_string($data_prjVendor[$vendor_index]['upload_file'])."' ";
                                if($data_prjVendor[$vendor_index]['vendor_po']!="")$sql.=", '".pg_escape_string($data_prjVendor[$vendor_index]['vendor_po'])."' ";
				$sql.=");";
				//echo $sql;
				if(!($result= pg_query($connection,$sql)))
				{
				  $return_arr['error'] = "Error while storing vendor information to database!";	
				  echo json_encode($return_arr);
				  return;
				}
				$sql = "";
				pg_free_result($result);
			}//end of vendor for loop
		}//end of vendor if statement
		if(count($data_prjSample)>0)
		{
			$sql.="INSERT INTO tbl_prjsample (";
			$sql.=" pid";
			if($data_prjSample['sampleprovided']!="")$sql.=" ,sampleprovided ";
			if($data_prjSample['product_client']!="")$sql.=", product_client ";
			if($data_prjSample['sampledate']!="")$sql.=", sampledate ";
			if($data_prjSample['samplenumberId']!="")$sql.=", \"samplenumberId\" ";
			if($data_prjSample['embroidery']!="")$sql.=", embroidery ";
			if($data_prjSample['silkscreening']!="")$sql.=", silkscreening ";
			if($data_prjSample['etaproduction']!="")$sql.=", etaproduction ";
			if($data_prjSample['status']!="")$sql.=", status ";
			if($data_prjSample['createddate']!="")$sql.=", createddate ";
			if($data_prjSample['updateddate']!="")$sql.=", updateddate ";
			$sql.=")";
			$sql.=" VALUES (";
			$sql.="  $destination_pid  ";
			if($data_prjSample['sampleprovided']!="")$sql.=" ,'".pg_escape_string($data_prjSample['sampleprovided'])."'";
			if($data_prjSample['product_client']!="")$sql.=" ,'".pg_escape_string($data_prjSample['product_client'])."' ";
			if($data_prjSample['sampledate']!="")$sql.=", '".pg_escape_string($data_prjSample['sampledate'])."' ";
			if($data_prjSample['samplenumberId']!="")$sql.=", '".pg_escape_string($data_prjSample['samplenumberId'])."' ";
			if($data_prjSample['embroidery']!="")$sql.=", '".pg_escape_string($data_prjSample['embroidery'])."' ";
			if($data_prjSample['silkscreening']!="")$sql.=", '".pg_escape_string($data_prjSample['silkscreening'])."' ";
			if($data_prjSample['etaproduction']!="")$sql.=" ,'".pg_escape_string($data_prjSample['etaproduction'])."' ";
			if($data_prjSample['status']!="")$sql.=" ,'".pg_escape_string($data_prjSample['status'])."' ";
			if($data_prjSample['createddate']!="")$sql.=" ,".pg_escape_string($data_prjSample['createddate']);
			if($data_prjSample['updateddate']!="")$sql.=" ,".pg_escape_string($data_prjSample['updateddate']);
		//	if($data_prjSample['updateddate']!="")sql.=" ,'".$data_prjSample['updateddate']."' ";
			$sql.=" );";
			//echo $sql;
			if(!($result= pg_query($connection,$sql)))
			{
			  $return_arr['error'] = "Error while storing sample information to database!";	
			  echo json_encode($return_arr);
			  return;
			}
			$sql = "";
			pg_free_result($result);
		}//end of samples if statement
		if(count($data_prjPricing) >0)
		{
			$sql.="INSERT INTO tbl_prjpricing (";
			$sql.=" pid";
			if($data_prjPricing['pt_invoice']!="")$sql.=" ,pt_invoice ";
			if($data_prjPricing['shipping_cost']!="")$sql.=", shipping_cost ";
			if($data_prjPricing['taxes']!="")$sql.=", taxes ";
			if($data_prjPricing['targetpriceunit']!="")$sql.=" ,targetpriceunit ";
			if($data_prjPricing['targetretail']!="")$sql.=", targetretail ";
			if($data_prjPricing['prjquote']!="")$sql.=", prjquote ";
			if($data_prjPricing['prjcost']!="")$sql.=", prjcost ";
			if($data_prjPricing['prj_estimatecost']!="")$sql.=", prj_estimatecost ";
			if($data_prjPricing['prj_completioncost']!="")$sql.=", prj_completioncost ";
			$sql.=", status ";
			if($data_prjPricing['createddate']!="")$sql.=", createddate ";
			if($data_prjPricing['updateddate']!="")$sql.=", updateddate ";
			if($data_prjPricing['prj_est_profit']!="")$sql.=",prj_est_profit";
			$sql.=")";
			$sql.=" VALUES (";
			$sql.="  $destination_pid  ";
			if($data_prjPricing['pt_invoice']!="")$sql.=", '".pg_escape_string($data_prjPricing['pt_invoice'])."' ";
			if($data_prjPricing['shipping_cost']!="")$sql.=", '".pg_escape_string($data_prjPricing['shipping_cost'])."' ";
			if($data_prjPricing['taxes']!="")$sql.=", '".pg_escape_string($data_prjPricing['taxes'])."' ";
			if($data_prjPricing['targetpriceunit']!="")$sql.=", '".pg_escape_string($data_prjPricing['targetpriceunit'])."' ";
			if($data_prjPricing['targetretail']!="")$sql.=", '".pg_escape_string($data_prjPricing['targetretail'])."' ";
			if($data_prjPricing['prjquote']!="")$sql.=", '".pg_escape_string($data_prjPricing['prjquote'])."' ";
			if($data_prjPricing['prjcost']!="")$sql.=", '".pg_escape_string($data_prjPricing['prjcost'])."' ";
			if($data_prjPricing['prj_estimatecost']!="")$sql.=", '".pg_escape_string($data_prjPricing['prj_estimatecost'])."' ";
			if($data_prjPricing['prj_completioncost']!="")$sql.=" ,'".pg_escape_string($data_prjPricing['prj_completioncost'])."' ";
			$sql.=" , 1";
			if($data_prjPricing['createddate']!="")$sql.=" ,'".pg_escape_string($data_prjPricing['createddate'])."' ";
			if($data_prjPricing['updateddate']!="")$sql.=" ,'".pg_escape_string($data_prjPricing['updateddate'])."'";
			if($data_prjPricing['prj_est_profit']!="")$sql.=",'".pg_escape_string($data_prjPricing['prj_est_profit'])."'";
			$sql.=" );";
			//echo $sql;
			if(!($result= pg_query($connection,$sql)))
			{
			  $return_arr['error'] = "Error while storing pricing information to database!";	
			  echo json_encode($return_arr);
			  return;
			}
			$sql = "";
			pg_free_result($result);
			
		}//end of if statement of pricing data
		if(count($data_prj_estimate)>0)
		{
			$sql="INSERT INTO \"projectEstimatedUnitCost\" ( status";
			$sql.=", pid";
			if($data_prj_estimate['ptrnsetup']!="")$sql.=" ,ptrnsetup ";
			if($data_prj_estimate['grdngsetup']!="")$sql.=", grdngsetup ";
			if($data_prj_estimate['smplefeesetup']!="")$sql.=", smplefeesetup ";
			if($data_prj_estimate['fabric']!="")$sql.=", fabric ";
			if($data_prj_estimate['trimfee']!="")$sql.=", trimfee ";
			if($data_prj_estimate['labour']!="")$sql.=", labour ";
			if($data_prj_estimate['duty']!="")$sql.=", duty ";
			if($data_prj_estimate['frieght']!="")$sql.=", frieght ";
			if($data_prj_estimate['other']!="")$sql.=", other ";
			$sql.=")";
			$sql.=" VALUES (1";
			$sql.=", $destination_pid ";
			if($data_prj_estimate['ptrnsetup']!="")$sql.=", '".pg_escape_string($data_prj_estimate['ptrnsetup'])."' ";
			if($data_prj_estimate['grdngsetup']!="")$sql.=", '".pg_escape_string($data_prj_estimate['grdngsetup'])."' ";
			if($data_prj_estimate['smplefeesetup']!="")$sql.=", '".pg_escape_string($data_prj_estimate['smplefeesetup'])."' ";
			if($data_prj_estimate['fabric']!="")$sql.=", '".pg_escape_string($data_prj_estimate['fabric'])."' ";
			if($data_prj_estimate['trimfee']!="")$sql.=", '".pg_escape_string($data_prj_estimate['trimfee'])."' ";
			if($data_prj_estimate['labour']!="")$sql.=" ,'".pg_escape_string($data_prj_estimate['labour'])."' ";
			if($data_prj_estimate['duty']!="")$sql.=" ,'".pg_escape_string($data_prj_estimate['duty'])."' ";
			if($data_prj_estimate['frieght']!="")$sql.=" ,'".pg_escape_string($data_prj_estimate['frieght'])."' ";
			if($data_prj_estimate['other']!="")$sql.=" ,'".pg_escape_string($data_prj_estimate['other'])."' ";
			$sql.=" );";
			//echo $sql;
			if(!($result= pg_query($connection,$sql)))
			{
			  $return_arr['error'] = "Error while storing estimated cost information to database!";	
			  echo json_encode($return_arr);
			  return;
			}
			$sql = "";
			pg_free_result($result);
		}//end of estimated unit price if statement
		if(count($data_prj_style)>0)
		{
			for($style_index=0; $style_index <count($data_prj_style);$style_index++)
			{
				$sql.="INSERT INTO tbl_prj_style (";
				$sql.=" pid";
				if($data_prj_style[$style_index]['garments']!="")$sql.= ", garments";
				if($data_prj_style[$style_index]['retailprice']!="")$sql.= ", retailprice";
				if($data_prj_style[$style_index]['priceunit']!="")$sql.= ", priceunit";
				if($data_prj_style[$style_index]['status']!="")$sql.=" ,status";
				if($data_prj_style[$style_index]['createddate']!="")$sql.=" ,createddate";
				$sql.=" ) VALUES(";
				$sql.=" '$destination_pid' ";
				if($data_prj_style[$style_index]['garments']!="")$sql.=", '".pg_escape_string($data_prj_style[$style_index]['garments'])."' ";
				if($data_prj_style[$style_index]['retailprice']!="")$sql.=", '".pg_escape_string($data_prj_style[$style_index]['retailprice'])."' ";
				if($data_prj_style[$style_index]['priceunit']!="")$sql.=", '".pg_escape_string($data_prj_style[$style_index]['priceunit'])."' ";
				if($data_prj_style[$style_index]['status']!="")$sql.=" ,'".pg_escape_string($data_prj_style[$style_index]['status'])."' ";
				if($data_prj_style[$style_index]['createddate']!="")$sql.=" ,'".pg_escape_string($data_prj_style[$style_index]['createddate'])."' ";
				$sql.=" );";
				//echo $sql;
				if(!($result= pg_query($connection,$sql)))
				{
				  $return_arr['error'] = "Error while storing multiple style information to database!";	
				  echo json_encode($return_arr);
				  return;
				}
				$sql = "";
				pg_free_result($result);
			}//end of for loop of multiple style
		}//end of style if statement
		
		if(count($data_prj_milestone)>0)
		{
			$sql="INSERT INTO tbl_prmilestone ( status";
			$sql.=", pid";
			if($data_prj_milestone['lapdip']!="")$sql.=" ,lapdip ";
			if($data_prj_milestone['lapdipapproval']!="")$sql.=", lapdipapproval ";
			if($data_prj_milestone['prdtnsample']!="")$sql.=", prdtnsample ";
			if($data_prj_milestone['prdtnsampleapprval']!="")$sql.=", prdtnsampleapprval ";
			if($data_prj_milestone['szngline']!="")$sql.=", szngline ";
			if($data_prj_milestone['desbordcmplt']!="")$sql.=", desbordcmplt";
			if($data_prj_milestone['desbordappval']!="")$sql.=", desbordappval";
			if($data_prj_milestone['created_date']!="")$sql.=", created_date ";
			if($data_prj_milestone['created_by']!="")$sql.=", created_by ";
			if($data_prj_milestone['design_board_calender']!="")$sql.=",design_board_calender";
			$sql.=")";
			$sql.=" VALUES (1";
			$sql.=", $destination_pid ";
			if($data_prj_milestone['lapdip']!="")$sql.=", '".pg_escape_string($data_prj_milestone['lapdip'])."' ";
			if($data_prj_milestone['lapdipapproval']!="")$sql.=", '".pg_escape_string($data_prj_milestone['lapdipapproval'])."' ";
			if($data_prj_milestone['prdtnsample']!="")$sql.=", '".pg_escape_string($data_prj_milestone['prdtnsample'])."' ";
			if($data_prj_milestone['prdtnsampleapprval']!="")$sql.=", '".pg_escape_string($data_prj_milestone['prdtnsampleapprval'])."' ";
			if($data_prj_milestone['szngline']!="")$sql.=" ,'".pg_escape_string($data_prj_milestone['szngline'])."' ";
			if($data_prj_milestone['desbordcmplt']!="")$sql.=" ,'".pg_escape_string($data_prj_milestone['desbordcmplt'])."' ";
			if($data_prj_milestone['desbordappval']!="")$sql.=" ,'".pg_escape_string($data_prj_milestone['desbordappval'])."' ";
			if($data_prj_milestone['created_date']!="")$sql.=" ,'".pg_escape_string($data_prj_milestone['created_date'])."' ";
			if($data_prj_milestone['created_by']!="")$sql.=" ,".pg_escape_string($data_prj_milestone['created_by']);
			if($data_prj_milestone['design_board_calender']!="")$sql.=",'".pg_escape_string($data_prj_milestone['design_board_calender'])."' ";
			$sql.=" )".";";
			//echo $sql;
			if(!($result= pg_query($connection,$sql)))
			{
			  $return_arr['error'] = "Error while storing milestone information to database!";	
			  echo json_encode($return_arr);
			  return;
			}
			$sql = "";
			pg_free_result($result);
		}//end of milestone if statement
		
		if(isset($data_prj_element_all))
		{
			for($element_index=0;$element_index< count($data_prj_element_all); $element_index++)
			{
				// file uploading
				$newfile_name = "";
				$newfile_name1 = "";
				if($data_prj_element_all[$element_index]['elementfile'] != "" && file_exists($filePath.$data_prj_element_all[$element_index]['elementfile']))
				{
					$file = substr($data_prj_element_all[$element_index]['elementfile'],(strpos($data_prj_element_all[$element_index]['elementfile'], '-')+1));
					//echo $file;
					
					if($file!="")
						$newfile_name = date('U').'-'.$file;
					else
						$newfile_name =date('U').'-'.$data_prj_element_all[$element_index]['elementfile'];
				
					
						copy($filePath.$data_prj_element_all[$element_index]['elementfile'], $filePath.$newfile_name );
						@ chmod($filePath.$newfile_name,0777);
				}
				
				//image uploading
				if($data_prj_element_all[$element_index]['image'] != "" && file_exists($filePath.$data_prj_element_all[$element_index]['image']))
				{
					$file1 = substr($data_prj_element_all[$element_index]['image'],(strpos($data_prj_element_all[$element_index]['image'], '-')+1));
										
						
					if($file1!="")
						$newfile_name1 = date('U').'-'.$file1;
					else
						$newfile_name1 =date('U').'-'.$data_prj_element_all[$element_index]['image'];
				
						copy($filePath.$data_prj_element_all[$element_index]['image'], $filePath.$newfile_name1 );
						@ chmod($filePath.$newfile_name1,0777);
				}
				else
				{
					$newfile_name1 = "";
					$newfile_name = "";
				}
								
				$sql.="INSERT INTO tbl_prj_elements (";
				$sql.=" pid";
				if($data_prj_element_all[$element_index]['elementtype']!=0)$sql.=" ,elementtype ";
				if($data_prj_element_all[$element_index]['vid']!=0)$sql.=", vid ";
				if($data_prj_element_all[$element_index]['style']!="")$sql.=", style ";
				if($data_prj_element_all[$element_index]['color']!="")$sql.=", color ";
				if($data_prj_element_all[$element_index]['element_cost']!="")$sql.=", element_cost ";
				if($newfile_name!="")$sql.=", elementfile ";
				if($newfile_name1!="")$sql.=", image ";
				$sql.=", status ";
				if($data_prj_element_all[$element_index]['createddate']!="")$sql.=", createddate ";
				if($data_prj_element_all[$element_index]['updateddate']!="")$sql.=", updateddate ";
				$sql.=")";
				$sql.=" VALUES (";
				$sql.="  $destination_pid  ";
				if($data_prj_element_all[$element_index]['elementtype']!="")$sql.=" ,'".pg_escape_string($data_prj_element_all[$element_index]['elementtype'])."' ";
				if($data_prj_element_all[$element_index]['vid']!=0)$sql.=", '".pg_escape_string($data_prj_element_all[$element_index]['vid'])."' ";
				if($data_prj_element_all[$element_index]['style']!="")$sql.=", '".pg_escape_string($data_prj_element_all[$element_index]['style'])."' ";
				if($data_prj_element_all[$element_index]['color']!="")$sql.=", '".pg_escape_string($data_prj_element_all[$element_index]['color'])."' ";
				if($data_prj_element_all[$element_index]['element_cost']!="")$sql.=", '".pg_escape_string($data_prj_element_all[$element_index]['element_cost'])."' ";
				if($newfile_name!="")$sql.=", '".pg_escape_string($newfile_name)."' ";
				if($newfile_name1!="")$sql.=", '".pg_escape_string($newfile_name1)."' ";
				$sql.=" ,1";
				if($data_prj_element_all[$element_index]['createddate']!="")$sql.=" ,'".pg_escape_string($data_prj_element_all[$element_index]['createddate'])."' ";
				if($data_prj_element_all[$element_index]['updateddate']!="")$sql.=" ,'".pg_escape_string($data_prj_element_all[$element_index]['updateddate'])."'";
				$sql.=" )".";";				
				if(!($result= pg_query($connection,$sql)))
				{
				  $return_arr['error'] = "Error while storing element information to database!";	
				  echo json_encode($return_arr);
				  return;
				}
				$sql = "";
				pg_free_result($result);
				
				
			}//end of for loop
		}//end of element if statement
		if(isset($data_prjUploads))
		{
			for($upload_index=0; $upload_index<count($data_prjUploads); $upload_index++)
			{
				//image file uploading
				
				if($data_prjUploads[$upload_index]['file_name']!="" && file_exists($filePath.$data_prjUploads[$upload_index]['file_name']) )
				{
					$file_upload = substr($data_prjUploads[$upload_index]['file_name'],(strpos($data_prjUploads[$upload_index]['file_name'], '-')+1));
										
						
					if($file_upload!="")
						$newfile_name_upload = date('U').'-'.$file_upload;
					else
						$newfile_name_upload =date('U').'-'.$data_prjUploads[$upload_index]['file_name'];
				
						copy($filePath.$data_prjUploads[$upload_index]['file_name'], $filePath.$newfile_name_upload );
						@ chmod($filePath.$newfile_name_upload,0777);
				}
				else
				{
					continue;
				}
				
				$sql.="INSERT INTO tbl_prjimage_file (";
				$sql.=" pid";
				if($newfile_name_upload!="")$sql.=" ,file_name ";
				if($data_prjUploads[$upload_index]['type']!="")$sql.=", \"type\"";
				$sql.=", status ";
				if($data_prjUploads[$upload_index]['createddate']!="")$sql.=", createddate ";
				if($data_prjUploads[$upload_index]['updateddate']!="")$sql.=", updateddate ";
				$sql.=")";
				$sql.=" VALUES (";
				$sql.="  $destination_pid  ";
				if($newfile_name_upload!="")$sql.=" ,'".pg_escape_string($newfile_name_upload)."' ";
				if($data_prjUploads[$upload_index]['type']!="")$sql.=", '".pg_escape_string($data_prjUploads[$upload_index]['type'])."' ";
				$sql.=", 1 ";
				if($data_prjUploads[$upload_index]['createddate']!="")$sql.=", '".pg_escape_string($data_prjUploads[$upload_index]['createddate'])."' ";
				if($data_prjUploads[$upload_index]['updateddate']!="")$sql.=", '".pg_escape_string($data_prjUploads[$upload_index]['updateddate'])."' ";
				$sql.=" );";
				if(!($result= pg_query($connection,$sql)))
				{
				  $return_arr['error'] = "Error while storing uploads information to database!";	
				  echo json_encode($return_arr);
				  return;
				}
				$sql = "";
				pg_free_result($result);
			}//end of uploads for loop
		}//end of uploads if loop
				
	}//end of if count(pname) statement
}//end of if isset(pid) statement

echo json_encode($return_arr);
return;
?>