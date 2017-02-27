<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');
$id = 0;
$pid = 0;
$isEdit=0;
$sampleid =0;
$is_session =0;
$emp_type ="";
$emp_id= "";
extract($_POST);
$tx='';
if(isset($close))
$tx='_closed';

$return_arr = array();
$return_arr['error'] = "";
$return_arr['html'] = "";
$return_arr['divId'] = $_POST['divId'];
if(isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] >0 && $_SESSION['employeeType'] == 1))
{
	$emp_id =  $_SESSION['employee_type_id'];
	$emp_type = $_SESSION['employeeType'] ;
	$emp_sql = ' and vendor."vendorID" ='.$emp_id;
	$is_session = 1;
}
else if(isset($_SESSION['employee_type_id']) AND ($_SESSION['employeeType'] >0 && $_SESSION['employeeType'] == 2))
{
	$emp_id =$_SESSION['employee_type_id'];
	$emp_type = $_SESSION['employeeType'] ;
	$emp_sql = ' and c."ID" ='.$emp_id;
	$is_session = 1; 
}
if(isset($_POST['id']) && $_POST['id']>0)
{
	$id = $_POST['id'];
	$pid = $_POST['pid'];	
	$sql = "Select * from tbl_prj_sample$tx where status = 1 and id = $id ";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result))
	{
		$data_sample  =$row;
	}
	pg_free_result($result);
	if($data_sample['id'] !="")
		$sampleid = $data_sample['id'];
		
		
	$sql = "Select * from tbl_prjsample_uploads$tx where status =1 and sample_id = $id";
	if(!($result=pg_query($connection,$sql))){
		print("Failed query1: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_Uploads[] =$row;
	}
	$imageArr = array();
	$fileArr = array();
	for($i = 0, $img= 0, $file = 0; $i < count($data_Uploads); $i++)
	{
		if(trim($data_Uploads[$i]['uploadtype']) == 'I')
		{
			$imageArr[$img]['id'] = $data_Uploads[$i]['uploadid'];
			$imageArr[$img++]['file'] = stripslashes($data_Uploads[$i]['filename']);
		}
		else if(trim($data_Uploads[$i]['uploadtype']) == 'F')
		{
			$fileArr[$file]['id'] = $data_Uploads[$i]['uploadid'];
			$fileArr[$file++]['file'] = stripslashes($data_Uploads[$i]['filename']);
		}
	}
	pg_free_result($result);
	
		$sql ='select notes_id, notes, created_date, e.firstname as "firstName", e.lastname as "lastName" from tbl_prjsample_notes'.$tx.' as n 
inner join "employeeDB" as e on e."employeeID" =n.created_by where sample_id='.$id.' order by notes_id;';
	if(!($result=pg_query($connection,$sql))){
		print("Failed sql: " . pg_last_error($connection));
		exit;
	}
	while($row = pg_fetch_array($result)){
		$data_notes[]=$row;
	}
}
$query1=("SELECT \"ID\", \"clientID\", \"client\", \"active\" ".
		 "FROM \"clientDB\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER BY \"client\" ASC");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}

$sql='select (Max("id")+1) as "id" from "tbl_sampleRequest" ';
if(!($result_cnt=pg_query($connection,$sql))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row_cnt = pg_fetch_array($result_cnt)){
	$data_cnt=$row_cnt;
}
if(! isset($data_cnt['id'])) { $data_cnt['id']=1; }
if(!$data_cnt['id']) { $data_cnt['id']=1;  }

$queryVendor="SELECT \"vendorID\", \"vendorName\", \"active\" ".
		 "FROM \"vendor\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER BY \"vendorName\" ASC ";
	if(!($result=pg_query($connection,$queryVendor))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_Vendr[]=$row;
} 


/* carrier values*/
$sql = "select carrier_id,carrier_name  from  tbl_carriers where status=1";
if(!($result=pg_query($connection,$sql))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row = pg_fetch_array($result)){
	$data_carrier[]=$row;
}
pg_free_result($result);	

