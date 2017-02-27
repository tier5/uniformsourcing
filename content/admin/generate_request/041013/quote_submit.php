<?php 
require('Application.php');
require($JSONLIB.'jsonwrapper.php');

$return_arr = array();

extract($_POST);
$other_name  = pg_escape_string($other_name);
$other_street  = pg_escape_string($other_street);
$other_city  = pg_escape_string($other_city);
$other_state  = pg_escape_string($other_state);
$other_zip  = pg_escape_string($other_zip);
$client_shipto  = pg_escape_string($client_shipto);
$client_customer_id  = pg_escape_string($client_customer_id);
$vendor_shipto  = pg_escape_string($vendor_shipto);
$po_number  = pg_escape_string($po_number);
$shipto_vendorId  = pg_escape_string($shipto_vendorId);
$other_shipper  = pg_escape_string($other_shipper);
$instructions  = pg_escape_string($instructions);
$track_audit =0;
$return_arr['name'] = "";
$return_arr['error'] = "";
$return_arr['qid'] = 0;
$log_desc="";
if(isset($_POST['submit']))
{
	if($po_number == "")
	{
		$return_arr['error'] = "Enter PO Number";
		echo json_encode($return_arr);
		return;	
	}
	$sql="Select count(*) as n from tbl_quote where po_number='$po_number' and status = 1";
	if($qid >0)
	$sql .= " and qid <> $qid";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query: " . pg_last_error($connection));
		exit;
	}
	$quoteCount = "";
	while($row = pg_fetch_array($result))
	{
		$quoteCount=$row;
	}
	if((int)$quoteCount['n'] >0)
	{
		$return_arr['error'] = "P.O Number already exist";
		echo json_encode($return_arr);
		return;
	}
	
	
	$query = "";
	$log_module="GenerateRequest";
	if($_POST['submit'] == 'Add')
	{
		$log_desc ="New Generate Request Added";
  		$query="INSERT INTO tbl_quote ( status";
		if($company_val >0)$query.=",company_id ";
		if($vendor_id >0)$query.=", vendor_id ";
		if($client >0)$query.=", client_id ";
		if($ship_to_select>0)$query.=", ship_to ";
		if($other_name!="")$query.=", other_name ";
		if($other_street!="")$query.=", other_street ";
		if($other_city!="")$query.=", other_city ";
		if($other_state!="")$query.=", other_state ";
		if($other_zip!="")$query.=", other_zip ";
		if($client_shipto!="")$query.=", ship_to_clientfield ";
		if($client_customer_id!="")$query.=", ship_to_customer_id ";
		if($vendor_shipto!="")$query.=", ship_to_vendorfield ";
		if($po_number!="")$query.=", po_number ";
		if($podate!="")$query.=", po_date ";
		if($internalpo!="")$query.=", internal_po ";
		if($shipto_vendorId!="")$query.=", shipto_vendor_id ";
		if($goods_through!="")$query.=", good_thru ";
		if($payment_terms >0)$query.=", payment_id ";
		if($salesrep>0)$query.=", sales_rep ";
		if($amountsubtotal!="")$query.=", amount_sub_total ";
		if($taxsubtotal!="")$query.=", tax_sub_total ";
		if($total!="")$query.=", total ";
		if($shipvia>0)$query.=", ship_via ";
		if($other_shipper!="")$query.=", shipperno ";
		if($client_shipper>0)$query.=", client_shipper ";
		if($carrier>0)$query.=", carrier_id ";
		if($instructions!="")$query.=", instruction_notes ";
		if($project_name!="")$query.=", project_name ";
		$query.=", createdby ";
		$query.=", createddate ";
		$query.=", updateddate ";
		$query.=")";
		$query.=" VALUES ( 1";
		if($company_val >0)$query.=",$company_val ";
		if($vendor_id >0)$query.=" ,$vendor_id ";
		if($client >0)$query.=" ,$client ";
		if($ship_to_select>0)$query.=" ,$ship_to_select ";
		if($other_name!="")$query.=" ,'$other_name' ";
		if($other_street!="")$query.=",'$other_street' ";
		if($other_city!="")$query.=" ,'$other_city' ";
		if($other_state!="")$query.=" ,'$other_state' ";
		if($other_zip!="")$query.=" ,'$other_zip' ";
		if($client_shipto!="")$query.=",'$client_shipto' ";
		if($client_customer_id!="")$query.=" ,'$client_customer_id' ";
		if($vendor_shipto!="")$query.=" ,'$vendor_shipto' ";
		if($po_number!="")$query.=" ,'$po_number' ";
		if($podate!="")$query.=",".strtotime($podate);
		if($internalpo!="")$query.=" ,'$internalpo' ";
		if($shipto_vendorId!="")$query.=" ,'$shipto_vendorId' ";
		if($goods_through!="")$query.=" ,".strtotime($goods_through);
		if($payment_terms >0)$query.=" ,$payment_terms ";
		if($salesrep>0)$query.=" ,$salesrep ";
		if($amountsubtotal!="")$query.=" ,$amountsubtotal ";
		if($taxsubtotal!="")$query.=" ,$taxsubtotal ";
		if($total!="")$query.=" ,$total ";
		if($shipvia>0)$query.=" ,'$shipvia' ";
		if($other_shipper!="")$query.=" ,'$other_shipper' ";
		if($client_shipper>0)$query.=" ,$client_shipper ";
		if($carrier>0)$query.=" ,$carrier ";
		if($instructions!="")$query.=",'$instructions' ";
		if($project_name!="")$query.=",'$project_name' ";
		$query.=" ,{$_SESSION['employeeID']} ";
		$query.=" ,'".date(U)."' ";
		$query.=" ,'".date(U)."' ";
		$query.=")";
		if($query != "")
		{
			if(!($result= pg_query($connection,$query)))
			{
				$return_arr['error'] = "Error while storing quote information to database!". pg_last_error($connection);	
				echo json_encode($return_arr);
				return;
			}
			pg_free_result($result);
			
			if($pid == 0)
			{
				$query="select qid from tbl_quote where po_number = '$po_number' limit 1";
				if(!($result= pg_query($connection,$query)))
				{
					$return_arr['error'] = "Error while getting quote information from database!". pg_last_error($connection);	
					echo json_encode($return_arr);
					return;
				}
				while($row = pg_fetch_array($result)){
					$qid=$row['qid'];
				}
				pg_free_result($result);
			}
				
		}
		$return_arr['qid'] = $qid;
		
	}
	else if($_POST['submit'] == 'Save')
	{
		$log_desc = "Updated Generate Request";
		$query="Update tbl_quote SET ";
		$query.="status =1";
		if($company_val >0)$query.=", company_id =$company_val";
		else $query.=", company_id =0";
		if($vendor_id >0)$query.=", vendor_id =$vendor_id";
		else $query.=", vendor_id =0";
		if($client >0)$query.=", client_id =$client ";
		else $query.=", client_id =0 ";
		if($ship_to_select>0)$query.=", ship_to =$ship_to_select ";
		else $query.=", ship_to =0 ";
		if($other_name!="")$query.=", other_name ='$other_name'";
		else $query.=", other_name =null";
		if($other_street!="")$query.=", other_street ='$other_street'";
		else $query.=", other_street =null";
		if($other_city!="")$query.=", other_city ='$other_city'";
		else $query.=", other_city =null";
		if($other_state!="")$query.=", other_state ='$other_state' ";
		else $query.=", other_state =null ";
		if($other_zip!="")$query.=", other_zip ='$other_zip' ";
		else $query.=", other_zip =null ";
		if($client_shipto!="")$query.=", ship_to_clientfield ='$client_shipto'";
		else $query.=", ship_to_clientfield =null";
		if($client_customer_id!="")$query.=", ship_to_customer_id ='$client_customer_id'";
		else $query.=", ship_to_customer_id =null";
		if($vendor_shipto!="")$query.=", ship_to_vendorfield ='$vendor_shipto' ";
		else $query.=", ship_to_vendorfield = 0 ";
		if($po_number!="")$query.=", po_number ='$po_number'";
		else $query.=", po_number =null";
		if($internalpo!="")$query.=", internal_po ='$internalpo'";
		else $query.=", internal_po =null";
		if($podate!="")$query.=", po_date =".strtotime($podate);
		else $query.=", po_date =null";
		if($shipto_vendorId!="")$query.=", shipto_vendor_id ='$shipto_vendorId' ";
		else $query.=", shipto_vendor_id =null ";
		if($goods_through!="")$query.=", good_thru =".strtotime($goods_through);
		if($payment_terms >0)$query.=", payment_id =$payment_terms ";
		else $query.=", payment_id =0";
		if($salesrep>0)$query.=", sales_rep =$salesrep";
		else $query.=", sales_rep =$salesrep";		
		if($amountsubtotal!="")$query.=", amount_sub_total =$amountsubtotal";
		else $query.=", amount_sub_total =0";
		if($taxsubtotal!="")$query.=", tax_sub_total ='$taxsubtotal'";
		else $query.=", tax_sub_total =0";
		if($total!="")$query.=", total ='$total'";
		else $query.=", total =0";
		if($shipvia>0)$query.=", ship_via =$shipvia";
		else $query.=", ship_via =0";
		if($other_shipper!="")$query.=", shipperno ='$other_shipper'";
		else $query.=", shipperno =null";
		if($client_shipper>0)$query.=", client_shipper =$client_shipper";
		if($carrier>0)$query.=", carrier_id =$carrier";
		else $query.=", carrier_id =0";
		if($instructions!="")$query.=", instruction_notes ='$instructions'";
		else $query.=", instruction_notes =null";
		if($project_name!="")$query.=", project_name ='$project_name'";
		else $query.=", project_name =null";
		$query.=", updatedby ='{$_SESSION['employeeID']}' ";
		$query.=", updateddate ='".date(U)."' ";
		$query.=" where qid=".$qid;			
	}
	if($query != "")
	{
	if(!($result=pg_query($connection,$query)))
	{
		$return_arr['error'] = "Error while storing Quote information to database!". pg_last_error($connection);	
		echo json_encode($return_arr);
		return;
	}	
	pg_free_result($result);	
	}
	$return_arr['qid'] = $qid;
	$log_id = $qid;
	$query = "";
	for($i = 0; $i < count($item);$i++)
	{
		$item[$i] = pg_escape_string($item[$i]);
		$desc[$i] = pg_escape_string($desc[$i]);
		if($item[$i] != "" && $hdn_id[$i] != "" && $hdn_id[$i]> 0)
		{
			$query .= "Update tbl_quote_items SET status =1";
			$query .= ", qid = $qid ";
			$query .= ", itemno = '$item[$i]' ";
			if($desc[$i] !="")$query .= ", description = '$desc[$i]' ";
			else $query .= ", description = null ";
			if($unitprice[$i] !="")$query .= ", unit_price = $unitprice[$i] ";
			else $query .= ", unit_price =0 ";
			if($quantity[$i] !="")$query .= ", quantity = $quantity[$i] ";
			else $query .= ", quantity = 0 ";
			if($tax_type[$i] >0)$query .= ", tax_type = $tax_type[$i] ";
			else $query .= ", tax_type = 0";
			if($tax_amount[$i] !="")$query .= ", tax_amount = $tax_amount[$i] ";
			else $query .= ", tax_amount = 0 ";
			if($amount[$i] !="")$query .= ", amount = $amount[$i] ";
			else $query .= ", amount = 0 ";
			$query .=" where item_id=".$hdn_id[$i]." ; ";
		}
		else if($hdn_id[$i] != "")
		{
			$query .="INSERT INTO tbl_quote_items ( ";
			$query .=" qid ";
			$query .=", itemno ";
			$query .=", description ";
			if($unitprice[$i] !="")$query .=", unit_price ";
			if($quantity[$i] !="")$query .=", quantity ";
			if($tax_type[$i] >0)$query .=", tax_type ";
			$query .=", tax_amount ";
			$query .=", amount ";
			$query .=")";
			$query .=" VALUES (";
			$query .=" '$qid' ";
			$query .=", '{$item[$i]}' ";
			$query .=", '{$desc[$i]}' ";
			if($unitprice[$i] !="")$query .=", {$unitprice[$i]} ";
			if($quantity[$i] !="")$query .=", {$quantity[$i]} ";
			if($tax_type[$i] >0)$query .=",{$tax_type[$i]} ";
			$query .=", {$tax_amount[$i]} ";
			$query .=", {$amount[$i]} ";
			$query .="); ";
		}
	}
	if($query != "")
	{
		if(!($result= pg_query($connection,$query)))
		{
		  $return_arr['error'] = "Error while storing item information to database!". pg_last_error($connection);	
		  echo json_encode($return_arr);
		  return;
		}
		pg_free_result($result);
	}
	if($log_desc!="")
	{
		$sql="INSERT INTO tbl_change_record (";
		$sql.=" log_date ";
		if($log_desc!="")$sql.=", log_desc ";
		if($log_module!="")$sql.=", module ";
		if($log_id!="")$sql.=", module_id ";
		$sql.=", status ";
		$sql.=", created_date ";
		$sql.=", employee_id ";	
		$sql.=")";
		$sql.=" VALUES (";				   
		$sql.=" ".date('U');
		if($log_desc!="")$sql.=", '".$log_desc."' ";
		if($log_module!="")$sql.=", '".$log_module."' ";
		if($log_id!="")$sql.=", $log_id ";
		$sql.=" ,1 ";
		$sql.=" ,".date('U');
		$sql.=" ,".$_SESSION["employeeID"];
		$sql.=" )".";";
		if(!($result=pg_query($connection,$sql)))
		{
			$return_arr['error'] = "Basic tab :".pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		}
		pg_free_result($result);		
		$sql ="";
		$log_desc ="";
	}
	$return_arr['qid'] = $qid;
}
echo json_encode($return_arr);
return;	
?>