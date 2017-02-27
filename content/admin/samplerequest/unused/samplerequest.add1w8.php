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
$sizerequest=pg_escape_string($sizerequest);
$dateneeded=pg_escape_string($dateneeded);
$detaildesc=pg_escape_string($detaildesc);
$color=pg_escape_string($color);
$fabricType=pg_escape_string($fabricType);
$cost=pg_escape_string($cost);
$customerTargetprice=pg_escape_string($customerTargetprice);
$customerpo=pg_escape_string($customerpo);
$internalpo=pg_escape_string($internalpo);
$invoiceno=pg_escape_string($invoiceno);
$returnauth=pg_escape_string($returnauth);
$shipperno=pg_escape_string($shipperno);

$sample_name=pg_escape_string($sample_name);
$tracking_number=pg_escape_string($tracking_number);

if(isset($_POST['id']))
{
	$id = $_POST['id'];
	if($srID == "")
	{		
		$return_arr['name'] = "Please Enter Sample Id.";
		echo json_encode($return_arr);			
		return;
	}
	$sql="Select count(*) as n from \"tbl_sampleRequest\" where \"srID\"='$srID'";
	if($id >0)
	$sql .= " and id <> $id";
	if(!($result=pg_query($connection,$sql))){
		$return_arr['error'] ="Error while counting sample information from database!";	
		echo json_encode($return_arr);
		return;
	}
	$sql = "";
	$sampleCount = "";
	while($row = pg_fetch_array($result))
	{
		$sampleCount=$row;
	}
	pg_free_result($result);
	if((int)$sampleCount['n'] >0)
	{
		$return_arr['error'] = "Sample Id already exist";
		echo json_encode($return_arr);
		return;
	}
	
	$query4 = "";
	
	if($id == 0)
	{
		$query4="INSERT INTO \"tbl_sampleRequest\" (\"createdDate\", \"modifiedDate\", \"status\" ";
		if($srID !="") $query4.=", \"srID\" ";
		if($brand_manufac !="") $query4.=", brand_manufct ";
		if($style!="") $query4.=", \"styleNo\" ";
		if($briefdesc !="") $query4.=", brief_desc ";
		if($sizerequest!="") $query4.=", \"size\" ";
		if($dateneeded!="") $query4.=", dateneeded ";
		if($detaildesc !="") $query4.=", detail_description ";
		if($color!="") $query4.=", \"color\" ";
		if($fabricType!="") $query4.=", \"fabricType\" ";
		if($cost!="") $query4.=", \"cost\" ";
		if($customerTargetprice!="") $query4.=", \"customerTargetprice\" ";
		if($inStock!="") $query4.=", \"inStock\" ";
		if($embroidery!="") $query4.=", embroidery_new ";
		if($silkscreening!="") $query4.=", silkscreening ";
		if($customerpo!="") $query4.=", customer_po ";
		if($internalpo!="") $query4.=", internal_po ";
		if($invoiceno!="") $query4.=", invoicenumber ";
		if($shipperno!="") $query4.=", clientshipper_no ";
		if($returnauth!="") $query4.=", returnauthor ";
		if($sampletype!="") $query4.=", sampletype ";
		if($sample_name!="") $query4.=", sample_name ";
		if($ordered_date!="") $query4.=", ordered_date ";
		if($tracking_number!="") $query4.=", tracking_number ";
		if($generate_po!="") $query4.=", po_sequence ";
		if($mailvendor_check!="") $query4.=", mailvendor_check ";
		if($clientID) $query4.=", \"cid\" ";
		if($vendorID) $query4.=",\"vid\" ";
		$query4.=")";
		$query4.=" VALUES ('".date('U')."','".date('U')."','1'";
		if($srID) $query4.=" ,'$srID' ";
		if($brand_manufac !="") $query4.=" ,'$brand_manufac' ";
		if($style) $query4.=" ,'$style' ";
		if($briefdesc !="") $query4.=" ,'$briefdesc'";
		if($sizerequest!="") $query4.=" ,'$sizerequest' ";
		if($dateneeded!="") $query4.=", '$dateneeded'";
		if($detaildesc !="") $query4.=" ,'$detaildesc'";
		if($color!="") $query4.=" ,'$color' ";
		if($fabricType!="") $query4.=" ,'$fabricType' ";
		if($cost!="") $query4.=" ,'$cost' ";
		if($customerTargetprice!="") $query4.=" ,'$customerTargetprice' ";
		if($inStock!="") $query4.=" ,'$inStock' ";
		if($embroidery!="") $query4.=" ,'$embroidery' ";
		if($silkscreening!="") $query4.=" ,'$silkscreening' ";
		if($customerpo!="") $query4.=", '$customerpo' ";
		if($internalpo!="") $query4.=", '$internalpo' ";
		if($invoiceno!="") $query4.=", '$invoiceno' ";
		if($shipperno!="") $query4.=", '$shipperno' ";
		if($returnauth!="") $query4.=", '$returnauth' ";
		if($sampletype!="") $query4.=", '$sampletype' ";
		if($sample_name!="") $query4.=", '$sample_name' ";
		if($ordered_date!="") $query4.=", ".date($ordered_date);
		if($tracking_number!="") $query4.=", '$tracking_number' ";
		if($generate_po!="") $query4.=", '$generate_po' ";
		if($mailvendor_check!="") $query4.=", '$mailvendor_check' ";
		if($clientID!="") $query4.=" ,'$clientID' ";
		if($vendorID!="") $query4.=" ,'$vendorID' ";
		$query4.=")";
		if(!($result=pg_query($connection,$query4))){
			$return_arr['error'] ="Error while storing sample request information to database!";	
			echo json_encode($return_arr);
			return;
		}
		pg_free_result($result);
		$sql="Select id from \"tbl_sampleRequest\" where \"srID\"='".$srID."'";
		if(!($result_sql=pg_query($connection,$sql))){
			$return_arr['error'] ="Error while getting sample request information from database!";	
			echo json_encode($return_arr);
			return;
		}
		$row=pg_fetch_array($result_sql);
		 $return_arr['id'] = $row['id'];
		 $id = $return_arr['id'];
		pg_free_result($result_sql);		
	}
	else if($id > 0)
	{
		$query4="UPDATE \"tbl_sampleRequest\" set \"cid\" = '$clientID',";		 	
		if($srID!= "") $query4.="\"srID\" = '$srID', ";
		if($brand_manufac!= "") $query4.="brand_manufct  = '$brand_manufac', ";
		if($style!="") $query4.="\"styleNo\" = '$style', ";
		if($briefdesc !="")  $query4.="brief_desc = '$briefdesc', ";
		if($sizerequest!="")   $query4.="size = '$sizerequest', "; 
		if($dateneeded!="")   $query4.="dateneeded = '$dateneeded', "; 
		if($detaildesc !="") $query4.="detail_description = '$detaildesc', "; 
		if($vendorID) $query4.="\"vid\" = '$vendorID', ";
		if($color!="") $query4.="\"color\" = '$color', ";
		if($fabricType!="") $query4.="\"fabricType\" = '$fabricType', ";
		if($cost!="") $query4.="\"cost\" = '$cost', ";
		if($customerTargetprice!="") $query4.="\"customerTargetprice\" = '$customerTargetprice', ";
		if($inStock!="")  $query4.="\"inStock\" = '$inStock', ";
		if($embroidery!="") $query4.="embroidery_new = '$embroidery', ";
		if($silkscreening!="") $query4.=" silkscreening = '$silkscreening', ";
		if($customerpo)  $query4.="customer_po = '$customerpo', ";
		if($internalpo) $query4.="internal_po = '$internalpo', ";
		if($invoiceno!="") $query4.=" invoicenumber = '$invoiceno', ";
		if($shipperno!="") $query4.=" clientshipper_no = '$shipperno', ";
		if($returnauth!="") $query4.="returnauthor = '$returnauth', ";
		if($sampletype!="")$query4.="sampletype = '$sampletype', ";
		if($sampletype!="")$query4.="sample_name = '$sample_name', ";
		else $query4.="sample_name = null, ";
		if($ordered_date!="")$query4.="ordered_date = ".strtotime($ordered_date).",";
		else $query4.="ordered_date = null, ";
		if($tracking_number!="")$query4.="tracking_number = '$tracking_number', ";
		else $query4.="tracking_number = null, ";
		if($generate_po!="")$query4.="po_sequence = '$generate_po', ";
		else $query4.="po_sequence = null, ";
		if($mailvendor_check!="")$query4.="mailvendor_check = '$mailvendor_check', ";
		$query4.= " \"modifiedDate\" = '".date('U')."' ";
		$query4.= " WHERE id = $id";
		if($query4 !="")
		{
			if(!($result=pg_query($connection,$query4)))
			{
				$return_arr['error'] = "Error while updating sample information to database!".pg_last_error($connection);
				echo json_encode($return_arr);
				return;
			}
			pg_free_result($result);
		}
		$return_arr['id'] = $id;
	}
	if($id >0)
	{
		for($i=0; $i<count($textAreaName); $i++)
		{
			if($hdnNotesName[$i] == 0 && $textAreaName[$i] !="")
			{
				
				$sql="Insert into \"tbl_sampleNotes\" (";
				if($textAreaName[$i]!="") $sql.="notes ,";
				$sql.=" \"sampleId\"" ;
				$sql .=", \"createdDate\"";
				$sql .=", \"createdBy\"";
				$sql .=" )Values(";
				if($textAreaName!="") $sql .=" '".pg_escape_string($textAreaName[$i])."',";
				$sql .=" '".$id."'";
				$sql .=", ".date("U");
				$sql .=", ".$_SESSION["employeeID"]."";
				$sql .=" );";
				if(!($result=pg_query($connection,$sql)))
				{
					$return_arr['error'] = "Error while insertin information from samplenotes database!".pg_last_error($connection);
					echo json_encode($return_arr);
					return;
				}
				pg_free_result($result);
			}
		}	
	}
}
if($return_arr['error'] =="")
{
	if($clientID !="")
	{
		$sql = 'select client,email from "clientDB" where "ID" = '.$clientID;
		if(!($result=pg_query($connection,$sql)))
			{
				$return_arr['error'] = "Error while getting client information from database!".pg_last_error($connection);
				echo json_encode($return_arr);
				return;
			}
		$sql = "";
		$clientName = "";
		while($row = pg_fetch_array($result))
		{
			$clientName=$row;
		}
		$clientEmail = $clientName['email'];
		pg_free_result($result);
	}
	if($vendorID)
	{
		$sql = 'select "vendorName",address,email from vendor where "vendorID" = '.$vendorID;
		if(!($result=pg_query($connection,$sql)))
		{
			$return_arr['error'] = "Error while getting vendor information from database!".pg_last_error($connection);
			echo json_encode($return_arr);
			return;
		}
		$sql = "";
		$vendorName = "";
		while($row = pg_fetch_array($result))
		{
			$vendorName=$row;
		}
		$vendor_email = $vendorName['email'];
		pg_free_result($result);
	}
	$sql = "select uploadid,filename,uploadtype from tbl_sample_uploads where sampleid = $id";
	if(!($result=pg_query($connection,$sql)))
	{
		$return_arr['error'] = "Error while getting information from sample_uploads database!".pg_last_error($connection);
		echo json_encode($return_arr);
		return;
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
				  '<td align="right" >Sample Type:</td>'.
				  '<td width="10" >&nbsp;</td>'.
				  '<td align="left" >'.$sampletype.'</td>'.
				'</tr>'.
				 '<tr>'.
				  '<td align="right" >Sample Name:</td>'.
				  '<td width="10" >&nbsp;</td>'.
				  '<td align="left" >'.$sample_name.'</td>'.
				'</tr>'.
				 '<tr>'.
				  '<td align="right" >Ordered Date:</td>'.
				  '<td width="10" >&nbsp;</td>'.
				  '<td align="left" >'.$ordered_date.'</td></tr>'.
				 '<tr>'.
				  '<td align="right" >Tracking Number:</td>'.
				  '<td width="10" >&nbsp;</td>'.
				  '<td align="left" >'.$tracking_number.'</td>'.
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
				  '<td align="right" >Generate Purchase Order:</td>'.
				  '<td width="10" >&nbsp;</td>'.
				  '<td align="left" >'.$generate_po.'</td>'.
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
					if($inStock == 1) 
					{
						$mailBody2.='Yes'; 
                    }
					else
					{
						$mailBody2.='No';
					}
				 $mailBody2 .='</td>'.
				'</tr>'.
                '<tr>'.
				  '<td align="right" >Embroidery:</td>'.
				  '<td width="10" >&nbsp;</td>'.
				  '<td align="left" >';
				if($embroidery == 1) 
				{
                  $mailBody2.='Yes';
				}
				else
				{
                   $mailBody2 .= 'No';
				}
                    $mailBody2 .='</td>'.
				'</tr>'.
                '<tr>'.
				  '<td align="right" >Silk Screening:</td>'.
				  '<td width="10" >&nbsp;</td>'.
				  '<td align="left" >';
				if($silkscreening == 1) 
				{
                	$mailBody2 .='Yes'; 
				}
				else
				{
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
				if(count($imageArr))
				{
					for($i=0; $i<count($imageArr); $i++)
					{
                  $mailBody2.='<tr>'.
                    '<td >Image</td>'.
                  '</tr>'.
    			 '<tr>'.
                   '<td >';
					if($imageArr[$i] != "")
					{   
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
				if($isMailServer == 'true')
				{
					require('../../mail.php');
					
					$mail       = new PHPMailer();	
					
					$mail->AddReplyTo("Do Not Reply", $name = "DO NOT REPLY");
			
					$mail->From       = "admin@uniformsourcing.com";
					
					$mail->FromName   = "";
					
					$mail->Subject    = "Sample Request - ".$srID;
					
					$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test					
				
					
					if($is_mail == 1 && $clientEmail !="")
					{
						$mail->MsgHTML($mailBody1.$mailBody2);	
						
						$mail->to[0][0]=$clientEmail;
						$mail->to[0][1]='';
						
						if(!$mail->Send())
						{			
							$return_arr['error'] = "Unable to send email. Please try again later";
							echo json_encode($return_arr);
							return;
						}
						
					}
					if($mailvendor_check == 'on' && $generate_po !="")
					{
						$mail->MsgHTML($mailBody1.$mailBody2);	
						
						$mail->to[0][0]=$vendor_email;
						$mail->to[0][1]='';
						
						if(!$mail->Send())
						{			
							$return_arr['error'] = "Unable to send email. Please try again later";
							echo json_encode($return_arr);
							return;
						}
					}
					if($sampletype == 'Stock')
					{
						$mail->to[0][0]="stock@uniforms.net";
						$mail->to[0][1]='';
						
						if(!$mail->Send())
						{			
							$return_arr['error'] = "Unable to send email. Please try again later";
							echo json_encode($return_arr);
							return;
						}
					}
					if($sampletype == 'Custom')
					{
						$mail->to[0][0]="custom@uniforms.net";
						$mail->to[0][1]='';
						
						if(!$mail->Send())
						{			
							$return_arr['error'] = "Unable to send email. Please try again later";
							echo json_encode($return_arr);
							return;
						}
					}
					$mail->MsgHTML($mailBody1.'<tr>'.'<td align="right" >Cost:</td>'.'<td width="10" >&nbsp;</td>'.'<td align="left" >'.$cost.'</td>'.
				'</tr>'.$mailBody2);
					
					$mail->to[0][0]=$account_mailid;
					$mail->to[0][1]='';
					
					if(!$mail->Send())
					{			
						$return_arr['error'] = "Unable to send email. Please try again later";
						echo json_encode($return_arr);
						return;
					}
				}
				else
				{
					require($PHPLIBDIR.'mailfunctions.php');
					if($is_mail ==1 && $clientEmail !="")
					{
						$headers=create_smtp_headers("Sample Request".$srID, "admin@uniformsourcing.com", $clientEmail, "Sample Request -".$srID,"","text/html");
						$data=$headers. "<html>".$mailBody1.$mailBody2."</html>";
						if((send_smtp($mailServerAddress,"admin@uniformsourcing.com",$clientEmail, $data)) == false)
						{
							$return_arr['error'] = "Unable to send email. Please try again later";
							echo json_encode($return_arr);
							return;
						
						}
					}
					if($mailvendor_check == 'on' && $generate_po !="")
					{
						$headers=create_smtp_headers("Sample Request".$srID, "admin@uniformsourcing.com", $vendor_email, "Sample Request -".$srID,"","text/html");
						$data=$headers. "<html>".$mailBody1.$mailBody2."</html>";
						if((send_smtp($mailServerAddress,"admin@uniformsourcing.com",$vendor_email, $data)) == false)
						{
							$return_arr['error'] = "Unable to send email. Please try again later";
							echo json_encode($return_arr);
							return;
						
						}
					}
					if($sampletype == 'Stock')
					{
						$headers=create_smtp_headers("Sample Request".$srID, "admin@uniformsourcing.com", "stock@uniforms.net", "Sample Request -".$srID,"","text/html");
						$data=$headers. "<html>".$mailBody1.$mailBody2."</html>";
						if((send_smtp($mailServerAddress,"admin@uniformsourcing.com","stock@uniforms.net", $data)) == false)
						{
							$return_arr['error'] = "Unable to send email. Please try again later";
							echo json_encode($return_arr);
							return;
						
						}
					}
					if($sampletype == 'Custom')
					{
						$headers=create_smtp_headers("Sample Request".$srID, "admin@uniformsourcing.com", "custom@uniforms.net", "Sample Request -".$srID,"","text/html");
						$data=$headers. "<html>".$mailBody1.$mailBody2."</html>";
						if((send_smtp($mailServerAddress,"admin@uniformsourcing.com","custom@uniforms.net", $data)) == false)
						{
							$return_arr['error'] = "Unable to send email. Please try again later";
							echo json_encode($return_arr);
							return;
						
						}
					}
					$headers=create_smtp_headers("Sample Request".$srID, "admin@uniformsourcing.com", $account_emailid, "Sample Request -".$srID,"","text/html");
					$data=$headers. "<html>".$mailBody1.'<tr>'.'<td align="right" >Cost:</td>'.'<td width="10" >&nbsp;</td>'.'<td align="left" >'.$cost.'</td>'.
				'</tr>'.$mailBody2."</html>";
					if((send_smtp($mailServerAddress,"admin@uniformsourcing.com",$account_mailid, $data)) == false)
					{
						$return_arr['error'] = "Unable to send email. Please try again later";
						echo json_encode($return_arr);
						return;
					
					}
				}
				
	}	
		
echo json_encode($return_arr);
return;
?>