<?php
require('Application.php');
require($JSONLIB.'jsonwrapper.php');

if($debug == "on"){
	require('../../header.php');
	foreach($_POST as $key=>$value) {
		if($key!="submit") { echo "$key = $value<br/>"; }
	}
}

$error = "";
$msg = "";
$return_arr = array();
extract($_POST);
$return_arr['error'] = "";
$return_arr['name'] = "";
$return_arr['id'] = "";

$brand_manufac=pg_escape_string($brand_manufac);
$srID=pg_escape_string($srID);
$style=pg_escape_string($style);
$briefdesc=pg_escape_string($briefdesc);
$size=pg_escape_string($size);
$quantity=pg_escape_string($quantity);
$dateneeded=pg_escape_string($dateneeded);
$detaildesc=pg_escape_string($detaildesc);
$color = pg_escape_string(strtoupper(trim($color)));
$fabricType=pg_escape_string($fabricType);
$cost=pg_escape_string($cost);
$retailprice=pg_escape_string($retailprice);
$garment=pg_escape_string($garment);
$size_field=pg_escape_string($size_field);
$department=pg_escape_string($department);
$conveyor = pg_escape_string($conveyor);
$slot = pg_escape_string($slot);
$notes = pg_escape_string($notes);
$log_desc="";

if(isset($_POST['id'])){

	$id = $_POST['id'];
	if($srID == "")	{		
		$return_arr['name'] = "Please Enter Sample Name.";
		echo json_encode($return_arr);			
		return;
	}

	$query1 = "SELECT count(*) as n ".
		"FROM \"tbl_sample_database\" WHERE \"sample_id_val\" = '$srID' ";
	if($id >0){
		$query1 .= " AND sample_id <> $id";
	}

	if(!($result1 = pg_query($connection,$query1))){
		$return_arr['error'] ="Error while counting sample information from database!";	
		echo json_encode($return_arr);
		return;
	}
	while($row1 = pg_fetch_array($result1)){
		$data1 = $row1;
	}
	pg_free_result($result1);

	if((int)$data1['n'] >0){
		$return_arr['error'] = "Sample Name already exist";
		echo json_encode($return_arr);
		return;
	}

	$query4 = "";
	$log_module="SampleDatabase";
	if($id == 0){

		$log_desc ="New Sample Database Added";
		$query4 = "INSERT INTO tbl_sample_database (createddate, modifieddate, status ";
	
		if($brand_manufac != ""){
			$query4 .= ", brand_manufct ";
		}
	
		if($srID !=""){
			$query4 .= ", sample_id_val ";
		}
	
		if($style!=""){
			$query4 .= ", style_number ";
		}
	
		if($briefdesc !=""){
			$query4 .= ", brief_desc ";
		}
	
		if($size !=""){
			$query4 .= ", size ";
		}

		if($quantity !=""){
			$query4 .= ", quantity ";
		}

		if($detaildesc !=""){
			$query4 .= ", detail_description ";
		}

		if($vendorID != ""){
			$query4 .= ", vid ";
		}

		if($color!=""){
			$query4 .= ", color ";
		}
		
		if($fabricType!=""){
			$query4 .= ", fabric";
		}

		if($cost!=""){
			$query4 .= ", samplecost ";
		}

		if($retailprice != ""){ 
			$query4 .= ", retailprice ";
		}
		
		if($inStock!=""){
			$query4.=", instock ";
		}
		
		if($embroidery != ""){
			$query4 .= ", embroidery_new ";
		}
		
		if($silkscreening!=""){
			$query4.=", silkscreening ";
		}
		
		if($clientname!=""){
			$query4.=", client ";
		}
		
		if($location!=""){
			$query4.=", location ";
		}
		
		if($garment!=""){ 
			$query4.=", garment ";
		}
		
		if($size_field!=""){ 
			$query4.=", size_field ";
		}
		
		if($department!=""){ 
			$query4.=", department ";
		}
		
		if($conveyor != ""){ 
			$query4 .= ", conveyor ";
		}
		
		if($slot != ""){ 
			$query4 .= ", slot ";
		}
		
		if($notes != ""){ 
			$query4 .= ", notes ";
		}
		
		if($sample_type > 0){ 
			$query4 .= ", sample_type_id ";
		}
		
		$query4.=")";

		$query4.=" VALUES ('".date('U')."','".date('U')."','1'";
		
		if($brand_manufac !=""){
			$query4.=" ,'$brand_manufac' ";
		}

		if($srID){
			$query4.=" ,'$srID' ";
		}
		if($style){ 
			$query4.=" ,'$style' ";
		}
		if($briefdesc !=""){
			$query4.=" ,'$briefdesc'";
		}
		if($size !=""){
			$query4.=" ,'$size'";
		}
		if($quantity !=""){
			$query4.=" ,'$quantity'";
		}
		if($detaildesc !=""){
			$query4.=" ,'$detaildesc'";
		}
		if($vendorID!=""){
			$query4.=" ,'$vendorID' "; // martin
		}
		if($color != ""){
			$query4.=" ,'".strtoupper($color)."' ";
		}
		if($fabricType != ""){
			$query4 .= " ,'$fabricType' ";
		}
		if($cost != ""){
			$query4 .= " ,'$cost' ";
		}
		if($retailprice != ""){
			$query4 .= " ,'$retailprice' ";
		}
		if($inStock != ""){
			$query4 .= " ,'$inStock' ";
		}
		if($embroidery != ""){
			$query4 .= " ,'$embroidery' ";
		}
		if($silkscreening!=""){
			$query4.=" ,'$silkscreening' ";
		}
		if($clientname != ""){
			$query4 .= " ,'$clientname' ";
		}
		if($location!=""){
			$query4.=" ,'$location' ";
		}
		if($garment!=""){
			$query4.=" ,'$garment' ";
		}
		if($size_field!=""){
			$query4.=" ,'$size_field' ";
		}
		if($department!=""){
			$query4.=" ,'$department' ";
		}
		
		if($conveyor != ""){
			$query4 .= " ,'$conveyor' ";
		}

		if($slot != ""){
			$query4 .= " ,'$slot' ";
		}

		if($notes != ""){
			$query4 .= " ,'$notes' ";
		}

		if($sample_type > 0){
			$query4 .= " ,$sample_type ";
		}
		$query4.=")";
		
		if(!($result=pg_query($connection,$query4))){
			$return_arr['error'] ="Error while storing sample request information to database! ".pg_last_error($connection)."<br><br>$query4";	
			echo json_encode($return_arr);
			return;
		}
		pg_free_result($result);
		
		$sql="Select sample_id from tbl_sample_database where sample_id_val='".$srID."'";
		if(!($result_sql=pg_query($connection,$sql))){
			$return_arr['error'] ="Error while getting sample request information from database! sql";	
			echo json_encode($return_arr);
			return;
		}
		$row=pg_fetch_array($result_sql);
		 $return_arr['id'] = $row['sample_id'];
		 $id = $return_arr['id'];
		 $log_id = $id;
		 pg_free_result($result_sql);
 
	}else if($id > 0){

		$log_desc ="Sample Database Updated";
		$query4="UPDATE tbl_sample_database set status = 1,";
		if($brand_manufac!= ""){
			$query4.="brand_manufct  = '$brand_manufac', ";	
		}else{  
			$query4.="brand_manufct  = null, ";	
		}

		if($srID!= ""){
			$query4.="sample_id_val = '$srID', ";
		}else{
			$query4.="sample_id_val = null, ";
		}

		if($style!=""){
			$query4.="style_number = '$style', ";
		}else{
			$query4.="style_number = null, ";
		}

		if($briefdesc !=""){
			$query4.="brief_desc = '$briefdesc', ";
		}else{
			$query4.="brief_desc = null, ";
		}

		if($size!=""){
			$query4.="size = '$size', "; 
		}else{
			$query4.="size = null, ";
		}

		if($quantity!=""){
			$query4.="quantity = '$quantity', "; 
		}else{
			$query4.="quantity = null, "; 
		}

		if($detaildesc !=""){
			$query4.="detail_description = '$detaildesc', "; 
		}else{
			$query4.="detail_description = null, "; 
		}

		if($vendorID >0){
			$query4.="vid = '$vendorID', ";
		}else{
			$query4.="vid = 0, ";
		}

		if($color != ""){
			$query4 .= "color = '".strtoupper($color)."', ";
		}else{
			$query4.="color = null, ";
		}

		if($fabricType!=""){
			$query4.="fabric = '$fabricType', ";
		}else{
			$query4.="fabric = null, ";
		}

		if($cost!=""){
			$query4.="samplecost = '$cost', ";
		}else{
			$query4.="samplecost = null, ";
		}

		if($retailprice!=""){
			$query4.="retailprice = '$retailprice', ";
		}else{
			$query4.="retailprice = null, ";
		}

		if($inStock!=""){
			$query4.="instock = '$inStock', ";
		}

		if($embroidery!=""){
			$query4.="embroidery_new = '$embroidery', ";
		}

		if($clientname!=""){
			$query4.="client = '$clientname', ";
		}else{
			$query4.="client = null, ";
		}

		if($garment!=""){
			$query4.="garment = '$garment', ";
		}else{
			$query4.="garment = null, ";
		}

		if($size_field!=""){
			$query4.="size_field = '$size_field', ";
		}else{
			$query4.="size_field = null, ";
		}

		if($department!=""){
			$query4.="department = '$department', ";
		}else{ 
			$query4.="department = null, ";
		}

		if($conveyor != ""){
			$query4 .= "conveyor = '$conveyor', ";
		}else{ 
			$query4 .= "conveyor = null, ";
		}

		if($slot != ""){
			$query4 .= "slot = '$slot', ";
		}else{ 
			$query4 .= "slot = null, ";
		}

		if($notes != ""){
			$query4 .= "notes = '$notes', ";
		}else{ 
			$query4 .= "notes = null, ";
		}

		if($location!=""){
			$query4.="location = '$location', ";
		}else{
			$query4.="location = null, ";
		}

		if($silkscreening!=""){
			$query4.=" silkscreening = '$silkscreening', ";
		}

		if($sample_type>0){
			$query4.=" sample_type_id = '$sample_type', ";
		}

		$query4.= " modifieddate = '".date('U')."' ";
		$query4.= " WHERE sample_id = $id";
		if($query4 !=""){
			if(!($result=pg_query($connection,$query4))){
				$return_arr['error'] = pg_last_error($connection);
				echo json_encode($return_arr);
				return;
			}
			pg_free_result($result);
		}
		$return_arr['id'] = $id;
		$log_id = $id;
	}
}