$count = 200;
$html = '';
$html .= '
<center>
	<table id="sample_table" width="86%">
		<tr>
	  	<td valign="top" align="center">     
<table id="sample_content" width="100%" cellspacing="1" cellpadding="1" border="0">
				<tbody>
				<tr>
				  <td height="25" valign="top" align="right">Brand/Manufacture:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="brand_manufac" id="brand_manufac" value="'.htmlspecialchars(stripslashes($data_sample['brand_manufaturer'])).'"></td>
				</tr>
				  <tr>
				  <td height="25" valign="top" align="right"><font color="#FF0000">(R)</font>Sample ID:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="srID" id="srID" value="'.htmlspecialchars(stripslashes($data_sample['sample_id'])).'"></td>
				</tr>
                 <tr>
				  <td height="25" valign="top" align="right">Sample Type:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left">
                  <select name="sampletype">
                  <option value="0">----Select----</option>
                  <option value="1" ';
if($data_sample['sampletype'] == 1)
	$html .= 'selected="selected" ';
$html .= '>Stock</option>
                  <option value="2" ';
if($data_sample['sampletype'] == 2)
	$html .= 'selected="selected" ';
$html .= '>Custom</option>
                  </select></td>
				</tr>
                <tr>
				  <td height="25" valign="top" align="right">Style Number:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="sample_style" value="'.htmlspecialchars(stripslashes($data_sample['style_number'])).'" ></td>
				</tr>
				 <tr>
				  <td height="25" valign="top" align="right">Quantity:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="sample_quantity" id="srID" value="'.htmlspecialchars(stripslashes($data_sample['quantity'])).'" onchange="isNumeric(this);"></td>
				</tr>
                 <tr>
                 <td height="25" valign="top" align="right">Brief Sample Description:</td>
				  <td width="10">&nbsp;</td>
    				<td align="left"><textarea id="briefdesc" name="briefdesc" cols="30" rows="4">'.htmlspecialchars(stripslashes($data_sample['brief_desc'])).'</textarea></td>
    			</tr>
                 <tr>
                 <td height="25" valign="top" align="right">Detailed Description:</td>
				  <td width="10">&nbsp;</td>
    				<td align="left"><textarea id="detaildesc" name="detaildesc" cols="35" rows="8">'.htmlspecialchars(stripslashes($data_sample['detail_description'])).'</textarea></td>
    			</tr>
                <tr>
				  <td height="25" valign="top" align="right">Size Requested:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="sizerequest" id="size" value="'.htmlspecialchars(stripslashes($data_sample['size_requested'])).'"></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Date Needed:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" onclick="showDate(this);" name="dateneeded" id="dateneeded" value="'.$data_sample['dateneeded'].'"></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Picture:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><table width="250" cellspacing="0" cellpadding="0" border="0">
					<tbody><tr>
					  <td valign="top" align="left"><input type="file" name="file'.$count.'" id="file'.$count.'" onchange="javascript:ajaxFileUpload('.$count++.',\'I\',960,720);">
						  </td>
					</tr>
				  </tbody></table></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">File:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><table width="250" cellspacing="0" cellpadding="0" border="0">
					<tbody><tr>
					  <td valign="top" align="left"><input type="file" id="file'.$count.'" name="file'.$count.'" onchange="javascript:ajaxFileUpload('.$count++.',\'F\',960,720);" />
						</td>
					</tr>
				  </tbody></table></td>
				</tr>';