if($return_arr['error'] ==""){
	if($vendorID){
		$sql = 'select "vendorName",address from vendor where "vendorID" = '.$vendorID;
		if(!($result=pg_query($connection,$sql))){
			$return_arr['error'] = pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		}
		$sql = "";
		$vendorName = "";
		while($row = pg_fetch_array($result)){
			$vendorName=$row;
		}
		pg_free_result($result);
	}
	$sql = "select upload_id,filename,uploadtype from tbl_sample_database_uploads where sample_id = $id";
	if(!($result=pg_query($connection,$sql))){
		$return_arr['error'] = pg_last_error($connection);
		echo json_encode($return_arr);
		return;
	}
	while($row = pg_fetch_array($result)){
		$data_Uploads[] =$row;
	}
	$imageArr = array();
	$fileArr = array();
	for($i = 0, $img= 0, $file = 0; $i < count($data_Uploads); $i++){
		if(trim($data_Uploads[$i]['uploadtype']) == 'I'){
			$imageArr[$img]['id'] = $data_Uploads[$i]['upload_id'];
			$imageArr[$img++]['file'] = stripslashes($data_Uploads[$i]['filename']);
		}else if(trim($data_Uploads[$i]['uploadtype']) == 'F'){
			$fileArr[$file]['id'] = $data_Uploads[$i]['upload_id'];
			$fileArr[$file++]['file'] = stripslashes($data_Uploads[$i]['filename']);
		}
	}
	pg_free_result($result);
	if($log_desc!=""){

		$sql="INSERT INTO tbl_change_record (";
		$sql.=" log_date ";
		if($log_desc!=""){
			$sql.=", log_desc ";
		}

		if($log_module!=""){
			$sql.=", module ";
		}

		if($log_id!=""){
			$sql.=", module_id ";
		}
		$sql.=", status ";
		$sql.=", created_date ";
		$sql.=", employee_id ";	
		$sql.=")";
		$sql.=" VALUES (";				   
		$sql.=" ".date('U');
		if($log_desc!=""){
			$sql.=", '".$log_desc."' ";
		}

		if($log_module!=""){
			$sql.=", '".$log_module."' ";
		}

		if($log_id!=""){
			$sql.=", $log_id ";
		}
		$sql.=" ,1 ";
		$sql.=" ,".date('U');
		$sql.=" ,".$_SESSION["employeeID"];
		$sql.=" )".";";
		if(!($result=pg_query($connection,$sql))){
			$return_arr['error'] = "Basic tab :".pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		}
		pg_free_result($result);		
		$sql ="";
		$log_desc ="";
	}
	$mailBody1 = '<center>'.
			  '<strong>Sample Request form </strong>'.
			  '<br>'.
'<table>'.
'<tr>'.
'<td>'.
'<table width="500px" border="0">'.
				'<tr>'.
				    '<td align="right" >Choose Client:</td>'.
				    '<td width="10" >&nbsp;</td>'.
				    '<td align="left" >'.$clientName['client'].'</td></tr>'.
                  '<tr>'.
				  '<td align="right" >Brand/Manufacture:</td>'.
				  '<td width="10" >&nbsp;</td>'.
				  '<td align="left" >'.$brand_manufac.'</td>'.
				'</tr>'.
				  '<tr>'.
				  '<td align="right" >Sample ID:</td>'.
				  '<td width="10" >&nbsp;</td>'.
				  '<td align="left" >'.$srID.'</td>'.
				'</tr>'.
                '<tr>'.
				  '<td align="right" >Style Number:</td>'.
				  '<td width="10" >&nbsp;</td>'.
				  '<td align="left" >'.$style.'</td>'.
				'</tr>'.
                 '<tr>'.
                 '<td align="right" >Brief Sample Description:</td>'.
				  '<td width="10" >&nbsp;</td>'.
    				'<td align="left" >'.$briefdesc.'</td></tr>'.
               ' <tr>'.
				  '<td align="right" >Size Requested:</td>'.
				  '<td width="10" >&nbsp;</td>'.
				  '<td align="left" >'.$sizerequest.'</td>'.
				'</tr>'.
				'<tr>'.
				  '<td align="right" >Date Needed:</td>'.
				  '<td width="10" >&nbsp;</td>'.
				  '<td align="left" >'.$dateneeded.'</td>'.
				'</tr>'.
				 '<tr>'.
                '<td align="right" >Detailed Description:</td>'.
				  '<td width="10" >&nbsp;</td>'.
    				'<td align="left" >'.$detaildesc.'</td>'.
    			'</tr>'.                         
				'<tr>'.
				  '<td align="right" >Vendor:</td>'.
				  '<td width="10" >&nbsp;</td>'.
				  '<td align="left" >'.$vendorName['vendorName'].'</td>'.
				  '</tr>'.
				  '<tr>'.
				  '<td align="right" >Vendor Address:</td>'.
				  '<td width="10" >&nbsp;</td>'.
				  '<td align="left" >'.$vendorName['address'].'</td>'.
				  '</tr>'.
				'<tr>'.
				  '<td align="right" >Color:</td>'.
				 '<td width="10" >&nbsp;</td>'.
				  '<td align="left" >'.$color.'</td>'.
				'</tr>'.
				'<tr>'.
				  '<td align="right">Fabric:</td>'.
				  '<td width="10">&nbsp;</td>'.
				  '<td align="left">'.$fabricType.'</td>'.
				'</tr>';
				$mailBody2.='<tr>'.
				  '<td align="right" >Customer Target Price:</td>'.
				  '<td width="10" >&nbsp;</td>'.
				  '<td align="left" >'.$customerTargetprice.'</td>'.
				'</tr>'.
				'<tr>'.
				  '<td align="right" >In Stock:</td>'.
				  '<td width="10" >&nbsp;</td>'.
                  '<td align="left" >';
				if($inStock == 1){
					$mailBody2.='Yes'; 
				}else{
					$mailBody2.='No';
				}
				$mailBody2 .='</td>'.
				'</tr>'.
                '<tr>'.
				  '<td align="right" >Embroidery:</td>'.
				  '<td width="10" >&nbsp;</td>'.
				  '<td align="left" >';
				if($embroidery == 1){
					$mailBody2.='Yes';
				}else{
					$mailBody2 .= 'No';
				}
	    			$mailBody2 .='</td>'.
				'</tr>'.
                '<tr>'.
				  '<td align="right" >Silk Screening:</td>'.
				  '<td width="10" >&nbsp;</td>'.
				  '<td align="left" >';
				if($silkscreening == 1){
					$mailBody2 .='Yes'; 
				}else{
					$mailBody2 .='No';
				}
                    $mailBody2 .='</td>'.
				'</tr>'.
                '<tr>'.
				  '<td align="right" >Customer PO:</td>'.
				  '<td width="10" >&nbsp;</td>'.
				  '<td align="left" >'.$customerpo.'</td>'.
				'</tr>'.
                '<tr>'.
				  '<td align="right" >Internal PO:</td>'.
				 '<td width="10" >&nbsp;</td>'.
				  '<td align="left" >'.$internalpo.'</td>'.
				'</tr>'.
                 '<tr>'.
				  '<td align="right" >Invoice Number:</td>'.
				  '<td width="10" >&nbsp;</td>'.
				  '<td align="left" >'.$invoiceno.'</td>'.
				'</tr>'.
                 '<tr>'.
				  '<td align="right" >Client Shipper Number:</td>'.
				  '<td width="10" >&nbsp;</td>'.
				  '<td align="left" >'.$shipperno.'</td>'.
				'</tr>'.
                '<tr>'.
				  '<td align="right" >Return Authorization:</td>'.
				  '<td width="10" >&nbsp;</td>'.
				  '<td align="left" >'.$returnauth.'</td>'.
				'</tr>'.
				'</table>'.
				'</td>'.
				'<td>'.
				'<table>';
				if(count($imageArr)){
					for($i=0; $i<count($imageArr); $i++){
						$mailBody2.='<tr>'.
							'<td >Image</td>'.
							'</tr>'.
							'<tr>'.
							'<td >';
						if($imageArr[$i] != ""){   
							$mailBody2.='<img src="'.($_SESSION['HOME_URL'].'/projectimages/'.$imageArr[$i]['file']).'" width="101" height="89" id="thumb_image3">';
						}
						$mailBody2.='</td>'.
							'</tr>';
					}
				}
				$mailBody2.='</table>'.
				'</td>'.
				'</tr>'.
				'</table></center>';
}

echo json_encode($return_arr);
return;
?>