if($emp_type !=2){
	$html .= '<tr>
				  <td height="25" valign="top" align="right">Vendor</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><select name="sample_vendorID" style="width:240px">';
				   for($i=0; $i < count($data_Vendr); $i++)
				   {
					if($data_sample['vid'] == $data_Vendr[$i]['vendorID'])
						$html .= '<option value="'.$data_Vendr[$i]['vendorID'].'" selected="selected">'.$data_Vendr[$i]['vendorName'].'</option>';
					else 
						$html .= '<option value="'.$data_Vendr[$i]['vendorID'].'">'.$data_Vendr[$i]['vendorName'].'</option>';
                   }
			      $html .= '</select></td>
				  </tr>';
				}
             	$html .= '<tr>
				  <td height="25" valign="top" align="right">Send Mail to Vendor:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="checkbox" name="mailvendor_check"';
				  if($data_sample['mailvendor_check'] == "on")
					  $html .= 'checked="checked" ';
				  $html .= '></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Color:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="sample_color" value="'.htmlspecialchars(stripslashes($data_sample['sample_color'])).'" ></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Fabric: </td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="fabricType"  value="'.htmlspecialchars(stripslashes($data_sample['fabric'])).'" ></td>
				</tr>';
			if($emp_type !=2){
				$html .= '<tr>
				  <td height="25" valign="top" align="right">Cost :</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="sample_cost"  value="'.$data_sample['fabric_cost'].'" onchange="isNumeric(this);" /></td>
				</tr>';
			}

			if($emp_type !=1){
				$html .= '<tr>
				  <td height="25" valign="top" align="right">Quote Price:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="customerTargetprice" id="customerTargetprice" value="'.stripslashes($data_sample['quote_price']).'" onchange="isNumeric(this);" /></td>
				</tr>';
			}
			$html .= '<tr>
				  <td height="25" valign="top" align="right">Embroidery:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left">';
			if($data_sample['embroidery_new'] == 1){
            	$html .= '<input type="radio" checked="checked" value="1" name="embroidery" id="embroideryYes">Yes	
					<input type="radio" value="0" name="embroidery[]" id="embroideryNo">No ';
			}
			else
			{
	            $html .= '<input type="radio" value="1" name="embroidery" id="embroideryYes">
					Yes	<input type="radio"  checked="checked" value="0" name="embroidery" id="embroideryNo">	No ';
			}
            $html .= '</td>
				</tr>
                <tr>
				  <td height="25" valign="top" align="right">Silk Screening:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left">';
			if($data_sample['silkscreening'] == 1) 
			{
            	$html .= '<input type="radio" checked="checked" value="1" name="silkscreening" id="silkscreeningYes">
					Yes
					<input type="radio" value="0" name="silkscreening" id="silkscreeningNo">
					No ';
			}
			else
			{
	         	$html .= '<input type="radio" value="1" name="silkscreening" id="silkscreeningYes">
					Yes
					<input type="radio"  checked="checked" value="0" name="silkscreening" id="silkscreeningNo">
					No';
			}
            $html .= '</td>
				</tr>
                   <tr>
				  <td height="25" valign="top" align="right">Generate PO:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" readonly="readonly" value="'.stripslashes($data_sample['generate_po']).'" name="generate_po" id="generate_po" >
				      <input type="button" name="generate_po_btn[]" id="generate_po_btn" value="Generate PO" onclick="javascript:GenerateInvoice(\'generate_po\');" /></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Generate Purchase Order:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="button" name="generate_purchase_btn" id="generate_purchase_btn" value="Generate Purchase Order" onclick="javascript:load_genaratePO();" /></td>
				</tr>
                <tr>
				  <td height="25" valign="top" align="right">Customer PO:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="customerpo" id="customerpo" value="'.htmlspecialchars(stripslashes($data_sample['customer_po'])).'" ></td>
				</tr>
                <tr>
				  <td height="25" valign="top" align="right">Internal PO:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" readonly="readonly" value="'.stripslashes($data_sample['internal_po']).'" name="internalpo" id="internalpo" >
				      <input type="button" name="invoice_btn" id="invoice_btn" value="Generate Invoice" onclick="javascript:GenerateInvoice(\'internal_po\');" /></td>
				</tr>
                 <tr>
				  <td height="25" valign="top" align="right">Invoice Number:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="invoiceno" id="invoiceno" value="'. htmlspecialchars(stripslashes($data_sample['invoicenumber'])).'" ></td>
				</tr>
                <tr>
				  <td height="25" valign="top" align="right">Order/Confirmation #:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="order_confirmation"  value="'. htmlspecialchars(stripslashes($data_sample['order_confirmation'])).'"></td>
				</tr>
                
                       <tr>
				  <td height="25" valign="top" align="right">Order Placed On:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="order_on" onclick="javascript:showDate(this);" value="'.htmlspecialchars(stripslashes($data_sample['order_on'])).'"></td>
				</tr>              
                 <tr>
				  <td height="25" valign="top" align="right">Bid Number:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="bid_number"  value="'.htmlspecialchars(stripslashes($data_sample['bid_number'])).'"></td>
				</tr>
                       <tr>
				  <td height="25" valign="top" align="right">Project Budget:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="project_budget"  value="'.htmlspecialchars(stripslashes($data_sample['project_budget'])).'"></td>
				</tr>
				<tr>
				  <td height="25" valign="top" align="right">Carrier:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><select name="carrier_shipping">
                <option value="0">----- select -----</option>';
			for($index=0; $index < count($data_carrier); $index++){
				if($data_carrier[$index]['carrier_id'] == $data_sample['carrier_shipping']){
                	$html .= '<option value="'.$data_carrier[$index]['carrier_id'].'" selected="selected">'.$data_carrier[$index]['carrier_name'].'</option> ';
				}
				else{
                	$html .= '<option value="'.$data_carrier[$index]['carrier_id'].'">'.$data_carrier[$index]['carrier_name'].'</option>';
				}
			}
            $html .= '</select></td>
				</tr>';
if($emp_type !=2){
            	$html .= '<tr>
				  <td height="25" valign="top" align="right">Client Shipper Number:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="shipperno" id="shipperno" value="'.htmlspecialchars(stripslashes($data_sample['clientshipper_no'])).'" ></td>
				</tr>
             
                 <tr>
				  <td height="25" valign="top" align="right">Return Authorization:</td>
				  <td>&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="returnauth" id="returnauth" value="'.htmlspecialchars(stripslashes($data_sample['returnauthor'])).'" ></td>
				</tr> 
                <tr>
				  <td height="25" valign="top" align="right">Tracking Number:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="tracking_number" value="'.htmlspecialchars(stripslashes($data_sample['tracking_number'])).'"></td>
				</tr>
                       <tr>
				  <td height="25" valign="top" align="right">Shipped On:</td>
				  <td width="10">&nbsp;</td>
				  <td valign="top" align="left"><input type="text" class="textBox" name="shipped_on" onclick="javascript:showDate(this);" value="'.htmlspecialchars(stripslashes($data_sample['shipped_on'])).'"></td>
				</tr>
            	<tr>
				  <td height="25" valign="top" align="right">Add Notes:</td>
                  <td>&nbsp;</td>
                  <td valign="top">
					<table width="80%" border="0" cellspacing="0" cellpadding="0" id="sample_notes">
                    <tbody>
                      <tr>
                    
	<td align="left" valign="top" colspan="4"><a style="cursor:hand;cursor:pointer;" name="addNotes" id="addNotes" onClick="javascript:popOpen(\'\',\'\');"><img height="25px" width="120px"src="'.$mydirectory.'/images/addNotes.gif" alt="notes" /></a>
	</td></tr>';
if($pid){
	for($i=0; $i<count($data_notes); $i++){
		$html .= '<tr>';
			$limitNotes = substr($data_notes[$i]['notes'],0,10);
			$html .= '<td width="100px">Notes '.($i+1).': </td>
			<td>&nbsp;</td>
            <td width="150px" >'.htmlspecialchars(stripslashes($limitNotes)).'<input type="hidden" name="sample_notes_id" id="sample_notes_id" value="'.$data_notes[$i]['notes_id'].'" /></td>
			<td width="150px" ><a style="cursor:hand;cursor:pointer;" onclick="javascript:popOpen('.($i+1).',\'SAMPLE\' );">Read more...</a></td>
			<td >&nbsp;</td>
			<td ><textarea id="sampletxtAreaId'.($i+1).'" name="sample_textAreaName[]" style="display:none">'.htmlspecialchars(stripslashes($data_notes[$i]['notes'])).'</textarea>
			       <input type="hidden" id="dateTimeId'.($i+1).'" value="'.date("d-m-Y g:i A", $data_notes[$i]['created_date']).'" />
				   <input type="hidden"  id="hdn_sample_notesId'.($i+1).'" name="hdn_sample_notesName[]" value="'.$data_notes[$i]['notes_id'].'" />
				   <input type="hidden" id="empNameId'.($i+1).'" value="'.$data_notes[$i]['firstName'].' '.$data_notes[$i]['lastName']. '" /></td>
			</tr>';
	}
}    
$html .= '</tbody>
                    </table>
                    </td>
				</tr>';
}
$html .= '<tr>
                <td align="right">Send Email to Client:</td>
                <td>&nbsp;</td>
                <td align="left"><input name="is_mail" type="radio" value="1" ';
				if($data_sample['send_client_mail']==1) $html .= 'checked="checked"';
				$html .= '/>Yes &nbsp;<input name="is_mail" type="radio" value="0" ';
				if($data_sample['send_client_mail']==1){}else $html .= 'checked="checked"';
				$html .= '/>No
				<input type="hidden" id="sampleId" name="sampleId" value= "'.$sampleid.'" />
                </td>
                </tr>
			  </tbody></table>
</td>
<td valign="top">

<table id="sample_uploads" border="0" cellspacing="0" cellpadding="0">';
	for($i = 0; $i < count($data_Uploads); $i++ ){
		if($data_sample['id'] == $data_Uploads[$i]['sample_id'] ){
			if(trim($data_Uploads[$i]['uploadtype']) == 'I'){ 
				$html .= '<tr>
						<td height="25" width="250px">Image<br/>';
					$html .= '<img src="'.($upload_dir.$data_Uploads[$i]['filename']).'" width="101" height="89" onClick="PopEx(this, null,  null, 0, 0, 50, \'PopBoxImageLarge\');" />
					<a style="cursor:hand;cursor:pointer;"  class="deleteTd"  onClick="javascript:return DeleteUploads(\''.$data_Uploads[$i]['upload_id'].'\',\''.addslashes($data_Uploads[$i]['filename']).'\',\''.$pid.'\',\'\',\'sample_upload\');"><img src="'.$mydirectory.'/images/close.png" alt="delete" />
					</a> ';    
				$html .= '</td>
				</tr>';
			}
			if(trim($data_Uploads[$i]['uploadtype']) == F){
				$html .= '<tr>
				<td height="25" width="250px">File<br/>';
				$html .= '<strong>'.(substr($data_Uploads[$i]['filename'], (strpos($data_Uploads[$i]['filename'], "-")+1))).'</strong>
				<a href="download.php?file='.$data_Uploads[$i]['filename'].'"><img src="'.$mydirectory.'/images/Download.png" alt="download"/></a>
				 <a href="javascript:void(0);"  class="deleteTd"  onClick="javascript:return DeleteUploads(\''.$data_Uploads[$i]['upload_id'].'\',\''.addslashes($data_Uploads[$i]['filename']).'\',\''.$pid.'\',\'\',\'sample_upload\');"><img src="'.$mydirectory.'/images/close.png" alt="delete"/></a>';
				$html .= '</td>
				</tr>';	
			}
		}
	}
$html .= '</table>

</td>
</tr></table>';
$return_arr['html'] =$html;
echo json_encode($return_arr);
return;
?>